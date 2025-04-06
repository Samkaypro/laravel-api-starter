<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiRateLimit
{
    /**
     * The rate limiter instance.
     *
     * @var RateLimiter
     */
    protected RateLimiter $limiter;

    /**
     * Create a new middleware instance.
     *
     * @param RateLimiter $limiter
     * @return void
     */
    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param int $maxAttempts
     * @param int $decayMinutes
     * @return Response
     */
    public function handle(Request $request, Closure $next, int $maxAttempts = 60, int $decayMinutes = 1): Response
    {
        // Determine the rate limit key - use user id if authenticated, otherwise use IP address
        $key = $request->user() ? 'api:' . $request->user()->id : 'api:' . $request->ip();

        // Check if the request has hit the rate limit
        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            return response()->json([
                'success' => false,
                'message' => 'Too many requests. Please try again later.',
                'retry_after' => $this->limiter->availableIn($key),
            ], 429);
        }

        // Increment the rate limiter
        $this->limiter->hit($key, $decayMinutes * 60);

        // Get the response
        $response = $next($request);

        // Add rate limit headers to the response
        $response->headers->add([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => $maxAttempts - $this->limiter->attempts($key) - 1,
            'X-RateLimit-Reset' => $this->limiter->availableIn($key),
        ]);

        return $response;
    }
} 