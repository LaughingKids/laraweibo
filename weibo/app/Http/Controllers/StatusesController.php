<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Statuses;

class StatusesController extends Controller
{
  public function __construct()
  {
      $this->middleware('auth');
      $this->middleware('cache',[
        'only'=> ['store','destroy']
      ]);
  }

  public function store(Request $request) {
    $this->validate($request, [
        'content' => 'required|max:140'
    ]);

    Auth::user()->statuses()->create([
        'content' => $request->content
    ]);

    return redirect()->back();
  }

  public function destroy(Statuses $status) {
    $this->authorize('destroy', $status);
    $status->delete();
    session()->flash('success', 'The status has been deleted successfully.');
    return redirect()->back();
  }
}
