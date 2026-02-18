<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Http;

class EntityController extends Controller
{

    public function index()
    {
        $entities = Entity::all();
        //return response()->json($entities);
        return view('admin.entities.index', compact('entities'));
    }

    public function preview($id)
    {
        $entity = Entity::findOrFail($id);
        
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

    public function create() {
        $items = Item::all();
        return view('admin.entities.form', compact('items'));
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

        return view('admin.entities.form', compact('entity', 'items'));
    }

    public function copy($id)
    {
        $entity = Entity::with('items')->findOrFail($id);
        $items = Item::all();
        $isCopy = true;

        return view('admin.entities.form', compact('entity', 'items', 'isCopy'));
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
}
