<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAnswerTable extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->string('type')->default('rate');
        });

        Schema::table('question_answers', function (Blueprint $table) {
            $table->string('note', 500)->nullable();
            $table->integer('rate')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('question_answers', function (Blueprint $table) {
            $table->dropColumn('note');
            $table->integer('rate')->nullable(false)->change();
        });
    }
}
