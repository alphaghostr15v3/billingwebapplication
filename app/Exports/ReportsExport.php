<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Collection;

class ReportsExport implements FromCollection, WithHeadings, WithTitle
{
    protected $data;
    protected $title;
    protected $headings;

    public function __construct(Collection $data, string $title, array $headings)
    {
        $this->data = $data;
        $this->title = $title;
        $this->headings = $headings;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function title(): string
    {
        return $this->title;
    }
}
