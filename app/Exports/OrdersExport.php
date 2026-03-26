<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrdersExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnFormatting, ShouldAutoSize
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
            'Tanggal Order',
            'Pelanggan',
            'Outlet',
            'Status Order',
            'Metode Pembayaran',
            'Status Pembayaran',
            'Tipe Order',
            'Diskon',
            'Total Order',
        ];
    }

    public function map($order): array
    {
        return [
            $order->invoice_number,
            $order->created_at->format('d/m/Y H:i'),
            $order->customer->name ?? '-',
            $order->outlet->name ?? '-',
            ucfirst(str_replace('_', ' ', $order->status)),
            $order->paymentMethodLabel(),
            $order->paymentStatusLabel(),
            $order->order_type ? ucfirst(str_replace('_', ' ', $order->order_type)) : '-',
            $order->discount_amount ?? 0,
            $order->total_price,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => '1E3A34'],
                ],
            ],
        ];
    }
}
