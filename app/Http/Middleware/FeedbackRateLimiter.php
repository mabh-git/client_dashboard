<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class FeedbackRateLimiter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si la limitation de taux est activée
        if (!config('feedback.rate_limit.enabled', true)) {
            return $next($request);
        }

        // Obtenir l'adresse IP du client
        $ip = $request->ip();
        
        // Définir la limite par jour
        $maxPerDay = config('feedback.rate_limit.max_per_day', 5);
        
        // Vérifier si l'IP a dépassé la limite
        $key = 'feedback_limit_' . $ip;
        $attempts = RateLimiter::attempt(
            $key,
            $maxPerDay,
            function() {},
            60 * 60 * 24 // 24 heures
        );
        
        if (!$attempts) {
            return response()->json([
                'message' => 'Limite de feedbacks atteinte. Veuillez réessayer plus tard.',
                'error' => 'rate_limit_exceeded'
            ], 429);
        }
        
        return $next($request);
    }
}