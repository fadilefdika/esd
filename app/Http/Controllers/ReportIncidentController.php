<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use App\Models\ReportIncident;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportIncidentController extends Controller
{
    public function storeReport(Request $request)
    {
        // 1. Cari Entity berdasarkan NPK user yang login
        //    (User model tidak punya kolom entity_id, relasinya via NPK)
        $user   = auth('web')->user();
        $entity = Entity::where('npk', $user->npk)->first();

        if (!$entity) {
            return response()->json([
                'success' => false,
                'message' => 'Data ESD Anda tidak ditemukan. Hubungi Admin.'
            ], 404);
        }

        // 2. Parsing metadata items dari JSON string ke Array
        $selectedItems = json_decode($request->items_metadata, true);

        if (empty($selectedItems)) {
            return response()->json(['success' => false, 'message' => 'Silakan pilih minimal satu item.'], 400);
        }

        // 3. Validasi
        $request->validate([
            'report_type' => 'required|in:rusak,hilang',
            'details'     => 'required|string|max:2000',
            'evidence.*'  => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // 4. Simpan semua foto ke Storage
        $paths = [];
        if ($request->hasFile('evidence')) {
            foreach ($request->file('evidence') as $file) {
                $path    = $file->store('evidence_reports', 'public');
                $paths[] = $path;
            }
        }

        if (empty($paths)) {
            return response()->json(['success' => false, 'message' => 'Minimal harus ada 1 foto bukti.'], 400);
        }

        DB::beginTransaction();
        try {
            $evidenceJson = json_encode($paths);
            $entityId     = $entity->id; // Ambil dari entity yang sudah ditemukan

            // Update data akan dilakukan per item yang dipilih

            foreach ($selectedItems as $item) {
                $itemId = $item['id'];
                $setNo  = (int) $item['set_no'];

                // 5. Simpan Laporan ke REPORT_INCIDENTS (per item yang dilaporkan)
                ReportIncident::create([
                    'entity_id'     => $entityId,
                    'item_id'       => $itemId,
                    'set_no'        => $setNo,
                    'report_type'   => $request->report_type,
                    'details'       => $request->details,
                    'evidence_path' => $evidenceJson,
                    'status_report' => 'pending',
                    'creator_id'    => $user->id,
                ]);

                // 6. Update status HANYA untuk item yang dilaporkan saja
                DB::table('ENTITY_DETAIL_ITEM')
                    ->where('entity_id', $entityId)
                    ->where('item_id', $itemId)
                    ->where('set_no', $setNo)
                    ->update([
                        'status'     => $request->report_type, // 'rusak' atau 'hilang'
                        'updated_at' => now()
                    ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($selectedItems) . ' item berhasil dilaporkan.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            // Hapus foto yang sudah terlanjur terupload jika DB gagal
            foreach ($paths as $p) {
                Storage::disk('public')->delete($p);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }
}