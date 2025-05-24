<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use App\Exports\SalesReportExport;
use Maatwebsite\Excel\Facades\Excel;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        $perPageOptions = [10, 20, 50];
        $perPage = $request->input('per_page', 10);
        if (!in_array($perPage, $perPageOptions)) {
            $perPage = 10;
        }

        $sales = Sale::with(['user', 'customer'])
            ->orderBy('sale_date', 'desc')
            ->paginate($perPage)
            ->appends(['per_page' => $perPage]);

        return view('Reports.sales', compact('sales', 'perPage', 'perPageOptions'));
    }

    public function export(Request $request)
    {
        return Excel::download(new SalesReportExport(), 'sales_report.xlsx');
    }
}
