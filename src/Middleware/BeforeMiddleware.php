<?php namespace AB\Middleware;

use \Tester;
use Illuminate\Http\Request;
use Illuminate\Contracts\Routing\Middleware;
use Closure;

class BeforeMiddleware implements Middleware {

    /**
     * Tracker param
     * @var string
     */
    protected $tester;
    
    /**
     * Constructor.
     *
     * @param SessionInterface $session
     */
    public function __construct(Tester $tester)
    {
        $this->tester = $tester;
    }

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
       $this->app['ab']->track($request);
		return $next($request);
	}

}
