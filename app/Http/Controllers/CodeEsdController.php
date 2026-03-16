<?php

namespace App\Http\Controllers;

use App\Models\CodeEsd;
use Illuminate\Http\Request;

class CodeEsdController extends Controller
{
    public function index()
    {
        // Mengambil data urut berdasarkan ID (sesuai urutan input di DB)
        $codeEsd = CodeEsd::orderBy('id', 'asc')->get();
        
        return view('admin.master-code-esd.index', compact('codeEsd'));
    }

    public function create()
    {
        return view('admin.master-code-esd.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:CODE_ESD,name',
        ]);

        CodeEsd::create([
            'name' => strtoupper($request->name),
            'jumlah_karyawan' => 0,
            'creator_id' => auth()->id() ?? 1,
        ]);

        return redirect()->route('admin.code-esd.index')->with('success', 'Master Code ESD Berhasil Ditambahkan!');
    }

    public function show($id)
    {
        // Eager load entities (Asset ESD yang memakai code ini)
        $codeEsd = CodeEsd::with('entities')->findOrFail($id);
        
        return view('admin.master-code-esd.show', compact('codeEsd'));
    }

    public function edit($id)
    {
        $codeEsd = CodeEsd::findOrFail($id);
        return view('admin.master-code-esd.form', compact('codeEsd'));
    }

    public function update(Request $request, $id)
    {
        $codeEsd = CodeEsd::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:CODE_ESD,name,' . $id,
        ]);

        $codeEsd->update([
            'name' => strtoupper($request->name)
        ]);
        // Note: jumlah_karyawan & creator_id tidak diupdate manual dari form

        return redirect()->route('admin.code-esd.index')->with('success', 'Master Code ESD Berhasil Diupdate!');
    }

    public function destroy($id)
    {
        $codeEsd = CodeEsd::findOrFail($id);
        
        if ($codeEsd->entities()->count() > 0) {
            return redirect()->route('admin.code-esd.index')->with('error', 'Gagal: Master Code ESD ini sedang digunakan oleh Asset/Karyawan!');
        }

        $codeEsd->delete();

        return redirect()->route('admin.code-esd.index')->with('success', 'Master Code ESD Berhasil Dihapus!');
    }
}
