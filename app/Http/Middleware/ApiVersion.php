<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiVersion
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string $version
     * @return Response
     */
    public function handle(Request $request, Closure $next, string $version): Response
    {
        // Check if the request has an Accept-Version header
        $headerVersion = $request->header('Accept-Version');
        
        // If the request has an Accept-Version header and it doesn't match the current version
        if ($headerVersion && $headerVersion !== $version) {
            // If the header version is higher than our current supported version
            if (version_compare($headerVersion, $version, '>')) {
                return response()->json([
                    'success' => false,
                    'message' => 'This version is not yet supported. Please use version ' . $version . ' or earlier.',
                ], 400);
            }
            
            // If the header version is lower, we should redirect to that version
            // In this case, we'll just continue, as the routing would have already directed to the correct version
        }
        
        // Add the API version to the response headers
        $response = $next($request);
        $response->headers->set('API-Version', $version);
        
        // If this is a deprecated version, set a warning header
        if ($version !== config('api.latest_version')) {
            $response->headers->set(
                'Warning',
                '299 - "Deprecated API Version: This version of the API will be deprecated soon. Please migrate to the latest version ' . config('api.latest_version') . '."'
            );
        }
        
        return $response;
    }
} 