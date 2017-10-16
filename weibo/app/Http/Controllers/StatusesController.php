<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Status;
use Auth;
use Cache;
use Redis;

class StatusesController extends Controller
{
  public function __construct()
  {
      $this->middleware('auth');
  }

  public function store(Request $request) {
    $this->validate($request, [
        'content' => 'required|max:140'
    ]);

    Auth::user()->statuses()->create([
        'content' => $request->content
    ]);
    
    $user = Auth::user();
    $redis = Redis::connection('for-cache');
    $keys = $redis->keys("*user_".$user->id."_statuses*");
    foreach ($keys as $key) {
      $redis->del($key);
    }
    return redirect()->back();
  }
  public function destroy(Status $status) {
    $this->authorize('destroy', $status);
    $status->delete();
    session()->flash('success', 'The status has been deleted successfully.');
    return redirect()->back();
  }
}
