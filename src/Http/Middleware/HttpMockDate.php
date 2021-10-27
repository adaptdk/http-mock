<?php

namespace Adaptdk\HttpMock\Http\Middleware;

use Closure;
use Illuminate\Support\Carbon;

class HttpMockDate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // If the we are in testing mode and the request has sent a mock date header
        // then respect that header and set the test date on the current "request" from phpunit.
        if (request()->hasHeader('X-Mock-Date')) {
            Carbon::setTestNow(request()->header('X-Mock-Date'));
        }

        return $next($request);
    }
}
