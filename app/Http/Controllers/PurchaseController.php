<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Products;
use App\Models\PurchaseItem;
use App\Models\AccountsPayable;
use App\Models\Categories;
use App\Models\PurchasePayment;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with([
            'supplier',
            'purchaseItems.product',
            'payments',
            'accountsPayable'
        ])
            ->when(request('status'), function ($query, $status) {
                $query->whereHas('accountsPayable', function ($q) use ($status) {
                    $q->where('status', $status);
                });
            })
            ->latest()
            ->get();

        return view('Purchases.index', compact('purchases'));
    }


    public function showReceipt($id, Request $request)
    {
        $purchase = Purchase::with(['supplier', 'purchaseItems.product', 'payments'])->findOrFail($id);

        $pdf = app(PDF::class)->loadView('Purchases.receipt', compact('purchase'));

        if ($request->query('print') == 'true') {
            return $pdf->stream('struk_pembelian_' . $purchase->invoice_number . '.pdf');
        } else {
            return $pdf->download('struk_pembelian_' . $purchase->invoice_number . '.pdf');
        }
    }

    public function viewReceipt($id)
    {
        $purchase = Purchase::with(['supplier', 'purchaseItems.product', 'payments'])->findOrFail($id);
        return view('Purchases.receipt', compact('purchase'));
    }



    public function create()
    {
        $suppliers = Supplier::all();
        $products = Products::all();
        $categories = Categories::all();
        return view('Purchases.create', compact('suppliers', 'products', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            // Validasi untuk supplier lama atau data supplier baru
            'supplier_id' => 'nullable|exists:suppliers,id',
            'supplier.name' => 'nullable|string|max:255',
            'supplier.email' => 'nullable|string|',
            'supplier.phone_number' => 'nullable|string|max:20',
            'supplier.address' => 'nullable|string|max:255',

            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'amount_paid' => 'nullable|numeric|min:0',
            'payment_methode' => 'nullable|in:cash,credit',
            'payment_date' => 'nullable|date',
            'note' => 'nullable|string|max:255',
        ]);

        // Buat supplier baru jika tidak ada supplier_id
        if ($request->filled('supplier_id')) {
            $supplierId = $request->supplier_id;
        } else {
            $supplier = Supplier::create([
                'name' => $request->input('supplier.name'),
                'email' => $request->input('supplier.email'),
                'phone_number' => $request->input('supplier.phone_number'),
                'address' => $request->input('supplier.address'),
            ]);
            $supplierId = $supplier->id;
        }

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
        $tax = 0.11 * ($total - $discount);
        $grandTotal = ($total - $discount) + $tax;
        $invoiceNumber = $this->generateInvoiceNumber();
        $amountPaid = $request->input('amount_paid', 0);

        // Tentukan status pembayaran
        $paymentStatus = match (true) {
            $amountPaid >= $grandTotal => 'paid',
            $amountPaid > 0 => 'partial',
            default => 'unpaid',
        };

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
            'note' => $request->input('note'),
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

        PurchasePayment::create([
            'purchase_id' => $purchase->id,
            'amount' => $amountPaid,
            'payment_date' => $request->input('payment_date', now()),
            'payment_methode' => $request->input('payment_methode', 'cash'),
            'note' => $request->input('note'),
        ]);

        if ($paymentStatus !== 'paid') {
            AccountsPayable::create([
                'supplier_id' => $supplierId,
                'purchase_id' => $purchase->id,
                'amount_due' => $grandTotal,
                'amount_paid' => $paymentStatus === 'partial' ? $amountPaid : 0,
                'due_date' => now()->addDays(30),
                'payment_methode' => $request->input('payment_methode', 'cash'),
                'status' => $paymentStatus,
            ]);
        }

        return redirect()->route('purchases.index')->with([
            'success' => 'Transaksi berhasil!',
            'purchase_id' => $purchase->id
        ]);
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

    public function payDebt(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_methode' => 'required|in:cash,credit',
            'payment_date' => 'required|date',
        ]);

        $account = AccountsPayable::findOrFail($id);
        $purchase = $account->purchase;

        // Tambah pembayaran ke tabel purchase_payments
        PurchasePayment::create([
            'purchase_id' => $purchase->id,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_methode' => $request->payment_methode,
            'note' => 'Pembayaran hutang',
        ]);

        // Update jumlah yang sudah dibayar
        $account->amount_paid += $request->amount;

        // Tentukan status
        if ($account->amount_paid >= $account->amount_due) {
            $account->status = 'paid';
            $purchase->payment_status = 'paid';
        } elseif ($account->amount_paid > 0) {
            $account->status = 'partial';
            $purchase->payment_status = 'partial';
        }

        $account->save();
        $purchase->save();

        return redirect()->back()->with('success', 'Pembayaran hutang berhasil dicatat.');
    }
}
