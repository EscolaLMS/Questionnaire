<?php

use EscolaLms\Core\Models\User;
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
                    $table->uuid('id')->primary();
                    $table->string('title');
                    $table->string('model');
                    $table->integer('model_id');
                    $table->boolean('active')->default(false);
                    $table->timestamps();
                }
            );
        }
        if (!Schema::hasTable('question')) {
            Schema::create(
                'question',
                function (Blueprint $table) {
                    $table->uuid('id')->primary();
                    $table->morphs('questionnaires');
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
                    $table->uuid('id')->primary();
                    $table->foreignIdFor(User::class, 'user_id');
                    $table->morphs('questions');
                    $table->string('rate');
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
