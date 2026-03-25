<?php

namespace App\Http\Controllers;

use App\Imports\EsdImport;
use App\Models\CodeEsd;
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

    public function laundryForm($code) {
        $entity = Entity::where('code', $code)->firstOrFail();
        return view('public.laundry_form', compact('entity'));
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

        $qrUrl = url('/preview/' . $entity->code);

         $endroidQr = \Endroid\QrCode\QrCode::create($qrUrl)
                    ->setSize(300)
                    ->setMargin(1)
                    ->setErrorCorrectionLevel(\Endroid\QrCode\ErrorCorrectionLevel::Low)
                    ->setRoundBlockSizeMode(\Endroid\QrCode\RoundBlockSizeMode::Margin);

        $writer = new \Endroid\QrCode\Writer\PngWriter();
        $result = $writer->write($endroidQr);

        $filename = 'QR_' . ($entity->npk ?? $entity->code) . '.png';

        return response($result->getString())
                ->header('Content-Type', 'image/png')
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

                $endroidQr = \Endroid\QrCode\QrCode::create($qrUrl)
                    ->setSize(300)
                    ->setMargin(1)
                    ->setErrorCorrectionLevel(\Endroid\QrCode\ErrorCorrectionLevel::Low)
                    ->setRoundBlockSizeMode(\Endroid\QrCode\RoundBlockSizeMode::Margin);

                $writer = new \Endroid\QrCode\Writer\PngWriter();
                $result = $writer->write($endroidQr);
                $qrCode = $result->getString();

                /** * 2. Tentukan Nama Folder berdasarkan Nama CODE_ESD (ATS, ATM, BJS, dll)
                 */
                $folderName = $entity->codeEsd ? $entity->codeEsd->name : 'UNCATEGORIZED';

                /** * 3. Tentukan Nama File 
                 * Jika NPK ada, gunakan NPK. Jika stok AVAILABLE (NPK null), gunakan kode uniknya.
                 */
                $fileIdentifier = $entity->code;
                $fileName = $folderName . '/QR_' . $fileIdentifier . '.png';

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
        $codeEsds = CodeEsd::all();
    
        return view('admin.entities.form', compact('package', 'items', 'codeEsds'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'npk'           => 'nullable|string|max:1024',
            'employee_name' => 'nullable|string|max:100',
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
                'package'        => $request->package ?? '-',
                'code_esd'       => $request->code_esd,
            ]);

            if ($request->code_esd) {
                CodeEsd::where('id', $request->code_esd)->increment('jumlah_karyawan', 1);
            }

            if ($request->has('items')) {
                $itemCounts = [];
                foreach ($request->items as $item) {
                    $itemId = $item['item_id'];
                    // Hitung jumlah kemunculan item secara dinamis
                    $itemCounts[$itemId] = isset($itemCounts[$itemId]) ? $itemCounts[$itemId] + 1 : 1;
                    
                    $setNo = $item['set_no'] ?? $itemCounts[$itemId];
                    $notes = $item['notes'] ?? 'Set ke-' . $setNo;

                    $entity->items()->attach($itemId, [
                        'set_no'       => $setNo,
                        'size'         => $item['size'] ?? null,
                        'notes'        => $notes,
                        'creator_id'   => $currentUserId,
                        'status'       => $item['status'] ?? '-',
                        'receive_date' => $item['receive_date'] ?? null,
                        'return_date'  => $item['return_date'] ?? null,
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
        $codeEsds = CodeEsd::all();

        return view('admin.entities.form', compact('entity', 'items', 'package', 'codeEsds'));
    }

    public function copy($id)
    {
        $entity = Entity::with('items')->findOrFail($id);
        $items = Item::all();
        $package = Package::with('items')->get();
        $codeEsds = CodeEsd::all();
        $isCopy = true;

        return view('admin.entities.form', compact('entity', 'items', 'isCopy', 'package', 'codeEsds'));
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

        $oldCodeEsd = $entity->code_esd;

        $entity->update($request->except(['id', 'creator_id']));

        // Handle CodeEsd Counter Change
        if ($request->has('code_esd') && $request->code_esd != $oldCodeEsd) {
            if ($oldCodeEsd) {
                CodeEsd::where('id', $oldCodeEsd)->decrement('jumlah_karyawan', 1);
            }
            if ($request->code_esd) {
                CodeEsd::where('id', $request->code_esd)->increment('jumlah_karyawan', 1);
            }
        }

        if ($request->has('items')) {
            $currentUserId = Auth::id() ?? $request->creator_id;
            
            // Simpan data pivot lama untuk mengambil 'created_at' nya
            $oldPivots = DB::table('ENTITY_DETAIL_ITEM')->where('entity_id', $id)->get();
            
            // Hapus semua data pivot karena method sync() tidak mendukung duplicate item_id (akan tertimpa)
            $entity->items()->detach();

            $itemCounts = [];
            foreach ($request->items as $item) {
                $itemId = $item['item_id'];
                $itemCounts[$itemId] = isset($itemCounts[$itemId]) ? $itemCounts[$itemId] + 1 : 1;
                
                $setNo = $item['set_no'] ?? $itemCounts[$itemId];
                $notes = $item['notes'] ?? 'Set ke-' . $setNo;

                // Cek data lama jika barang yang sama dan set_no sama
                $createdAt = now();
                foreach ($oldPivots as $old) {
                    if ($old->item_id == $itemId && $old->set_no == $setNo) {
                        $createdAt = $old->created_at;
                        break;
                    }
                }

                $entity->items()->attach($itemId, [
                    'set_no'       => $setNo,
                    'size'         => $item['size'] ?? null,
                    'notes'        => $notes,
                    'creator_id'   => $currentUserId,
                    'status'       => $item['status'] ?? '-',
                    'receive_date' => $item['receive_date'] ?? null,
                    'return_date'  => $item['return_date'] ?? null,
                    'return_notes' => $item['return_notes'] ?? '-',
                    'created_at'   => $createdAt,
                    'updated_at'   => now(),
                ]);
            }
        } else {
             $entity->items()->detach();
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

            if ($entity->code_esd) {
                CodeEsd::where('id', $entity->code_esd)->decrement('jumlah_karyawan', 1);
            }

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

    // Pertama jalanin fungsi ini dulu, baru setelah itu yang bawahnya
    // public function generateManualSpare()
    // {
    //     $matrix = [
    //         'ATS'   => ['Pemagangan' => 3],
    //         'ATM'   => ['Pemagangan' => 3],
    //         'ATL'   => ['Pemagangan' => 3, 'OB' => 5, 'PKL' => 2],
    //         'ATXL'  => ['Pemagangan' => 3, 'PKL' => 2],
    //         'AT2XL' => ['PKL' => 2],
    //         'AT3XL' => ['OB' => 1],
    //         'BJS'   => ['Pemagangan' => 7],
    //         'BJM'   => ['Pemagangan' => 5],
    //         'BJL'   => ['Pemagangan' => 5],
    //         'BJXL'  => ['Pemagangan' => 3],
    //         'CTM'   => ['Tamu' => 5],
    //         'CTL'   => ['Supplier' => 5, 'Tamu' => 5],
    //         'CTXL'  => ['Supplier' => 5, 'Tamu' => 5],
    //         'CT2XL' => ['Supplier' => 5, 'Tamu' => 5],
    //         'CT3XL' => ['Tamu' => 2],
    //     ];

    //     \DB::beginTransaction();
    // try {
    //         // 1. Hitung nomor urut awal SEKALI saja di luar loop
    //         $year = date('Y');
    //         $latest = Entity::whereYear('created_at', $year)->latest('id')->first();
    //         $nextNumber = $latest ? (intval(substr($latest->code, -4)) + 1) : 1;

    //         foreach ($matrix as $kode => $categories) {
    //             $packageLetter = substr($kode, 0, 1); 
    //             $extractedSize = substr($kode, 2); 
    //             if ($extractedSize == '2X') $extractedSize = '2XL';
    //             if ($extractedSize == '3X') $extractedSize = '3XL';

    //             $package = \App\Models\Package::where('package_name', $packageLetter)->with('items')->first();
    //             $codeEsd = \DB::table('CODE_ESD')->where('name', $kode)->first();

    //             foreach ($categories as $categoryName => $count) {
    //                 for ($i = 1; $i <= $count; $i++) {
                        
    //                     // 2. Buat string kode secara manual agar urut dan unik
    //                     $manualCode = 'ENT-' . $year . '-' . str_pad($nextNumber++, 4, '0', STR_PAD_LEFT);

    //                     $entity = Entity::create([
    //                         'code'          => $manualCode, // Isi manual untuk menghindari redundansi
    //                         'npk'           => null, 
    //                         'employee_name' => null, 
    //                         'dept_name'     => null, 
    //                         'status'        => 'AVAILABLE',
    //                         'category'      => $categoryName,
    //                         'package'       => $packageLetter,
    //                         'total_set_esd' => 1,
    //                         'code_esd'      => $codeEsd->id ?? null,
    //                         'creator_id'    => 1,
    //                     ]);

    //                     if ($package) {
    //                         $details = [];
    //                         foreach ($package->items as $item) {
    //                             $itemSize = (string)$extractedSize;
    //                             if ($item->id == 5) $itemSize = null;

    //                             $details[] = [
    //                                 'entity_id'  => $entity->id,
    //                                 'item_id'    => $item->id,
    //                                 'size'       => $itemSize,
    //                                 'notes'      => 'Set ke-1',
    //                                 'created_at' => now(),
    //                                 'updated_at' => now(),
    //                                 'creator_id' => 1,
    //                                 'status'     => 'AVAILABLE',
    //                                 'set_no'     => 1,
    //                             ];
    //                         }
    //                         \DB::table('ENTITY_DETAIL_ITEM')->insert($details);
    //                     }
    //                 }
    //             }
    //         }
    //         \DB::commit();
    //         return response()->json(['message' => 'Berhasil menambahkan 81 data Spare tanpa redundansi!']);
    //     } catch (\Exception $e) {
    //         \DB::rollBack();
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }


    public function generateManualSpare()
    {
        $matrix = [
            'CTXL'  => ['Backup' => 16],
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

                        CodeEsd::where('id', $codeEsd->id)->increment('jumlah_karyawan', 1);
                        
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
