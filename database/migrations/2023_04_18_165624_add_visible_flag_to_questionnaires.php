<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVisibleFlagToQuestionnaires extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->boolean('public_answers')->default(false);
        });

        Schema::table('question_answers', function (Blueprint $table) {
            $table->boolean('visible_on_front')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('public_answers');
        });

        Schema::table('question_answers', function (Blueprint $table) {
            $table->dropColumn('visible_on_front');
        });
    }
}
