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
            'payment_methode' => 'nullable|in:cash,credit',
            'payment_date' => 'nullable|date',
            'note' => 'nullable|string|max:255',
        ]);

        // customer handling
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
        $items = [];

        foreach ($products as $product) {
            $subtotal = $product['quantity'] * $product['price'];
            $total += $subtotal;
            $items[] = [
                'id' => $product['id'],
                'quantity' => $product['quantity'],
                'price' => $product['price'],
                'subtotal' => $subtotal,
                'product' => Products::find($product['id']), // Untuk dapatkan nama dan info produk di view
            ];
        }

        $discountPercent = $request->input('discount', 0);
        $discountAmount = ($discountPercent / 100) * $total;
        $taxAmount = 0.11 * ($total - $discountAmount);
        $grandTotal = ($total - $discountAmount) + $taxAmount;
        $invoiceNumber = $this->generateInvoiceNumber();

        $sale = Sale::create([
            'customer_id' => $customerId,
            'user_id' => $userId,
            'sale_date' => now(),
            'invoice_number' => $invoiceNumber,
            'total' => $total,
            'discount' => $discountAmount,
            'tax' => $taxAmount,
            'grand_total' => $grandTotal,
            'payment_status' => 'unpaid',
            'note' => $request->input('note'),
        ]);

        foreach ($items as $item) {
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'sub_total' => $item['subtotal'],
            ]);
        }

        return redirect()->route('sale.confirmation', ['id' => $sale->id]);
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

    public function showDebtPayment($id)
    {
        $account = AccountsReceivable::findOrFail($id);
        $sale = $account->sale;

        return view('Sales.confirm-debt', compact('account', 'sale'));
    }

    public function payDebt(Request $request, $id)
    {
        $request->validate([
            'amount_paid' => 'required|numeric|min:0.01',
            'payment_methode' => 'required|in:cash,credit',
        ]);

        $account = AccountsReceivable::findOrFail($id);
        $sale = $account->sale;

        // Tambah pembayaran ke tabel sale_payments
        SalePayment::create([
            'sale_id' => $sale->id,
            'amount' => $request->amount_paid,
            'payment_date' => now(),
            'payment_methode' => $request->payment_methode,
            'note' => 'Pembayaran hutang',
        ]);

        // Update jumlah yang sudah dibayar
        $account->amount_paid += $request->amount_paid;

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

        return redirect()->route('sales.index')
            ->with('success', 'Pembelian dikonfirmasi dan disimpan.')
            ->with('sale_id', $sale->id);
    }

    public function showConfirmation($id)
    {
        $sale = Sale::with(['items.product', 'user', 'customer'])->findOrFail($id);

        if ($sale->payment_status === 'paid') {
            // Redirect ke index jika sudah lunas
            return redirect()->route('sales.index')->with('info', 'Pembelian ini sudah lunas dan tidak bisa dikonfirmasi ulang.');
        }

        return view('Sales.confirm', compact('sale'));
    }


    public function confirmation(Request $request, $id)
    {
        $sale = Sale::with('items')->findOrFail($id);

        $request->validate([
            'amount_paid' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:255',
            'payment_methode' => 'required|in:cash,credit'
        ]);

        $amountPaid = $request->input('amount_paid');
        $grandTotal = $sale->grand_total;

        // Tentukan status pembayaran
        if ($amountPaid == 0) {
            $status = 'unpaid';
        } elseif ($amountPaid < $grandTotal) {
            $status = 'partial';
        } else {
            $status = 'paid';
        }

        $paymentMethod = $request->input('payment_methode');

        // Simpan ke sale_payments
        SalePayment::create([
            'sale_id' => $sale->id,
            'amount' => $amountPaid,
            'payment_date' => now(),
            'payment_methode' => $paymentMethod,
            'note' => $request->note,
        ]);

        // Simpan ke accounts_payable
        AccountsReceivable::create([
            'customer_id' => $sale->customer_id,
            'sale_id' => $sale->id,
            'amount_due' => $grandTotal,
            'amount_paid' => $amountPaid,
            'due_date' => now()->addDays(30),
            'payment_methode' => $paymentMethod,
            'status' => $status,
        ]);

        // Update status di tabel sales
        $sale->update([
            'payment_status' => $status,
        ]);

        // Tambah stok produk (jika belum ditambah sebelumnya)
        foreach ($sale->items as $item) {
            $product = Products::find($item->product_id);
            if ($product) {
                $product->stock -= $item->quantity;
                $product->save();
            }
        }

        return redirect()->route('sales.index')
            ->with('success', 'Pembelian dikonfirmasi dan disimpan.')
            ->with('sale_id', $sale->id);
    }
}
