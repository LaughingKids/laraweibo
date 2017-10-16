<?php

namespace App\Http\Controllers\Views;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use App\Models\User;
use Mail;
use Auth;
use Cache;
use Carbon\Carbon;

class UsersController extends Controller
{
  public function __construct(){
    $this->middleware('auth',[
      'except' => ['show', 'create', 'store','index','confirmEmail']
    ]);
    $this->middleware('guest', [
        'only' => ['create']
    ]);

    $this->middleware('cache',[
       'only'=> ['show']
    ]);
  }
  public function create(){
    return view('users.create');
  }

  public function show(Request $request,User $user){
    $page = $request->input('page');
    if($page == null) {
      $page = 1;
    }
    if(Cache::has('user_'.$user->id.'_statuses_page_'.$page)){
      $statuses = Cache::get('user_'.$user->id.'_statuses_page_'.$page);
    } else {
      $statuses = $user->statuses()
                       ->orderBy('created_at','desc')
                       ->paginate(30);
      $expiresAt = Carbon::now()->addMinutes(1*3600);
      Cache::put('user_'.$user->id.'_statuses_page_'.$page ,$statuses,$expiresAt);
    }
    return view('users.show',compact('user','statuses'));
  }

  public function index(){
    $users = User::paginate(10);
    return view('users.index', compact('users'));
  }

  public function edit($id) {
    $key = 'user_'.$id;
    $user  = Cache::get($key);
    $this->authorize('update', $user);
    return view('users.edit', compact('user'));
  }

  public function store(Request $request) {
     $this->validate($request, [
         'name' => 'required|max:50',
         'email' => 'required|email|unique:users|max:255',
         'password' => 'required|confirmed|min:6'
     ]);

     $user = User::create([
       'name' => $request->name,
       'email' => $request->email,
       'password' => bcrypt($request->password),
     ]);

     $this->sendEmailConfirmationTo($user);
     session()->flash('success', 'Please check your mail box to get activation email.');
     return redirect('/');
  }

  public function update(User $user, Request $request) {
      $this->validate($request, [
          'name' => 'required|max:50',
          'password' => 'required|confirmed|min:6'
      ]);

      $data = [];
      $data['name'] = $request->name;
      if ($request->password) {
          $data['password'] = bcrypt($request->password);
      }
      $user->update($data);
      session()->flash('success', 'Your profile has been updated successfully.');
      return redirect()->route('users.show', $user->id);
  }

  public function destroy(User $user) {
      $this->authorize('destroy', $user);
      $user->delete();
      session()->flash('success', 'Account has been deleted.');
      return back();
  }

  protected function sendEmailConfirmationTo($user) {
    $view = 'emails.confirm';
    $data = compact('user');
    $from = 'developer.wxj@gmail.com';
    $name ='Aufree';
    $to = $user->email;
    $subject = "We appreciate your registration, please check your email box to active your account.";
    Mail::send($view,$data,function($message) use ($from,$name,$to,$subject){
      $message->from($from,$name)->to($to)->subject($subject);
    });
  }

  public function confirmEmail($token) {
    $user = User::where('activation_token',$token)->firstOrFail();

    $user->activated = true;
    $user->activation_token = null;
    $user->save();

    Auth::login($user);
    session()->flash('success','Your account has been activated successfully.');
    return redirect()->route('users.show',[$user]);
  }

  public function sendPasswordResetNotification($token) {
      $this->notify(new ResetPassword($token));
  }

  public function followings(User $user) {
      $users = $user->followings()->paginate(30);
      $title = 'Following';
      return view('users.show_follow', compact('users', 'title'));
  }

  public function followers(User $user) {
      $users = $user->followers()->paginate(30);
      $title = 'Follower';
      return view('users.show_follow', compact('users', 'title'));
  }
}
