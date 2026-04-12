<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{

    public function index()
    {
        $transactions = Transaction::with('entity')->get();
        return response()->json($transactions);
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'entity_id'       => 'required|exists:ENTITY,id',
            'jenis_transaksi' => 'required|string',
            'items'           => 'required|array|min:1',
        ]);

        try {
            DB::beginTransaction();

            $jenis = $request->jenis_transaksi;
            $entityId = $request->entity_id;
            
            // Buat Kode Transaksi Unik (race-safe)
            $dateCode = now()->format('Ymd');
            $prefix = ($jenis === 'Serah ke laundry') ? 'SRH' : (($jenis === 'Ambil dari laundry') ? 'AMB' : 'OTH');
            $maxCode = Transaction::where('transaction_code', 'LIKE', "TRX-{$prefix}-{$dateCode}-%")
                ->lockForUpdate()
                ->max('transaction_code');
            $count = $maxCode ? ((int) substr($maxCode, -3)) + 1 : 1;
            $transactionCode = "TRX-{$prefix}-{$dateCode}-" . str_pad($count, 3, '0', STR_PAD_LEFT);

            // LOGIKA BARU: Apapun jenisnya, selalu buat record BARU
            $startDate = $request->transaction_date ? \Carbon\Carbon::parse($request->transaction_date) : now();
            
            $transaction = Transaction::create([
                'entity_id'              => $entityId,
                'transaction_code'       => $transactionCode,
                'transaction_type'       => $jenis,
                'transaction_start_date' => $startDate,
                // Jika Ganti/Hilang langsung FINISHED, jika Serah statusnya OPEN
                'transaction_status'     => ($jenis === 'Serah ke laundry') ? 'OPEN' : 'FINISHED',
                'transaction_end_date'   => ($jenis !== 'Serah ke laundry') ? now() : null,
                'creator_id'             => Auth::guard('vendor')->id() ?? Auth::id() ?? 1,
            ]);

            // Jika ini adalah proses "Ambil", kita juga harus menutup (FINISH) transaksi "Serah" sebelumnya
            // Agar status global Entity berubah menjadi AVAILABLE kembali
            if ($jenis === 'Ambil dari laundry') {
                Transaction::where('entity_id', $entityId)
                    ->where('transaction_type', 'Serah ke laundry')
                    ->where('transaction_status', 'OPEN')
                    ->update(['transaction_status' => 'FINISHED', 'transaction_end_date' => now()]);
                
                $message = "Barang berhasil diambil (Data terekam baru).";
            } else {
                $message = "Transaksi $jenis berhasil dicatat.";
            }

            $submittedItems = $request->items; // array of "1_1", "2_1"
            $attachData = [];
            $syncItems = [];
            
            if ($submittedItems && is_array($submittedItems)) {
                $creatorId = Auth::guard('vendor')->id() ?? Auth::id() ?? 1;
                foreach($submittedItems as $val) {
                    if (strpos($val, '_') !== false) {
                        list($itemId, $setNo) = explode('_', $val);
                        $attachData[$itemId] = ['set_no' => $setNo, 'creator_id' => $creatorId];
                        $syncItems[] = ['item_id' => $itemId, 'set_no' => $setNo];
                    } else {
                        // fallback if someone sends normal item_ids without set_no
                        $attachData[$val] = ['set_no' => 1, 'creator_id' => $creatorId];
                        $syncItems[] = ['item_id' => $val, 'set_no' => 1];
                    }
                }
            }

            // Attach items ke record baru ini
            if (count($attachData) > 0) {
                // Warning: if multiple sets use the same item_id, attach() will complain about duplicate primary keys
                // if it's not handled gracefully. In Laravel, if you attach the identical Model ID with different pivot data, 
                // you must do it separately or ensure primary keys allow it. 
                // Fortunately, we can attach correctly because we map $itemId -> [pivotData].
                // Wait! If an employee sends TWO Baju (Set 1 Baju AND Set 2 Baju), that's item_id=1 twice!
                // Since attach() parameter is keyed by item_id ($attachData[$itemId]), the second one will overwrite the first!
                // To attach duplicate item_ids with different pivot data, we can just pass a flat array!
                $flatAttach = [];
                foreach($syncItems as $si) {
                    $flatAttach[] = [
                        'item_id' => $si['item_id'],
                        'set_no' => $si['set_no'],
                        'creator_id' => $creatorId
                    ];
                }
                
                // insert directly to TRANSACTION_DETAIL_ITEM
                foreach($flatAttach as $fa) {
                    DB::table('TRANSACTION_DETAIL_ITEM')->insert([
                        'transaction_id' => $transaction->id,
                        'item_id' => $fa['item_id'],
                        'set_no' => $fa['set_no'],
                        'creator_id' => $fa['creator_id'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            // Sinkronisasi status item di ENTITY_DETAIL_ITEM secara presisi
            if ($jenis === 'Serah ke laundry') {
                foreach($syncItems as $si) {
                    DB::table('ENTITY_DETAIL_ITEM')
                        ->where('entity_id', $entityId)
                        ->where('item_id', $si['item_id'])
                        ->where('set_no', $si['set_no'])
                        ->update(['status' => 'LAUNDRY', 'updated_at' => now()]);
                }
            } elseif ($jenis === 'Ambil dari laundry') {
                foreach($syncItems as $si) {
                    DB::table('ENTITY_DETAIL_ITEM')
                        ->where('entity_id', $entityId)
                        ->where('item_id', $si['item_id'])
                        ->where('set_no', $si['set_no'])
                        ->update(['status' => 'diterima', 'updated_at' => now()]);
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => $message]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $transaction = Transaction::with(['entity', 'items'])->find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        return response()->json($transaction);
    }

    public function update(Request $request, $id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        $transaction->update($request->except(['id', 'creator_id']));

        // Update relasi item menggunakan sync
        if ($request->has('items')) {
            $transaction->items()->sync($request->items);
        }

        return response()->json(['message' => 'Transaksi berhasil diperbarui', 'data' => $transaction]);
    }

    public function destroy($id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        try {
            DB::beginTransaction();
            $transaction->items()->detach();
            $transaction->delete();
            DB::commit();

            return response()->json(['message' => 'Transaksi berhasil dihapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal hapus transaksi'], 500);
        }
    }
}
