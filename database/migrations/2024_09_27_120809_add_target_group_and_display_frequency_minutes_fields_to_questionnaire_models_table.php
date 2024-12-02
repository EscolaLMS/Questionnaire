<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questionnaire_models', function (Blueprint $table) {
            $table->string('target_group')->nullable();
            $table->unsignedInteger('display_frequency_minutes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('questionnaire_models', function (Blueprint $table) {
            $table->dropColumn([
                'target_group',
                'display_frequency_minutes',
            ]);
        });
    }
};
