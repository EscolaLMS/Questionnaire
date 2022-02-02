<?php

use EscolaLms\Core\Models\User;
use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Models\QuestionnaireModel;
use EscolaLms\Questionnaire\Models\QuestionnaireModelType;
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
                    $table->boolean('active')->default(false);
                    $table->timestamps();
                }
            );
        }
        if (!Schema::hasTable('questionnaire_model_types')) {
            Schema::create(
                'questionnaire_model_types',
                function (Blueprint $table) {
                    $table->id('id');
                    $table->string('title');
                    $table->string('modelable_class')->unique();
                    $table->timestamps();
                }
            );
        }
        if (!Schema::hasTable('questionnaire_models')) {
            Schema::create(
                'questionnaire_models',
                function (Blueprint $table) {
                    $table->id('id');
                    $table->foreignIdFor(Questionnaire::class, 'questionnaire_id');
                    $table->foreignIdFor(QuestionnaireModelType::class, 'modelable_type_id');
                    $table->integer('modelable_id');
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
                    $table->foreignIdFor(Question::class, 'question_id');
                    $table->foreignIdFor(QuestionnaireModel::class, 'questionnaire_model_id');
                    $table->integer('rate');
                    $table->timestamps();
                }
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('question_answers');
        Schema::dropIfExists('questionnaire_models');
        Schema::dropIfExists('questionnaire_model_types');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('questionnaires');
    }
}
