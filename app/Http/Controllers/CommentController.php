<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Post;
use App\Models\Comment;

class CommentController extends Controller
{
    public function store(Post $post, Request $request)
        {
            abort_if($post->is_draft, 404);

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

    /**
     * Поставить или убрать стикер-реакцию на комментарий
     */
    public function toggleReaction(Comment $comment, Request $request)
        {
            $request->validate([
                'type' => ['required', Rule::in(array_keys(Comment::REACTIONS))],
            ]);

            abort_if($comment->post->is_draft, 404);

            $reaction = $comment->reactions()->firstOrNew([
                'user_id' => auth()->id(),
                'type'    => $request->type,
            ]);

            $reaction->exists ? $reaction->delete() : $reaction->save();

            // AJAX-клик: отдаём только обновлённый блок реакций, страница не перезагружается
            if ($request->expectsJson()) {
                return response()->json([
                    'html' => view('comments._reactions', [
                        'comment' => $comment->load('reactions'),
                    ])->render(),
                ]);
            }

            // без JS: возвращаемся к тому же комментарию, а не в начало страницы
            return redirect()->to(url()->previous() . '#comment-' . $comment->id);
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
