<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesReportExport implements FromCollection, WithHeadings
{
    protected $perPage;

    public function __construct($perPage = 10)
    {
        $this->perPage = $perPage;
    }

    public function collection()
    {
        return Sale::with(['user', 'customer'])
            ->select('id', 'user_id', 'customer_id', 'sale_date', 'invoice_number', 'total', 'discount', 'tax', 'grand_total', 'payment_status', 'note')
            ->get()
            ->map(function ($sale) {
                return [
                    'ID' => $sale->id,
                    'User' => $sale->user->name ?? '',
                    'Customer' => $sale->customer->name ?? '',
                    'Sale Date' => $sale->sale_date,
                    'Invoice Number' => $sale->invoice_number,
                    'Total' => $sale->total,
                    'Discount' => $sale->discount,
                    'Tax' => $sale->tax,
                    'Grand Total' => $sale->grand_total,
                    'Payment Status' => $sale->payment_status,
                    'Note' => $sale->note,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'User',
            'Customer',
            'Sale Date',
            'Invoice Number',
            'Total',
            'Discount',
            'Tax',
            'Grand Total',
            'Payment Status',
            'Note',
        ];
    }
}
