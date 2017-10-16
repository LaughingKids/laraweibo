<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Auth;
use Cache;
use Carbon\Carbon;

class SessionsController extends Controller
{
    //
    public function __construct(){
      $this->middleware('guest',[
        'only' => ['create']
      ]);
    }
    public function create(){
      return view('sessions.create');
    }

    public function store(Request $request){
      $this->validate($request, [
           'email' => 'required|email|max:255',
           'password' => 'required'
       ]);

       $credentials = [
           'email'    => $request->email,
           'password' => $request->password,
       ];

       if (Auth::attempt($credentials,$request->has('remember'))) {
           if(Auth::user()->activated) {
             session()->flash('success', 'Welcome Back！');
             $user = Auth::user();
             $expiresAt = Carbon::now()->addMinutes(1*3600*24);
             if(!Cache::has('user_'.$user->id.'_profile')) {
               Cache::put('user_'.$user->id , $user , $expiresAt);
             }
             return redirect()->route('users.show', [Auth::user()]);
           } else {
             Auth:logout();
             session()->flash('warning','Your account is required to be activated, please check your email box to active your account.');
             return redirect('/');
           }
       } else {
         session()->flash('danger', 'Sorry, username and password are not correct, please check your input and try again.');
         return redirect()->back();
       }
       return;
    }

    public function destroy(Request $request){
      Auth::logout();
      session()->flash('success', 'You has logged out.');
      return redirect('login');
    }
}
