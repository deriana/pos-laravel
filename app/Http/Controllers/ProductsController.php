<?php

namespace App\Http\Controllers;

use App\Models\BarCode;
use App\Models\Categories;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Milon\Barcode\DNS1D;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $categories = Categories::all();

        $query = Products::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        $products = $query->with('category', 'barCode')->paginate(6);

        return view('Products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Categories::all(); 
        return view('Products.create', compact('categories'));
    }

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

        $sku = $request->sku ?: strtoupper('PROD-' . Str::random(8));

        if ($request->hasFile('product_image')) {
            $image = $request->file('product_image');
            $imagePath = $image->storeAs('public/images', uniqid() . '.' . $image->getClientOriginalExtension());
            $imageName = basename($imagePath);
        } else {
            $imageName = null;
        }

        $product = Products::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'sku' => $sku,
            'product_image' => $imageName, 
            'purchase_price' => $request->purchase_price,
            'selling_price' => $request->selling_price,
            'unit' => $request->unit,
        ]);

        $barcodeGenerator = new DNS1D();
        $barcode = $barcodeGenerator->getBarcodePNG($sku, 'C128');
        $barcodeFileName = 'barcode-' . uniqid() . '.png';
        Storage::put("public/barcodes/{$barcodeFileName}", base64_decode($barcode));


        $barName = basename($barcodeFileName);

        BarCode::create([
            'product_id' => $product->id,
            'filename' => $barName
        ]);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function generateBarcode($id)
    {
        $product = Products::findOrFail($id);

        if ($product->barCode) {
            return back()->with('info', 'Barcode sudah ada untuk produk ini.');
        }

        $barcodeGenerator = new DNS1D();
        $barcodePNG = $barcodeGenerator->getBarcodePNG($product->sku, 'C128');

        $barcodeFileName = 'barcode-' . uniqid() . '.png';
        Storage::put("public/barcodes/{$barcodeFileName}", base64_decode($barcodePNG));

        BarCode::create([
            'product_id' => $product->id,
            'filename' => $barcodeFileName
        ]);

        return back()->with('success', 'Barcode berhasil digenerate.');
    }

    public function edit($id)
    {
        $product = Products::findOrFail($id);
        $categories = Categories::all();
        return view('Products.edit', compact('product', 'categories'));
    }

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

        $sku = $request->sku ?: $product->sku;

        if ($request->hasFile('product_image')) {
            if ($product->product_image && file_exists(storage_path('app/public/images/' . $product->product_image))) {
                unlink(storage_path('app/public/images/' . $product->product_image));
            }

            $imagePath = $request->file('product_image')->store('public/images');
            $imageName = basename($imagePath); 
        } else {
            $imageName = $product->product_image;
        }

        $product->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'sku' => $sku,
            'product_image' => $imageName,
            'purchase_price' => $request->purchase_price,
            'selling_price' => $request->selling_price,
            'unit' => $request->unit,
        ]);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }


    public function destroy($id)
    {
        $product = Products::findOrFail($id);

        Storage::delete($product->product_image);

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
