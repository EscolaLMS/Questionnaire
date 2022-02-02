<?php

namespace EscolaLms\Questionnaire\Database\Seeders;

use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Models\QuestionAnswer;
use EscolaLms\Questionnaire\Models\Questionnaire;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database Seeders.
     *
     * @return void
     */
    public function run()
    {
        Questionnaire::factory()
            ->count(2)
            ->create();

        Question::factory()
            ->count(20)
            ->create();

        QuestionAnswer::factory()
            ->count(20)
            ->create();
    }
}
