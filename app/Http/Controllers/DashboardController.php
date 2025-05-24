<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{


    public function showDashboard()
    {

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();    

        $salesWeek = Sale::select(DB::raw('DAYNAME(created_at) as day_name'), DB::raw('SUM(grand_total) as total'))
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->groupBy('day_name')
            ->get()
            ->keyBy('day_name');

        $weekLabels = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $weekSalesData = [];
        foreach ($weekLabels as $day) {
            $weekSalesData[] = $salesWeek->has($day) ? (int)$salesWeek[$day]->total : 0;
        }

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $salesMonth = Sale::select(DB::raw('FLOOR((DAY(created_at)-1)/7)+1 as week_number'), DB::raw('SUM(grand_total) as total'))
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->groupBy('week_number')
            ->orderBy('week_number')
            ->get()
            ->keyBy('week_number');

        $monthSalesData = [];
        for ($i = 1; $i <= 5; $i++) {
            $monthSalesData[] = $salesMonth->has($i) ? (int)$salesMonth[$i]->total : 0;
        }

        $startOfYear = Carbon::now()->startOfYear();
        $endOfYear = Carbon::now()->endOfYear();

        $salesYear = Sale::select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(grand_total) as total'))
            ->whereBetween('created_at', [$startOfYear, $endOfYear])
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $yearSalesData = [];
        for ($i = 1; $i <= 12; $i++) {
            $yearSalesData[] = $salesYear->has($i) ? (int)$salesYear[$i]->total : 0;
        }

        $products = Products::paginate(5);
        $sales = Sale::orderBy('grand_total')->paginate(5);

        $topSales = SaleItem::select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('product_id')
            ->pluck('total_sold', 'product_id');

        $products->setCollection(
            $products->getCollection()->map(function ($product) use ($topSales) {
                $totalSold = $topSales->get($product->id, 0);

                if ($product->stock < 5) {
                    $type = 'low';
                } elseif ($totalSold > 0) {
                    $type = 'top';
                } else {
                    $type = 'all';
                }

                $product->total_sold = $totalSold;
                $product->type = $type;

                return $product;
            })
        );

        $totalProducts = $products->count();
        $lowStockProducts = $products->where('stock', '<', 5)->count();

        $profitNow = Sale::whereDate('created_at', Carbon::today())->sum('grand_total');
        $profitYesterday = Sale::whereDate('created_at', Carbon::yesterday())->sum('grand_total');

        if ($profitYesterday == 0) {
            $percentChange = $profitNow > 0 ? 100 : 0;
        } else {
            $percentChange = (($profitNow - $profitYesterday) / $profitYesterday) * 100;
        }
        $percentChangeFormatted = round($percentChange);

        $saleTransactionsToday = Sale::whereDate('created_at', Carbon::today())->count();
        $purchaseTransactionsToday = Purchase::whereDate('created_at', Carbon::today())->count();

        return view('Dashboard.index', compact(
            'totalProducts',
            'lowStockProducts',
            'profitNow',
            'profitYesterday',
            'percentChangeFormatted',
            'saleTransactionsToday',
            'purchaseTransactionsToday',
            'products',
            'sales',
            'weekSalesData',
            'monthSalesData',
            'yearSalesData',
        ));
    }
}
