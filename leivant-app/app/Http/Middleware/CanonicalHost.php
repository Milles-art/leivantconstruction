<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanonicalHost
{
    public function handle(Request $request, Closure $next): Response
    {
        $canonicalHost = parse_url(config('app.url', 'https://leivantconstruction.com'), PHP_URL_HOST) ?: 'leivantconstruction.com';
        $host = $request->getHost();

        if (strcasecmp($host, 'www.'.$canonicalHost) === 0) {
            $scheme = parse_url(config('app.url', 'https://leivantconstruction.com'), PHP_URL_SCHEME) ?: 'https';

            return redirect()->away($scheme.'://'.$canonicalHost.$request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
