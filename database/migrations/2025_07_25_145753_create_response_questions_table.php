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
        Schema::create('response_questions', function (Blueprint $table) {
            $table->id();
            $table->string('response_uuid', 36);
            $table->foreign('response_uuid')->references('uuid')->on('responses')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('index');
            $table->string('question_uuid', 36);
            $table->foreign('question_uuid')->references('uuid')->on('questions')->onUpdate('cascade');
            $table->text('answer')->nullable();
            $table->timestamp('generated_at')->useCurrent();
            $table->timestamp('last_modified_at')->nullable();

            $table->unique(['response_uuid', 'index']);
            $table->unique(['response_uuid', 'question_uuid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('response_questions');
    }
};
