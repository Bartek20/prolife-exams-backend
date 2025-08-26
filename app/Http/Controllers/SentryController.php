<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SentryController extends Controller
{
    function handle(Request $request) {
        $known_sentry_hosts = ["o4509185227554816.ingest.de.sentry.io"];
        $known_project_ids = ['4509128097595392', '4509185257766992']; // Laravel (Backend), Angular (Frontend)

        $envelope = $request->getContent();
        $pieces = explode("\n", $envelope, 2);
        $header = json_decode($pieces[0], true);

        if (!isset($header['dsn'])) {
            return response()->json(['error' => 'Invalid DSN'], 400);
        }

        $dsn = parse_url($header['dsn']);
        $sentry_host = $dsn['host'];
        $project_id = intval(trim($dsn['path'], '/'));

        if (!in_array($sentry_host, $known_sentry_hosts)) {
            return response()->json(['error' => 'Invalid Sentry host'], 400);
        }
        if (!in_array($project_id, $known_project_ids)) {
            return response()->json(['error' => 'Invalid project ID'], 400);
        }

        return Http::withBody($envelope, "application/x-sentry-envelope")
            ->withHeaders([
                'X-Forwarded-For' => $request->ip(),
                'X-Sentry-Forwarded-For' => $request->ip(),
            ])
            ->post("https://$sentry_host/api/$project_id/envelope/");
    }
}
