<?php

namespace App\Http\Controllers\Views;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class PagesController extends Controller
{
    //
    public function home(){
      $feed_items = [];
      if (Auth::check()) {
          $feed_items = Auth::user()->feed()->paginate(30);
      }
      return view('pages.home',compact('feed_items'));
    }

    public function help(){
      return view('pages.help');
    }

    public function about(){
      return view('pages.about');
    }
}
