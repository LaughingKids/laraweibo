<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Auth;

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
           // 该用户存在于数据库，且邮箱和密码相符合
           if(Auth::user()->activated) {
             session()->flash('success', '欢迎回来！');
             $user = Auth::user();
             Redis::set('user:profile:'.$user->id, $user);
             return redirect()->route('users.show', [Auth::user()]);
           } else {
             Auth:logout();
             session()->flash('warning','你的账号未激活，请检查邮箱中的注册邮件进行激活。');
             return redirect('/');
           }
       } else {
         session()->flash('danger', '很抱歉，您的邮箱和密码不匹配');
         return redirect()->back();
       }
       return;
    }

    public function destroy(){
      $user = Auth::user();
      Redis::del('user:profile:'.$user->id, $user);
      Auth::logout();
      session()->flash('success', '您已成功退出！');
      return redirect('login');
    }
}
