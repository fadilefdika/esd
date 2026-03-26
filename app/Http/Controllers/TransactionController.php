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

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'id' => 'required|integer|unique:TRANSACTION,id',
    //         'entity_id' => 'required|exists:ENTITY,id',
    //         'transaction_code' => 'required|string|max:100',
    //         'transaction_start_date' => 'required|date',
    //         'items' => 'required|array', 
    //     ]);

    //     try {
    //         DB::beginTransaction();

    //         $currentUserId = Auth::id() ?? $request->creator_id;

    //         $transaction = Transaction::create([
    //             'id' => $request->id,
    //             'entity_id' => $request->entity_id,
    //             'transaction_code' => $request->transaction_code,
    //             'transaction_start_date' => $request->transaction_start_date,
    //             'transaction_end_date' => $request->transaction_end_date,
    //             'transaction_type' => $request->transaction_type,
    //             'transaction_status' => $request->transaction_status ?? 'OPEN',
    //             'transaction_image_start' => $request->transaction_image_start,
    //             'transaction_image_finish' => $request->transaction_image_finish,
    //             'creator_id' => $currentUserId,
    //         ]);

    //         if ($request->has('items')) {
    //             $transaction->items()->attach($request->items);
    //         }

    //         DB::commit();
    //         return response()->json(['message' => 'Transaksi berhasil dibuat', 'data' => $transaction], 201);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json(['message' => 'Gagal simpan transaksi: ' . $e->getMessage()], 500);
    //     }
    // }

    
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
            
            // Buat Kode Transaksi Unik
            $dateCode = now()->format('Ymd');
            $prefix = ($jenis === 'Serah ke laundry') ? 'SRH' : (($jenis === 'Ambil dari laundry') ? 'AMB' : 'OTH');
            $count = Transaction::whereDate('transaction_start_date', now())->count() + 1;
            $transactionCode = "TRX-{$prefix}-{$dateCode}-" . str_pad($count, 3, '0', STR_PAD_LEFT);

            // LOGIKA BARU: Apapun jenisnya, selalu buat record BARU
            $transaction = Transaction::create([
                'entity_id'              => $entityId,
                'transaction_code'       => $transactionCode,
                'transaction_type'       => $jenis,
                'transaction_start_date' => now(),
                // Jika Ambil/Ganti/Hilang langsung FINISHED, jika Serah statusnya OPEN
                'transaction_status'     => ($jenis === 'Serah ke laundry') ? 'OPEN' : 'FINISHED',
                'creator_id'             => Auth::id() ?? 1,
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

            // Attach items ke record baru ini
            $transaction->items()->attach($request->items);

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
