<?php

namespace App\Http\Middleware;

use App\Models\EventJudge;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateJudgeToken
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->route('token');
        
        if (!$token) {
            abort(403, 'Invalid access token');
        }

        $eventJudge = EventJudge::where('judge_token', $token)
            ->with(['event', 'judge'])
            ->first();

        if (!$eventJudge) {
            abort(403, 'Invalid or expired judge token');
        }

        // Make eventJudge available to the controller
        $request->merge(['eventJudge' => $eventJudge]);

        return $next($request);
    }
}
