<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;

class CommentController extends Controller
{
    public function store(Post $post, Request $request)
        {
            $request->validate([
                'body' => 'required|string|max:2000',
                'parent_id' => 'nullable|exists:comments,id',
            ]);
            $post->comments()->create([
                'user_id'   => auth()->id(),
                'body'      => $request->body,
                'parent_id' => $request->parent_id,
            ]);
            return redirect()->back();
        }

    public function destroy(Comment $comment)
        {
            if ($comment->user_id !== auth()->id()) {
                abort(403);
            }

            $comment->delete();

            return back();
        }
}
