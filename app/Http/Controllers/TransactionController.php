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
            $transaction = Transaction::create([
                'entity_id'              => $entityId,
                'transaction_code'       => $transactionCode,
                'transaction_type'       => $jenis,
                'transaction_start_date' => now(),
                // Jika Ambil/Ganti/Hilang langsung FINISHED, jika Serah statusnya OPEN
                'transaction_status'     => ($jenis === 'Serah ke laundry') ? 'OPEN' : 'FINISHED',
                'transaction_end_date'   => ($jenis !== 'Serah ke laundry') ? now() : null,
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
