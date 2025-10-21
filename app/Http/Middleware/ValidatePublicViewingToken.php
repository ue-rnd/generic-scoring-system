<?php

namespace App\Http\Middleware;

use App\Models\Event;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidatePublicViewingToken
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

        $event = Event::where('public_viewing_token', $token)
            ->with(['contestants', 'criterias', 'rounds', 'judges'])
            ->first();

        if (!$event) {
            abort(403, 'Invalid or expired viewing token');
        }

        // Make event available to the controller
        $request->merge(['event' => $event]);

        return $next($request);
    }
}
