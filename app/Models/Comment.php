<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['body', 'post_id', 'user_id', 'parent_id',];

    // Доступные стикеры-реакции: ключ хранится в БД, эмодзи — для отображения (порядок = порядок в выборе)
    public const REACTIONS = [
        'like'  => '👍',
        'heart' => '❤️',
        'laugh' => '😂',
        'wow'   => '😮',
        'sad'   => '😢',
        'fire'  => '🔥',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    // Родительский комментарий
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    // Ответы
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')
            ->with('user', 'reactions', 'replies');
    }

    public function reactions()
    {
        return $this->hasMany(CommentReaction::class);
    }

    /**
     * Реакции для отображения: [type => ['emoji', 'count', 'mine']] в порядке REACTIONS
     */
    public function reactionSummary(?int $userId): array
    {
        $summary = [];

        foreach (self::REACTIONS as $type => $emoji) {
            $group = $this->reactions->where('type', $type);

            if ($group->isNotEmpty()) {
                $summary[$type] = [
                    'emoji' => $emoji,
                    'count' => $group->count(),
                    'mine'  => $userId !== null && $group->contains('user_id', $userId),
                ];
            }
        }

        return $summary;
    }
}
