<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{
    public function store(Request $request, $item_id) {
        $comment = new Comment();

        if(auth()->check()){
            $comment->user_id = auth()->id();
        }

        $comment->item_id = $item_id;
        $comment->comment = $request->comment;
        $comment->save();

        return back()->with('message','コメントを送信しました');
    }
}
