<?php

namespace App\Http\Controllers;

use App\Models\AccountsReceivable;
use App\Models\Categories;
use App\Models\Customer;
use App\Models\Products;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{

    public function index()
    {
        $purchases = Sale::with([
            'customer',
            'items.product',
            'payments',
            'accountsReceivable'
        ])
            ->when(request('status'), function ($query, $status) {
                $query->whereHas('accountsReceivable', function ($q) use ($status) {
                    $q->where('status', $status);
                });
            })
            ->latest()
            ->get();


        return view('Sales.index', compact('purchases'));
    }

    public function showReceipt($id, Request $request)
    {
        $sales = Sale::with(['customer', 'items.product', 'payments'])->findOrFail($id);

        $pdf = app(PDF::class)->loadView('Sales.receipt', compact('sales'));
        $pdf->setOptions(['isRemoteEnabled' => true]);

        if ($request->query('print') == 'true') {
            return $pdf->stream('struk_pembelian_' . $sales->invoice_number . '.pdf');
        } else {
            return $pdf->download('struk_pembelian_' . $sales->invoice_number . '.pdf');
        }
    }

    public function viewReceipt($id)
    {
        $sales = Sale::with(['customer', 'items.product', 'payments'])->findOrFail($id);
        return view('Sales.receipt', compact('sales'));
    }


    public function create()
    {
        $customers = Customer::all();
        $products = Products::all();
        $categories = Categories::all();
        return view('Sales.create', compact('customers', 'products', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'customer.name' => 'nullable|string|max:255',
            'customer.email' => 'nullable|string|',
            'customer.phone_number' => 'nullable|string|max:20',
            'customer.address' => 'nullable|string|max:255',

            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'amount_paid' => 'nullable|numeric|min:0',
            'payment_methode' => 'nullable|in:cash,kredit',
            'payment_date' => 'nullable|date',
            'note' => 'nullable|string|max:255',
        ]);

        if ($request->filled('customer_id')) {
            $customerId = $request->customer_id;
        } else {
            $customer = Customer::create([
                'name' => $request->input('customer.name'),
                'email' => $request->input('customer.email'),
                'phone_number' => $request->input('customer.phone_number'),
                'address' => $request->input('customer.address'),
            ]);
            $customerId = $customer->id;
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
        SalePayment::create([
            'sale_id' => $sale->id,
            'amount' => $amountPaid,
            'payment_date' => $request->input('payment_date', now()),
            'payment_methode' => $request->input('payment_methode', 'cash'),
            'note' => $request->input('note'),
        ]);


        // Simpan piutang jika belum lunas
        if ($paymentStatus !== 'paid') {
            AccountsReceivable::create([
                'customer_id' => $customerId,
                'sale_id' => $sale->id,
                'amount_due' => $grandTotal,
                'amount_paid' => $paymentStatus === 'partial' ? $amountPaid : 0,
                'due_date' => now()->addDays(30),
                'payment_methode' => $request->input('payment_methode', 'cash'),
                'status' => $paymentStatus,
            ]);
        }

        return redirect()->route('sales.index')->with(['success' => 'Transaksi penjualan berhasil dibuat!', 'sale_id' => $sale->id]);
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

    public function payDebt(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_methode' => 'required|in:cash,credit',
            'payment_date' => 'required|date',
        ]);

        $account = AccountsReceivable::findOrFail($id);
        $sale = $account->sale;

        // Tambah pembayaran ke tabel purchase_payments
        SalePayment::create([
            'sale_id' => $sale->id,
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
            $sale->payment_status = 'paid';
        } elseif ($account->amount_paid > 0) {
            $account->status = 'partial';
            $sale->payment_status = 'partial';
        }

        $account->save();
        $sale->save();

        return redirect()->back()->with('success', 'Pembayaran hutang berhasil dicatat.');
    }
}
