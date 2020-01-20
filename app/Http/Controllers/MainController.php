<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MainController extends Controller
{
    public function index(){
        if(view()->exists('main_index')){
            return view('main_index',['content'=>'Main page']);
        }
        abort(404);
    }
}
