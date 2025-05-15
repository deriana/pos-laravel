<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Product;
use App\Models\Products;
use Illuminate\Http\Request;

class InventoryReportController extends Controller
{
    public function index(Request $request)
    {
        $perPageOptions = [10, 20, 50];
        $perPage = $request->get('per_page', 10);

        $query = Products::with('category');

        // Filter kategori
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Search nama atau sku
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('sku', 'like', "%$search%");
            });
        }

        // Sorting stock
        if ($request->filled('stock_sort')) {
            $stockSort = $request->stock_sort;
            if (in_array($stockSort, ['asc', 'desc'])) {
                $query->orderBy('stock', $stockSort);
            }
        } else {
            $query->orderBy('stock', 'desc');
        }

        $products = $query->paginate($perPage)->withQueryString();
        $categories = Categories::all();

        return view('Reports.inventory', compact('products', 'categories', 'perPageOptions', 'perPage'));
    }
}
