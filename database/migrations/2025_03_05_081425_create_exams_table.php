<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Egzamin');
            $table->string('access_code')->unique();
            $table->timestamp('start_time')->default(now());
            $table->timestamp('end_time')->nullable();
            $table->char('duration', 8)->default('00:30:00');
            $table->unsignedTinyInteger('question_number')->default(20);
            $table->unsignedTinyInteger('passing_score')->default(15);
            $table->boolean('can_go_back')->default(true);
            $table->boolean('is_global_duration')->default(true);
            $table->boolean('show_results')->default(false);
            $table->binary('public_key', 32);
            $table->binary('private_key', 64);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
