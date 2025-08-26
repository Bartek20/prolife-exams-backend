<?php

namespace App\Console\Commands;

use App\Models\Response;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RemoveDemoResponses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remove-demo-responses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup DEMO responses';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $responses = Response::whereIn('exam_id', [1, 2])
            ->where('start_time', '<', Carbon::now()->subDay())
            ->get();

        if ($responses->isNotEmpty()) {
            $responseIds = $responses->pluck('id');

            DB::table('personal_access_tokens')
                ->where('tokenable_type', 'App\Models\Response')
                ->whereIn('tokenable_id', $responseIds)
                ->delete();

            $responses->each->delete();
        }
    }
}
