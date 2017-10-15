<?php

namespace App\Http\Controllers\Views;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PagesController extends Controller
{
    //
    public function home(){
      return view('pages.home');
    }

    public function help(){
      return view('pages.help');
    }

    public function about(){
      return view('pages.about');
    }
}
