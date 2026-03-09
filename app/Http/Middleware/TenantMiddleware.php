<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->business_id) {
            $business = \App\Models\Business::find($user->business_id);
            
            if (!$business) {
                \Illuminate\Support\Facades\Log::error("TenantMiddleware: Business not found for user ID: {$user->id}");
                abort(403, 'Unauthorized: Business association missing.');
            }

            if ($business->status !== 'active') {
                abort(403, 'Your account is suspended. Please contact support.');
            }

            // Switch connection
            \App\Services\TenantDatabaseService::switchToTenant($business->database_name);
            
            // Log for debugging
            \Illuminate\Support\Facades\Log::info("TenantMiddleware: Switched to database {$business->database_name} for user {$user->email}");
        }

        return $next($request);
    }
}
