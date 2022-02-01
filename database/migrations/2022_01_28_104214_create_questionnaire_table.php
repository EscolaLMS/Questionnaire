<?php

use EscolaLms\Core\Models\User;
use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Models\Questionnaire;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionnaireTable extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('questionnaires')) {
            Schema::create(
                'questionnaires',
                function (Blueprint $table) {
                    $table->id('id');
                    $table->string('title');
                    $table->string('model');
                    $table->integer('model_id');
                    $table->boolean('active')->default(false);
                    $table->timestamps();
                }
            );
        }
        if (!Schema::hasTable('questions')) {
            Schema::create(
                'questions',
                function (Blueprint $table) {
                    $table->id('id');
                    $table->foreignIdFor(Questionnaire::class, 'questionnaire_id');
                    $table->string('title');
                    $table->string('description');
                    $table->integer('position');
                    $table->boolean('active')->default(false);
                    $table->timestamps();
                }
            );
        }
        if (!Schema::hasTable('question_answers')) {
            Schema::create(
                'question_answers',
                function (Blueprint $table) {
                    $table->id('id');
                    $table->foreignIdFor(User::class, 'user_id');
                    $table->foreignIdFor(Question::class, 'questions_id');
                    $table->integer('rate');
                    $table->timestamps();
                }
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('question_answers');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('questionnaires');
    }
}
