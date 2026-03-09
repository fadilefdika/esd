<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $packages = Package::all();
        //return response()->json($package);
        return view('admin.master-package.index', compact('packages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $availableItems = Item::orderBy('item_name', 'asc')->get();
        return view('admin.master-package.form', compact('availableItems'));
    }

    /**
     * Menyimpan package baru beserta item-itemnya.
     */
    public function store(Request $request)
    {
        $request->validate([
            'package_name' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:ITEM,id', // Asumsi table items bernama ITEMS
            'items.*.size' => 'nullable|string|max:50',
        ]);

        try {
            DB::beginTransaction();

            // 1. Simpan Data Package Utama
            $package = Package::create([
                'package_name' => $request->package_name
            ]);

            // 2. Format data untuk tabel pivot
            $syncData = [];
            foreach ($request->items as $item) {
                // Menggunakan item_id sebagai key agar tidak duplikat di satu package
                $syncData[$item['item_id']] = [
                    'size' => $item['size']
                ];
            }

            // 3. Simpan ke tabel relasi (Pivot)
            $package->items()->sync($syncData);

            DB::commit();
            return redirect()->route('admin.packages.index')->with('success', 'Package berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan form edit.
     */
    public function edit($id)
    {
        $package = Package::with('items')->findOrFail($id);
        $availableItems = Item::orderBy('item_name', 'asc')->get();
        
        return view('admin.master-package.form', compact('package', 'availableItems'));
    }

    /**
     * Memperbarui data package dan sinkronisasi ulang item.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'package_name' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:ITEM,id',
            'items.*.size' => 'nullable|string|max:50',
        ]);

        try {
            DB::beginTransaction();

            $package = Package::findOrFail($id);
            
            // 1. Update nama package
            $package->update([
                'package_name' => $request->package_name
            ]);

            // 2. Format data pivot
            $syncData = [];
            foreach ($request->items as $item) {
                $syncData[$item['item_id']] = [
                    'size' => $item['size']
                ];
            }

            // 3. Sync akan menghapus item lama yang tidak ada di request dan menambah yang baru
            $package->items()->sync($syncData);

            DB::commit();
            return redirect()->route('admin.packages.index')->with('success', 'Package berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui data.');
        }
    }

    /**
     * Menghapus package dan relasinya.
     */
    public function destroy($id)
    {
        try {
            $package = Package::findOrFail($id);
            
            // Lepaskan semua relasi di tabel pivot sebelum menghapus package
            $package->items()->detach();
            $package->delete();

            return redirect()->route('admin.packages.index')->with('success', 'Package telah dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data.');
        }
    }
}
