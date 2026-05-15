<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function store($item_id){
        $like = Like::where('user_id', Auth::id())->where('item_id', $item_id)->first();

        if(!$like){
            Like::create([
                'user_id' => Auth::id(),
                'item_id' =>$item_id,
            ]);
        }
        else {
            $like->delete();
        }

        return back();
        }

    public function show($id){
        $item = Item::withCount('likes')->findOrFail($id);
        $is_liked = false;
        if (auth()->check()){
            $is_liked = auth()->user()->likedItems()->where('item_id', $id)->exists();
        }
        return view('items.show', compact('item','is_liked'));
    }
}
