<?php

namespace App\Exports;

use App\Models\SaleItem;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;

class ProfitReportExport implements FromCollection, WithHeadings
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $query = SaleItem::with('product', 'sale');

        if ($this->startDate && $this->endDate) {
            $query->whereHas('sale', function ($q) {
                $q->whereBetween('sale_date', [$this->startDate, $this->endDate]);
            });
        }

        $saleItems = $query->get();

        return $saleItems->map(function ($item) {
            $purchasePrice = $item->product->purchase_price;
            $sellingPrice = $item->product->selling_price;
            $quantity = $item->quantity;
            $profit = ($sellingPrice - $purchasePrice) * $quantity;

            return [
                'Product Name' => $item->product->name,
                'Quantity' => $quantity,
                'Purchase Price' => $purchasePrice,
                'Selling Price' => $sellingPrice,
                'Profit' => $profit,
                'Subtotal' => $item->sub_total,
                'Sale Date' => Carbon::parse($item->sale->sale_date)->format('Y-m-d'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Product Name',
            'Quantity',
            'Purchase Price',
            'Selling Price',
            'Profit',
            'Subtotal',
            'Sale Date',
        ];
    }
}
