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

        $perPage = request('per_page', 10); // default 10 jika tidak ada parameter

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
            ->paginate($perPage)
            ->withQueryString();

        return view('Purchases.index', compact('purchases'));
    }


    public function showReceipt($id, Request $request)
    {
        $purchase = Purchase::with(['supplier', 'purchaseItems.product', 'payments'])->findOrFail($id);

        $pdf = app(PDF::class)->loadView('Purchases.receipt', compact('purchase'));
        $pdf->setOptions(['isRemoteEnabled' => true]);

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
            'payment_methode' => 'nullable|in:cash,credit',
            'payment_date' => 'nullable|date',
            'note' => 'nullable|string|max:255',
        ]);

        // Supplier handling
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

        $purchase = Purchase::create([
            'supplier_id' => $supplierId,
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
            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['subtotal'],
            ]);
        }

        // Kirim data ke halaman konfirmasi (dengan compact atau array)
        return redirect()->route('purchases.confirmation', ['id' => $purchase->id]);
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

    public function showDebtPayment($id)
    {
        $account = AccountsPayable::findOrFail($id);
        $purchase = $account->purchase;

        return view('Purchases.confirm-debt', compact('account', 'purchase'));
    }

    public function payDebt(Request $request, $id)
    {
        $request->validate([
            'amount_paid' => 'required|numeric|min:0.01',
            'payment_methode' => 'required|in:cash,credit',
        ]);

        $account = AccountsPayable::findOrFail($id);
        $purchase = $account->purchase;

        // Tambah pembayaran ke tabel purchase_payments
        PurchasePayment::create([
            'purchase_id' => $purchase->id,
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
            $purchase->payment_status = 'paid';
        } elseif ($account->amount_paid > 0) {
            $account->status = 'partial';
            $purchase->payment_status = 'partial';
        }

        $account->save();
        $purchase->save();

        return redirect()->route('purchases.index')
            ->with('success', 'Pembelian dikonfirmasi dan disimpan.')
            ->with('purchase_id', $purchase->id);
    }

    public function showConfirmation($id)
    {
        $purchase = Purchase::with(['purchaseItems.product', 'user', 'supplier'])->findOrFail($id);

        if ($purchase->payment_status === 'paid') {
            // Redirect ke index jika sudah lunas
            return redirect()->route('purchases.index')->with('info', 'Pembelian ini sudah lunas dan tidak bisa dikonfirmasi ulang.');
        }

        return view('Purchases.confirm', compact('purchase'));
    }


    public function confirmation(Request $request, $id)
    {
        $purchase = Purchase::with('purchaseItems')->findOrFail($id);

        $request->validate([
            'amount_paid' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:255',
            'payment_methode' => 'required|in:cash,credit'
        ]);

        $amountPaid = $request->input('amount_paid');
        $grandTotal = $purchase->grand_total;

        // Tentukan status pembayaran
        if ($amountPaid == 0) {
            $status = 'unpaid';
        } elseif ($amountPaid < $grandTotal) {
            $status = 'partial';
        } else {
            $status = 'paid';
        }

        $paymentMethod = $request->input('payment_methode');

        // Simpan ke purchase_payments
        PurchasePayment::create([
            'purchase_id' => $purchase->id,
            'amount' => $amountPaid,
            'payment_date' => now(),
            'payment_methode' => $paymentMethod,
            'note' => $request->note,
        ]);

        // Simpan ke accounts_payable
        AccountsPayable::create([
            'supplier_id' => $purchase->supplier_id,
            'purchase_id' => $purchase->id,
            'amount_due' => $grandTotal,
            'amount_paid' => $amountPaid,
            'due_date' => now()->addDays(30),
            'payment_methode' => $paymentMethod,
            'status' => $status,
        ]);

        // Update status di tabel purchases
        $purchase->update([
            'payment_status' => $status,
        ]);

        // Tambah stok produk (jika belum ditambah sebelumnya)
        foreach ($purchase->purchaseItems as $item) {
            $product = Products::find($item->product_id);
            if ($product) {
                $product->stock += $item->quantity;
                $product->save();
            }
        }

        return redirect()->route('purchases.index')
            ->with('success', 'Pembelian dikonfirmasi dan disimpan.')
            ->with('purchase_id', $purchase->id);
    }
}
