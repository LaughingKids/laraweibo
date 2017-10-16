<?php

namespace App\Http\Middleware\Cache;

use Closure;
use App\Http\Middleware\Cache\StatusCacheMiddlewareHelper;


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
    }

    public function __construct() {
        $this->initCachedActions();
    }

    protected function initCachedActions(){
      $this->cachedActions = collect();
      $controllers = config('cache.cached_controller');
      foreach($controllers as $controller){
        $this->cachedActions->put($controller['class'],collect());
      }
      return $this;
    }

    protected function getResponse(Closure $next){
      if (!$this->isCached()) return $next($this->request);
      list($controller, $action) = explode('@', $this->request->route()->getActionName());
      $controllers = config('cache.cached_controller');
      switch ($controller) {
        case $controllers[0]['class']:
          $cached = StatusCacheMiddlewareHelper::userStatusCacheHandler($action,$controllers[0]['key'],$this->request);
          break;
        case $controllers[1]['class']:
          $cached = StatusCacheMiddlewareHelper::statusCacheHandler($action,$controllers[0]['key'],$this->request);
          break;
      }
      return $next($this->request);
    }

    protected function isCached(){
      if(app()->environment('local')) return true;
      return $this->checkRoute();
    }

    protected function checkRoute(){
      list($controller, $action) = explode('@', $this->request->route()->getActionName());
      $cachedController = $this->cachedActions->get($controller, false);
      if($cachedController === false) return false;
      if($cachedController->isEmpty()) return true;
      return !! $cachedController->get($action, false);
    }
}
