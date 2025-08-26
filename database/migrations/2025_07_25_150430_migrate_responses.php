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
        DB::table('responses')->select('uuid', 'question_uuids', 'answers', 'start_time', 'end_time')->orderBy('uuid')->chunk(100, function ($responses) {
            foreach ($responses as $response) {
                $questions = json_decode($response->question_uuids, true) ?? [];
                $answers = json_decode($response->answers, true) ?? [];

                foreach ($questions as $index => $question) {
                    if ($question == '-') {
                        continue;
                    }
                    DB::table('response_questions')->insert([
                        'response_uuid' => $response->uuid,
                        'index' => $index + 1,
                        'question_uuid' => $question,
                        'answer' => $answers[$index] ?? null,
                        'generated_at' => $response->start_time,
                        'last_modified_at' => $response->end_time ?: $response->start_time,
                    ]);
                }
            }
        });
    }
};
