<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Products;
use App\Models\QrCode as ModelsQrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $categories = Categories::all();

        $query = Products::query()->with(['qrCode']);

        // Jika ada query pencarian nama produk
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Jika ada filter berdasarkan kategori
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        // Memuat relasi 'category' dengan eager loading
        $products = $query->with('category')->paginate(6); // Ubah 'categories' menjadi 'category'

        return view('products.index', compact('products', 'categories'));
    }

    // Tampilkan form untuk menambah produk
    public function create()
    {
        $categories = Categories::all();  // Mengambil semua kategori untuk dropdown
        return view('products.create', compact('categories'));
    }

    // Simpan produk baru
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255',
            'product_image' => 'required|image|mimes:jpeg,png,jpg,gif',
            'purchase_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'unit' => 'required|string|max:50',
        ]);

        // Generate SKU otomatis jika tidak ada
        $sku = $request->sku ?: strtoupper('PROD-' . Str::random(8));

        // Proses file gambar
        if ($request->hasFile('product_image')) {
            $image = $request->file('product_image');
            // Simpan gambar di storage/public/images dan ambil nama file
            $imagePath = $image->storeAs('public/images', uniqid() . '.' . $image->getClientOriginalExtension());
            // Ambil nama file yang sudah disimpan (untuk disimpan di DB)
            $imageName = basename($imagePath);
        } else {
            // Jika tidak ada file, set default null atau sesuai kebutuhan
            $imageName = null;
        }

        // Simpan produk
        $product = Products::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'sku' => $sku,
            'product_image' => $imageName,  // Simpan nama file
            'purchase_price' => $request->purchase_price,
            'selling_price' => $request->selling_price,
            'unit' => $request->unit,
        ]);

        $qrContent = $sku; // atau bisa ID, nama produk, dsb
        $qrFileName = 'qr-' . uniqid() . '.png';

        // Simpan QR code sebagai file ke storage/public/qr
        Storage::put("public/qr/{$qrFileName}", QrCode::format('png')->size(300)->generate($qrContent));

        $qrName = basename($qrFileName);

        // Simpan ke tabel qr_codes
        ModelsQrCode::create([
            'product_id' => $product->id,
            'filename' => $qrName
        ]);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }



    // Tampilkan form untuk edit produk
    public function edit($id)
    {
        $product = Products::findOrFail($id);
        $categories = Categories::all();
        return view('products.edit', compact('product', 'categories'));
    }

    // Update data produk
    public function update(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255',
            'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'purchase_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'unit' => 'required|string|max:50',
        ]);

        $product = Products::findOrFail($id);

        // Update SKU jika ada perubahan, jika tidak, tetap menggunakan SKU yang lama
        $sku = $request->sku ?: $product->sku;

        // Menangani upload gambar jika ada gambar baru
        if ($request->hasFile('product_image')) {
            // Hapus gambar lama jika ada
            if ($product->product_image && file_exists(storage_path('app/public/images/' . $product->product_image))) {
                unlink(storage_path('app/public/images/' . $product->product_image));
            }

            // Simpan gambar baru
            $imagePath = $request->file('product_image')->store('public/images');
            // Ambil nama file untuk disimpan di database
            $imageName = basename($imagePath); // Ambil nama file saja
        } else {
            // Jika tidak ada gambar baru, gunakan gambar lama
            $imageName = $product->product_image;
        }

        // Update data produk
        $product->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'sku' => $sku,
            'product_image' => $imageName, // Menyimpan nama file gambar
            'purchase_price' => $request->purchase_price,
            'selling_price' => $request->selling_price,
            'unit' => $request->unit,
        ]);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }


    // Hapus produk
    public function destroy($id)
    {
        $product = Products::findOrFail($id);

        // Hapus gambar produk dari storage
        Storage::delete($product->product_image);

        // Hapus produk dari database
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
