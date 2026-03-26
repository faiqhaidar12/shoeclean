<?php

namespace App\Exports;

use App\Models\Expense;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExpensesExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnFormatting, ShouldAutoSize
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
        return Expense::whereIn('outlet_id', $this->outletIds)
            ->whereMonth('expense_date', $this->month)
            ->whereYear('expense_date', $this->year)
            ->with(['outlet', 'user'])
            ->latest('expense_date');
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Kategori',
            'Deskripsi',
            'Outlet',
            'Dicatat Oleh',
            'Nominal',
        ];
    }

    public function map($expense): array
    {
        return [
            $expense->expense_date->format('d/m/Y'),
            $expense->category,
            $expense->description ?? '-',
            $expense->outlet->name ?? '-',
            $expense->user->name ?? '-',
            $expense->amount,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => 'B42318'],
                ],
            ],
        ];
    }
}
