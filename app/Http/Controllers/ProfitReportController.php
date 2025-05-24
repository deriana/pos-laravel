<?php

namespace App\Http\Controllers;

use App\Exports\ProfitReportExport;
use Illuminate\Http\Request;
use App\Models\SaleItem;
use Maatwebsite\Excel\Facades\Excel;

class ProfitReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = SaleItem::with(['product', 'sale']);

        if ($startDate && $endDate) {
            $query->whereHas('sale', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('sale_date', [$startDate, $endDate]);
            });
        }

        $saleItems = $query->paginate(10);

        $report = $saleItems->map(function ($item) {
            $purchasePrice = $item->product ? $item->product->purchase_price : 0;
            $sellingPrice = $item->product ? $item->product->selling_price : 0;
            $quantity = $item->quantity;
            $productName = $item->product ? $item->product->name : 'Barang Unknown';
            $profit = ($sellingPrice - $purchasePrice) * $quantity;

            return [
                'sale_date' => $item->sale->sale_date, 
                'product_name' => $productName,
                'quantity' => $quantity,
                'purchase_price' => $purchasePrice,
                'selling_price' => $sellingPrice,
                'profit' => $profit,
                'subtotal' => $item->sub_total,
            ];
        });

        $totalProfit = $report->sum('profit');

        return view('Reports.profit', [
            'report' => $report,
            'totalProfit' => $totalProfit,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'saleItems' => $saleItems
        ]);
    }

    public function export(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $fileName = 'profit_report_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new ProfitReportExport($startDate, $endDate), $fileName);
    }
}
