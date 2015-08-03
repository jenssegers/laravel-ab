<?php namespace Jenssegers\AB\Middleware;

use Closure;
use Jenssegers\AB\Tester;

class TrackMiddleware {

    /**
     * AB Tester.
     *
     * @var \Jenssegers\AB\Tester
     */
    protected $ab;

    /**
     * Start A/B tracking middleware.
     *
     */
    public function __construct()
    {
        $this->ab = app('ab');
    }

    /**
     * Handle request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->ab->track($request);

        return $next($request);
    }

}
