<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MypageController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $page =$request->query('page', 'sell');
        if($page === 'buy'){
            $items = $user->boughtItems ?? collect();
        }
        else {
            $items = $user->items ?? collect();
        }
        return view('mypage.index' , compact('user', 'items', 'page'));
    }
}
