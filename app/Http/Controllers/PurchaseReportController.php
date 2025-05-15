<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PurchasesExport; // nanti buat export ini

class PurchaseReportController extends Controller
{
    public function index(Request $request)
    {
        $perPageOptions = [10, 20, 50];
        $perPage = $request->get('per_page', 10);

        $purchases = Purchase::with(['supplier', 'user'])
            ->orderBy('sale_date', 'desc')
            ->paginate($perPage)
            ->appends(['per_page' => $perPage]);

        return view('Reports.purchases', compact('purchases', 'perPageOptions', 'perPage'));
    }

    public function export()
    {
        return Excel::download(new PurchasesExport, 'purchase_report.xlsx');
    }
}
