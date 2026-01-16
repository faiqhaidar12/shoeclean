<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrdersExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    protected $outletIds;
    protected $month;
    protected $year;

    public function __construct($outletIds, $month = null, $year = null)
    {
        $this->outletIds = $outletIds;
        $this->month = $month ?? now()->month;
        $this->year = $year ?? now()->year;
    }

    /**
     * Return a query builder instead of a collection.
     * This enables chunked processing for large datasets.
     */
    public function query()
    {
        return Order::whereIn('outlet_id', $this->outletIds)
            ->whereMonth('created_at', $this->month)
            ->whereYear('created_at', $this->year)
            ->with(['customer', 'outlet'])
            ->latest();
    }

    public function headings(): array
    {
        return [
            'Invoice',
            'Date',
            'Customer',
            'Outlet',
            'Status',
            'Payment',
            'Total',
        ];
    }

    public function map($order): array
    {
        return [
            $order->invoice_number,
            $order->created_at->format('d/m/Y H:i'),
            $order->customer->name,
            $order->outlet->name,
            ucfirst($order->status),
            ucfirst($order->payment_status),
            $order->total_price,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
