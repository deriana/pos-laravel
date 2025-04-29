<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Products;
use App\Models\PurchaseItem;
use App\Models\AccountsPayable;
use App\Models\PurchasePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function index()
    {
        return view('purchases.index');
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $products = Products::all();
        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100', // Dalam persen
            'amount_paid' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|in:cash,credit',
            'payment_date' => 'nullable|date',
        ]);

        $supplierId = $request->supplier_id;
        $userId = Auth::id();
        $products = $request->input('products');
        $total = 0;
        $subtotalItems = [];

        foreach ($products as $product) {
            $subtotal = $product['quantity'] * $product['price'];
            $total += $subtotal;
            $subtotalItems[] = [
                'product_id' => $product['id'],
                'quantity' => $product['quantity'],
                'price' => $product['price'],
                'subtotal' => $subtotal,
            ];
        }

        // Hitung diskon (dalam persen)
        $discountPercent = $request->input('discount', 0);
        $discount = ($discountPercent / 100) * $total;

        // Pajak (PPN 11%) dihitung dari total setelah diskon
        $tax = 0.11 * ($total - $discount);

        // Hitung grand total
        $grandTotal = ($total - $discount) + $tax;

        $invoiceNumber = $this->generateInvoiceNumber();

        $amountPaid = $request->input('amount_paid', 0);

        // Tentukan status pembayaran otomatis
        if ($amountPaid >= $grandTotal) {
            $paymentStatus = 'paid';
        } elseif ($amountPaid > 0 && $amountPaid < $grandTotal) {
            $paymentStatus = 'partial';
        } else {
            $paymentStatus = 'unpaid';
        }

        $purchase = Purchase::create([
            'supplier_id' => $supplierId,
            'user_id' => $userId,
            'sale_date' => now(),
            'invoice_number' => $invoiceNumber,
            'total' => $total,
            'discount' => $discount,
            'tax' => $tax,
            'grand_total' => $grandTotal,
            'payment_status' => $paymentStatus,
            'note' => $request->note,
        ]);

        foreach ($subtotalItems as $item) {
            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['subtotal'],
            ]);

            $product = Products::find($item['product_id']);
            if ($product) {
                $product->stock += $item['quantity'];
                $product->save();
            }
        }

        $amountPaid = $request->input('amount_paid', 0);

        PurchasePayment::create([
            'purchase_id' => $purchase->id,
            'amount' => $amountPaid,
            'payment_date' => $request->input('payment_date', now()),
            'payment_method' => $request->input('payment_method', 'cash'),
            'note' => $request->input('note'),
        ]);        

        if ($request->payment_status !== 'paid') {
            AccountsPayable::create([
                'supplier_id' => $supplierId,
                'purchase_id' => $purchase->id,
                'amount_due' => $grandTotal,
                'amount_paid' => $request->payment_status === 'partial' ? $request->input('amount_paid', 0) : 0,
                'due_date' => now()->addDays(30),
                'payment_method' => $request->input('payment_method', 'cash'),
                'status' => $paymentStatus,
            ]);
        }

        return redirect()->route('purchases.index')->with('success', 'Transaksi pembelian berhasil dibuat!');
    }


    private function generateInvoiceNumber()
    {
        $date = date('Ymd');
        $latestInvoice = Purchase::where('invoice_number', 'like', '%' . $date . '%')
            ->orderBy('id', 'desc')
            ->first();

        $lastInvoiceNumber = $latestInvoice ? (int)substr($latestInvoice->invoice_number, -3) : 0;
        return sprintf('INV-%s-%03d', $date, $lastInvoiceNumber + 1);
    }
}
