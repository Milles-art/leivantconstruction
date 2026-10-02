<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldCachePublicPage($request)) {
            $cacheKey = 'leivant.public_page.'.sha1($request->getSchemeAndHttpHost().'/'.trim($request->path(), '/'));
            $cached = Cache::get($cacheKey);

            if (is_array($cached) && isset($cached['content'])) {
                $response = response($cached['content'], 200, [
                    'Content-Type' => $cached['content_type'] ?? 'text/html; charset=UTF-8',
                    'X-Leivant-Cache' => 'HIT',
                ]);

                return $this->withHeaders($request, $response, true);
            }
        }

        $response = $next($request);

        if (isset($cacheKey) && $response->getStatusCode() === 200 && str_contains((string) $response->headers->get('Content-Type'), 'text/html')) {
            Cache::put($cacheKey, [
                'content' => $response->getContent(),
                'content_type' => $response->headers->get('Content-Type'),
            ], now()->addMinutes(10));
            $response->headers->set('X-Leivant-Cache', 'MISS');
        }

        return $this->withHeaders($request, $response, false);
    }

    private function withHeaders(Request $request, Response $response, bool $cached): Response
    {
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(self), payment=(self)');

        if ($cached || $this->shouldCachePublicPage($request)) {
            $response->headers->set('Cache-Control', 'public, max-age=120, stale-while-revalidate=300');
            $response->headers->remove('Set-Cookie');
        }

        if ($request->isSecure() && app()->isProduction()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }

    private function shouldCachePublicPage(Request $request): bool
    {
        if (! $request->isMethod('GET') || $request->ajax() || $request->query()) {
            return false;
        }

        $sessionCookie = (string) config('session.cookie');
        if ($sessionCookie !== '' && $request->cookies->has($sessionCookie)) {
            return false;
        }

        return in_array(trim($request->path(), '/'), ['', 'about', 'projects', 'privacy', 'terms'], true);
    }
}
