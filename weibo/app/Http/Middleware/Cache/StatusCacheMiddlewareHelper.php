<?php

namespace App\Http\Middleware\Cache;

use Auth;
use Redis;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Mockery\Exception;

class StatusCacheMiddlewareHelper {
  protected static function getStatusCacheKey($request,$key){
    $user = Auth::user();
    $key = str_replace('id',$user->id,$key);
    $page = $request->input('page');
    if($page == null) {
      $page = 1;
      $key = str_replace('num',$page,$key);
    }
    return $key;
  }

  public static function userStatusCacheHandler($action,$key,$request){
    $key = self::getStatusCacheKey($request,$key);
    $user = Auth::user();
    switch ($action){
      case 'show':
        if(!Cache::has($key)){
          $statuses = $user->statuses()
                           ->orderBy('created_at','desc')
                           ->paginate(30);
          $expiresAt = Carbon::now()->addMinutes(1*3600);
          Cache::put($key ,$statuses, $expiresAt);
          return true;
        }
        break;
      default:
        break;
    }
  }

  public static function statusCacheHandler($action,$key,$request){
    switch ($action){
      case 'store':
        self::clearStatusCache();
        return true;
      case 'destroy':
        self::clearStatusCache();
        return true;
      default:
        break;
    }
  }

  protected static function clearStatusCache(){
    $user = Auth::user();
    $redis = Redis::connection('for-cache');
    $keys = $redis->keys("*user_".$user->id."_statuses*");
    foreach ($keys as $key) {
      $redis->del($key);
    }
  }
}