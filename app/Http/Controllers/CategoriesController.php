<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    // Tampilkan semua kategori
    public function index()
    {
        $categories = Categories::all();
        return view('Categories.index', compact('categories'));
    }

    // Tampilkan form tambah kategori
    public function create()
    {
        return view('Categories.create');
    }

    // Simpan kategori baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Categories::create($request->all());

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    // Tampilkan form edit kategori
    public function edit($id)
    {
        $category = Categories::findOrFail($id);
        return view('Categories.edit', compact('category'));
    }

    // Update data kategori
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category = Categories::findOrFail($id);
        $category->update($request->all());

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    // Hapus kategori
    public function destroy($id)
    {
        $category = Categories::findOrFail($id);
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
