<?php

namespace App\Http\Controllers;

use App\Models\AccountsReceivable;
use App\Models\Customer;
use App\Models\Products;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{

    public function index()
    {
        $purchases = Sale::with([
            'customer',
            // 'purchaseItems.product',
            'items',
            'payments'
        ])
            ->when(request('status'), function ($query, $status) {
                $query->whereHas('accountsPayable', function ($q) use ($status) {
                    $q->where('status', $status);
                });
            })
            ->latest()
            ->get();


        return view('sales.index', compact('purchases'));
    }

    public function create()
    {
        dd(session('checkout_cart'));
        $customers = Customer::all();
        $products = Products::all();
        return view('sales.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'amount_paid' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|in:cash,transfer',
            'payment_date' => 'nullable|date',

            // Customer field
            'name' => 'required',
            'phone_number' => 'required',
            'email' => 'nullable',
            'address' => 'required',
        ]);

        // Customer

        $customerId = $request->customer_id;
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

        $discountPercent = $request->input('discount', 0);
        $discount = ($discountPercent / 100) * $total;

        $tax = 0.11 * ($total - $discount); // PPN 11%
        $grandTotal = ($total - $discount) + $tax;

        $invoiceNumber = $this->generateInvoiceNumber(); // Buat method ini sesuai kebutuhan
        $amountPaid = $request->input('amount_paid', 0);

        $paymentStatus = match (true) {
            $amountPaid >= $grandTotal => 'paid',
            $amountPaid > 0 => 'partial',
            default => 'unpaid',
        };

        $sale = Sale::create([
            'customer_id' => $customerId,
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
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'sub_total' => $item['subtotal'],
            ]);

            $product = Products::find($item['product_id']);
            if ($product) {
                $product->stock -= $item['quantity']; // Kurangi stok karena dijual
                $product->save();
            }
        }

        // Simpan pembayaran (jika ada)
        if ($amountPaid > 0) {
            SalePayment::create([
                'sale_id' => $sale->id,
                'amount' => $amountPaid,
                'payment_date' => $request->input('payment_date', now()),
                'payment_methode' => $request->input('payment_method', 'cash'),
                'note' => $request->note,
            ]);
        }

        // Simpan piutang jika belum lunas
        if ($paymentStatus !== 'paid') {
            AccountsReceivable::create([
                'sale_id' => $sale->id,
                'amount' => $grandTotal,
                'payment_method' => $request->input('payment_method', 'cash'),
                'note' => $request->note,
            ]);
        }

        return redirect()->route('sales.index')->with('success', 'Transaksi penjualan berhasil dibuat!');
    }

    private function generateInvoiceNumber()
    {
        $date = date('Ymd');
        $latestInvoice = Sale::where('invoice_number', 'like', '%' . $date . '%')
            ->orderBy('id', 'desc')
            ->first();

        $lastInvoiceNumber = $latestInvoice ? (int)substr($latestInvoice->invoice_number, -3) : 0;
        return sprintf('INV-%s-%03d', $date, $lastInvoiceNumber + 1);
    }
}
