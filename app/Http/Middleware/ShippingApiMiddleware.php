<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\RateLimiter;
use App\Exceptions\ShippingException;

class ShippingApiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if rate limiting is enabled
        if (Config::get('shipping.security.rate_limit.enabled', true)) {
            $this->checkRateLimit($request);
        }

        // Check IP restrictions
        if (Config::get('shipping.security.ip_restrictions.enabled', false)) {
            $this->checkIpRestrictions($request);
        }

        // Add security headers
        $response = $next($request);
        $this->addSecurityHeaders($response);

        return $response;
    }

    /**
     * Check rate limiting
     */
    protected function checkRateLimit(Request $request)
    {
        $key = 'shipping_api:' . $request->ip();
        $maxAttempts = Config::get('shipping.security.rate_limit.max_attempts', 60);
        $decayMinutes = Config::get('shipping.security.rate_limit.decay_minutes', 1);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            throw new ShippingException(
                'Too many requests. Please try again in ' . $seconds . ' seconds.',
                ['retry_after' => $seconds],
                429
            );
        }

        RateLimiter::hit($key, $decayMinutes * 60);
    }

    /**
     * Check IP restrictions
     */
    protected function checkIpRestrictions(Request $request)
    {
        $allowedIps = Config::get('shipping.security.ip_restrictions.allowed_ips', []);
        
        if (!empty($allowedIps) && !in_array($request->ip(), $allowedIps)) {
            throw new ShippingException(
                'Access denied. Your IP address is not authorized.',
                ['ip' => $request->ip()],
                403
            );
        }
    }

    /**
     * Add security headers to response
     */
    protected function addSecurityHeaders($response)
    {
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        $response->headers->set('Content-Security-Policy', "default-src 'self'");
    }
} 