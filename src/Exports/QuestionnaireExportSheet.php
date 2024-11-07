<?php

namespace EscolaLms\Questionnaire\Exports;

use EscolaLms\Questionnaire\Models\QuestionAnswer;
use EscolaLms\Questionnaire\Models\Questionnaire;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class QuestionnaireExportSheet implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    use Exportable;

    private Collection $collection;
    private array $headers;
    private string $title;

    public function __construct(Collection $collection, array|Collection $headers, string $title)
    {
        $this->collection = $collection;
        $this->headers = $headers instanceof Collection ? $headers->toArray() : $headers;
        $this->title = $title;
    }
    public function collection()
    {
        return $this->collection;
    }

    public function headings(): array
    {
        return $this->headers;
    }

    public function map($row): array
    {
        $result = [];

        foreach ($this->headers as $header) {
            $result[] = $row->{$header} ?? null;
        }

        return $result;
    }

    public function title(): string
    {
        return $this->title;
    }
}
