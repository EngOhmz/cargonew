<?php

namespace App\Exports;

use App\Models\Customer\CustomerPacel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RevenueReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $query = CustomerPacel::where('company_id', auth()->user()->company_id);

        if ($this->startDate && $this->endDate) {
            $query->whereDate('created_at', '>=', $this->startDate)
                  ->whereDate('created_at', '<=', $this->endDate);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Tarehe',
            'Mtumaji',
            'Mteja',
            'Jina la Mzigo',
            'Route',
            'Bei ya Mzigo',
            'Hela Iliyopokelewa',
            'Jumla',
            'Hela Iliyobaki'
        ];
    }

    public function map($row): array
    {
        return [
            $row->created_at->format('Y-m-d H:i:s'),
            $row->mteja,
            $row->mpokeaji,
            $row->name,
            $row->mzigo_unapotoka . ' - ' . $row->mzigo_unapokwenda,
            $row->bei,
            $row->ela_iliyopokelewa,
            $row->jumla,
            (float)$row->jumla - (float)$row->ela_iliyopokelewa
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
