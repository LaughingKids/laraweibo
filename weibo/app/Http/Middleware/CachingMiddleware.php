<?php

namespace App\Http\Middleware;

use App\Http\Controllers\StatusesController;
use Closure;

class CachingMiddleware
{

    /**
     * @var Collection
     */
    protected $cachedActions;

    /**
     * @var int
     */
    protected $lifeTime = 120;

    /**
     * @var Request
     */
    protected $request;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $this->request = $request;
        return $this->getResponse($next);
//        return $next($request);
    }

    public function __construct() {
        $this->initCachedActions();
    }

    protected function initCachedActions(){
      $this->cachedActions = collect();
      $controllers = config('cache.cached_controller');
      foreach($controllers as $controller){
        $this->cachedActions->put($controller,collect());
      }
      return $this;
    }

    protected function getResponse(Closure $next){
      if (!$this->isCached()) return $next($this->request);
      $cacheKey = $this->request->getPathInfo();
      if(!\Cache::has($cacheKey)) {
        $response = $next($this->request);
        $response->original = '';
        \Cache::put($cacheKey, $response, $this->lifeTime);
      }
    }

    protected function isCached(){
//      if(app()->environment('local')) return false;
      return $this->checkRoute();
    }
    protected function checkRoute(){
      list($controller, $action) = explode('@', $this->request->route()->getActionName());
      var_dump($this->cachedActions);
      var_dump($controller, $action);
      die();
    }
}
