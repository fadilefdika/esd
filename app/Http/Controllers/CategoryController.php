<?php
namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Menampilkan semua kategori
    public function index()
    {
        $categories = Category::all();
        return view('admin.category.index', compact('categories'));
    }

    // Menampilkan form tambah
    public function create()
    {
        return view('admin.category.create');
    }

    // Menyimpan data baru
    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:100',
        ]);

        Category::create([
            'category_name' => $request->category_name,
            'creator_id' => auth()->id ?? 1 
        ]);

        return redirect()->route('category.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    // Menampilkan form edit
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.category.edit', compact('category'));
    }

    // Mengupdate data
    public function update(Request $request, $id)
    {
        $request->validate([
            'category_name' => 'required|string|max:100',
        ]);

        $category = Category::findOrFail($id);
        $category->update([
            'category_name' => $request->category_name
        ]);

        return redirect()->route('category.index')->with('success', 'Kategori berhasil diupdate.');
    }

    // Menghapus data
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('category.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
