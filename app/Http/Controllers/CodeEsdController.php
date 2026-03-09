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

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:1024',
            'description' => 'required|string|max:100',
            'jumlah_karyawan' => 'required|integer',
        ]);

        CodeEsd::create($request->all());

        return redirect()->route('admin.master-code-esd.index')->with('success', 'Data Berhasil Ditambahkan!');
    }

    public function edit($id)
    {
        $codeEsd = CodeEsd::findOrFail($id);
        return view('admin.master-code-esd.edit', compact('codeEsd'));
    }

    public function update(Request $request, $id)
    {
        $codeEsd = CodeEsd::findOrFail($id);

        $request->validate([
            'code' => 'required|string|max:1024',
            'description' => 'required|string|max:100',
            'jumlah_karyawan' => 'required|integer',
        ]);

        $codeEsd->update($request->all());

        return redirect()->route('admin.master-code-esd.index')->with('success', 'Data Berhasil Diupdate!');
    }

    public function destroy($id)
    {
        $codeEsd = CodeEsd::findOrFail($id);
        $codeEsd->delete();

        return redirect()->route('admin.master-code-esd.index')->with('success', 'Data Berhasil Dihapus!');
    }
}
