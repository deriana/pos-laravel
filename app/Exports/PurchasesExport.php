<?php

namespace App\Exports;

use App\Models\Purchase;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PurchasesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Purchase::with(['supplier', 'user'])->get()->map(function($purchase) {
            return [
                'ID' => $purchase->id,
                'Supplier' => $purchase->supplier->name ?? '-',
                'User' => $purchase->user->name ?? '-',
                'Purchase Date' => $purchase->sale_date,
                'Invoice Number' => $purchase->invoice_number,
                'Total' => $purchase->total,
                'Discount' => $purchase->discount,
                'Tax' => $purchase->tax,
                'Grand Total' => $purchase->grand_total,
                'Profit' => $purchase->grand_total - $purchase->total,
                'Payment Status' => ucfirst($purchase->payment_status),
                'Note' => $purchase->note,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Supplier',
            'User',
            'Purchase Date',
            'Invoice Number',
            'Total',
            'Discount',
            'Tax',
            'Grand Total',
            'Profit',
            'Payment Status',
            'Note',
        ];
    }
}
