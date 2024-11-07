<?php

namespace EscolaLms\Questionnaire\Exports;

use EscolaLms\Questionnaire\Models\QuestionAnswer;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class QuestionnaireExport implements WithMultipleSheets
{
    use Exportable;

    protected Collection $data;
    protected Collection $questions;

    public function __construct(Collection $data, Collection $questions)
    {
        $this->data = $data;
        $this->questions = $questions;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->questions as $question) {
            $answers = $this->data->filter(fn (QuestionAnswer $answer) => $answer->question_id === $question->getKey());

            $headers = $answers->flatMap(function ($answer) {
                return array_keys($answer->toArray());
            })
                ->unique()
                ->filter(fn (string $column) => !in_array($column, ['notes', 'rates', 'answer_dates']));

            $sheets[] = new QuestionnaireExportSheet($answers, $headers, $question->title);
        }

        return $sheets;
    }
}
