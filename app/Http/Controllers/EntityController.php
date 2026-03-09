<?php

namespace App\Http\Controllers;

use App\Imports\EsdImport;
use App\Models\Entity;
use App\Models\Item;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Http;
use ZipArchive;
use Illuminate\Support\Facades\File;

class EntityController extends Controller
{

    public function index()
    {
        $entities = Entity::all();
        //return response()->json($entities);
        return view('admin.entities.index', compact('entities'));
    }

    public function preview($code) {
        // Cari berdasarkan kolom code, bukan find()
        $entity = Entity::where('code', $code)->firstOrFail();
        return view('public.preview', compact('entity'));
    }

    public function proxyAwork(Request $request)
    {
        try {
            $apiUrl = env('API_BASE_URL', 'http://localhost:1411');
            $apiToken = env('API_KEY');

            $response = Http::withToken($apiToken)
                ->withHeaders(['Accept' => 'application/json'])
                ->get($apiUrl . '/api/v1/users', [
                    'search' => $request->search
                ]);

            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function downloadQR($id)
    {
        $entity = Entity::findOrFail($id);
        
        $qrCode = QrCode::format('svg') 
                        ->size(300)
                        ->margin(1)
                        ->generate(url('/preview/' . $entity->id));

        $filename = 'QR_' . $entity->npk . '.svg';
        
        return response($qrCode)
                ->header('Content-Type', 'image/svg+xml')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function downloadAllQR()
    {
        // Eager loading relasi codeEsd agar tidak lambat (N+1 query)
        $entities = Entity::with('codeEsd')->get();

        if ($entities->isEmpty()) {
            return back()->with('error', 'Tidak ada data untuk didownload.');
        }

        $zipFileName = 'QR_Codes_Inventory_' . date('Y-m-d') . '.zip';
        $zipPath = storage_path($zipFileName);
        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {

            foreach ($entities as $entity) {
                /** * 1. URL Best Practice: Menggunakan 'code' bukan 'id'
                 * Contoh: domain.com/preview/ENT-2026-0169
                 */
                $qrUrl = url('/preview/' . $entity->code);

                $qrCode = QrCode::format('svg')
                    ->size(300)
                    ->margin(1)
                    ->generate($qrUrl);

                /** * 2. Tentukan Nama Folder berdasarkan Nama CODE_ESD (ATS, ATM, BJS, dll)
                 */
                $folderName = $entity->codeEsd ? $entity->codeEsd->name : 'UNCATEGORIZED';

                /** * 3. Tentukan Nama File 
                 * Jika NPK ada, gunakan NPK. Jika stok AVAILABLE (NPK null), gunakan kode uniknya.
                 */
                $fileIdentifier = $entity->code;
                $fileName = $folderName . '/QR_' . $fileIdentifier . '.svg';

                // Masukkan ke dalam zip dengan struktur folder/nama_file.svg
                $zip->addFromString($fileName, $qrCode);
            }

            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function import(Request $request)
    {
        // DB::beginTransaction();
        try {
            Excel::import(new EsdImport, $request->file('file'));
           
            return back()->with('success', 'Import Berhasil!');
        } catch (\Exception $e) {
            // Cek level transaksi agar tidak muncul error "No transaction found"
            
            
            // Log pesan error aslinya untuk pengecekan
            Log::error("Detail Gagal Import: " . $e->getMessage());
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function create() {
        $package =  Package::with('items')->get();
        $items = Item::all();
    
        return view('admin.entities.form', compact('package', 'items'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'npk'           => 'required|string|max:1024',
            'employee_name' => 'required|string|max:100',
            'items'         => 'nullable|array', 
        ]);

        try {
            DB::beginTransaction();

            $currentUserId = Auth::id() ?? $request->creator_id;

            $entity = Entity::create([
                'npk'            => $request->npk,
                'employee_name'  => $request->employee_name,
                'dept_id'        => $request->dept_id,
                'dept_name'      => $request->dept_name,
                'no_loker'       => $request->no_loker,
                'line_id'        => $request->line_id,
                'line_name'      => $request->line_name,
                'status'         => $request->status ?? 'AKTIF',
                'entity_link_qr' => $request->entity_link_qr,
                'creator_id'     => $currentUserId,
                'category'       => $request->category ?? '-',
                'information'    => $request->information ?? '-',
            ]);

            if ($request->has('items')) {
                foreach ($request->items as $item) {
                    $entity->items()->attach($item['item_id'], [
                        'size'       => $item['size'] ?? null,
                        'notes'      => $item['notes'] ?? null,
                        'creator_id' => $currentUserId,
                        'status'     => $item['status'] ?? '-',
                        'receive_date' => $item['receive_date'] ?? null,
                        'return_date' => $item['return_date'] ?? null,
                        'return_notes' => $item['return_notes'] ?? '-',
                    ]);
                }
            }

            DB::commit();
            //return response()->json(['message' => 'Entity berhasil dibuat', 'data' => $entity], 201);
            return redirect()->route('admin.entities.index')->with('success', 'Entity Successfully Registered.');

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal simpan: ' . $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        $entity = Entity::with('items')->findOrFail($id);
        $items = Item::all(); 
        $package = Package::with('items')->get();

        return view('admin.entities.form', compact('entity', 'items', 'package'));
    }

    public function copy($id)
    {
        $entity = Entity::with('items')->findOrFail($id);
        $items = Item::all();
        $package = Package::with('items')->get();
        $isCopy = true;

        return view('admin.entities.form', compact('entity', 'items', 'isCopy', 'package'));
    }

    public function show($id)
    {
        $entity = Entity::with(['items', 'transactions'])->find($id);

        if (!$entity) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($entity);
    }

    public function update(Request $request, $id)
    {
        $entity = Entity::find($id);

        if (!$entity) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $entity->update($request->except(['id', 'creator_id']));

        if ($request->has('items')) {
            $currentUserId = Auth::id() ?? $request->creator_id;
            $syncData = [];
            foreach ($request->items as $item) {
                $syncData[$item['item_id']] = [
                    'size'       => $item['size'] ?? null,
                    'notes'      => $item['notes'] ?? null,
                    'creator_id' => $currentUserId,
                ];
            }
            $entity->items()->sync($syncData);
        }

        //return response()->json(['message' => 'Entity berhasil diperbarui', 'data' => $entity]);
        return redirect()->route('admin.entities.index')->with('success', 'Entity Successfully Updated.');

    }

    public function destroy($id)
    {
        $entity = Entity::find($id);

        if (!$entity) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        try {
            DB::beginTransaction();
            $entity->items()->detach();
            $entity->delete();
            DB::commit();

            // return response()->json(['message' => 'Entity berhasil dihapus']);
            return redirect()->route('admin.entities.index')->with('success', 'Data karyawan berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            // return response()->json(['message' => 'Gagal hapus: ' . $e->getMessage()], 500);
            return redirect()->route('admin.entities.index')->with('error', 'Gagal hapus: ' . $e->getMessage());
        }
    }

    /**
     * Menghasilkan data stok (spare) secara otomatis berdasarkan target manual.
     * Menggunakan logika: Target - Data Karyawan yang Sudah Ada = Sisa Stok yang Perlu QR.
     */
    public function generateManualSpare()
{
    $matrix = [
        'ATS'   => ['Pemagangan' => 3],
        'ATM'   => ['Pemagangan' => 3],
        'ATL'   => ['Pemagangan' => 3, 'OB' => 5, 'PKL' => 2],
        'ATXL'  => ['Pemagangan' => 3, 'PKL' => 2],
        'AT2XL' => ['PKL' => 2],
        'AT3XL' => ['OB' => 1],
        'BJS'   => ['Pemagangan' => 7],
        'BJM'   => ['Pemagangan' => 5],
        'BJL'   => ['Pemagangan' => 5],
        'BJXL'  => ['Pemagangan' => 3],
        'CTM'   => ['Tamu' => 5],
        'CTL'   => ['Supplier' => 5, 'Tamu' => 5],
        'CTXL'  => ['Supplier' => 5, 'Tamu' => 5],
        'CT2XL' => ['Supplier' => 5, 'Tamu' => 5],
        'CT3XL' => ['Tamu' => 2],
    ];

    \DB::beginTransaction();
   try {
        // 1. Hitung nomor urut awal SEKALI saja di luar loop
        $year = date('Y');
        $latest = Entity::whereYear('created_at', $year)->latest('id')->first();
        $nextNumber = $latest ? (intval(substr($latest->code, -4)) + 1) : 1;

        foreach ($matrix as $kode => $categories) {
            $packageLetter = substr($kode, 0, 1); 
            $extractedSize = substr($kode, 2); 
            if ($extractedSize == '2X') $extractedSize = '2XL';
            if ($extractedSize == '3X') $extractedSize = '3XL';

            $package = \App\Models\Package::where('package_name', $packageLetter)->with('items')->first();
            $codeEsd = \DB::table('CODE_ESD')->where('name', $kode)->first();

            foreach ($categories as $categoryName => $count) {
                for ($i = 1; $i <= $count; $i++) {
                    
                    // 2. Buat string kode secara manual agar urut dan unik
                    $manualCode = 'ENT-' . $year . '-' . str_pad($nextNumber++, 4, '0', STR_PAD_LEFT);

                    $entity = Entity::create([
                        'code'          => $manualCode, // Isi manual untuk menghindari redundansi
                        'npk'           => null, 
                        'employee_name' => null, 
                        'dept_name'     => null, 
                        'status'        => 'AVAILABLE',
                        'category'      => $categoryName,
                        'package'       => $packageLetter,
                        'total_set_esd' => 1,
                        'code_esd'      => $codeEsd->id ?? null,
                        'creator_id'    => 1,
                    ]);

                    if ($package) {
                        $details = [];
                        foreach ($package->items as $item) {
                            $itemSize = (string)$extractedSize;
                            if ($item->id == 5) $itemSize = null;

                            $details[] = [
                                'entity_id'  => $entity->id,
                                'item_id'    => $item->id,
                                'size'       => $itemSize,
                                'notes'      => 'Set ke-1',
                                'created_at' => now(),
                                'updated_at' => now(),
                                'creator_id' => 1,
                                'status'     => 'AVAILABLE',
                                'set_no'     => 1,
                            ];
                        }
                        \DB::table('ENTITY_DETAIL_ITEM')->insert($details);
                    }
                }
            }
        }
        \DB::commit();
        return response()->json(['message' => 'Berhasil menambahkan 81 data Spare tanpa redundansi!']);
    } catch (\Exception $e) {
        \DB::rollBack();
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
}
