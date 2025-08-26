<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ExamResultMiddleware extends RequestBase {
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response {
        $user = Auth::guard('sanctum')->user();
        $uuid = $request->route('uuid');

        $mode = $user?->getTable();

        switch ($mode) {
            case 'users':
                $response = \App\Models\Response::where('uuid', $uuid)->first();
                if ($user->role !== 'teacher' && $response->student_email !== $user->email) {
                    return $this->fail('Unauthorized', 403);
                }
                break;
            case 'responses':
                $response = $user;
                if ($uuid != $response->uuid) {
                    return $this->fail('Requested UUID does not match token.');
                }
                break;
            default:
                $response = \App\Models\Response::where('uuid', $uuid)->first();
        }

        if (!$response) {
            return $this->fail('Response not found', 404);
        }

        $exam = $response->exam;

        if ($mode === 'responses') {
            if ($response->status == 'in_progress') {
                return $this->fail('Exam has not ended', 403);
            }

            if (!$exam->show_results) {
                return $this->fail('Results are not available', 403);
            }
        }

        if ($mode !== 'users' && !$user?->currentAccessToken()->can('exam_result')) {
            return $this->fail('Unauthorized', 401);
        }

        if ($mode === 'users' && $user->role !== 'teacher') {
            return $this->fail('Unauthorized', 401);
        }

        $request->attributes->add([
            'exam' => $exam,
            'response' => $response,
        ]);

        return $next($request);
    }
}
