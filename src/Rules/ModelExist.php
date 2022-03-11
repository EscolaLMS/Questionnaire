<?php

namespace EscolaLms\Questionnaire\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

class ModelExist implements Rule
{
    private string $model;
    private string $column;

    public function __construct(string $model, ?string $column = 'id')
    {
        $this->model = $model;
        $this->column = $column;
    }

    public function passes($attribute, $value): bool
    {
        if (is_subclass_of($this->model, Model::class)) {
            $model = new $this->model();
            if ($model::find($value, $this->column)) {
                return true;
            }
        }

        return false;
    }

    public function message(): string
    {
        return 'The :attribute do not exist';
    }
}
