<?php

namespace Database\Seeders;

use App\Models\Like;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->firstOrFail();
        $user = User::where('email', 'test@example.com')->firstOrFail();

        $posts = [
            [
                'author' => $admin,
                'title' => 'Добро пожаловать на Talkspace!',
                'body' => "Привет всем! Это первая тема на форуме.\n\nЗдесь можно обсуждать что угодно: от новостей и науки до игр и кино. Создавайте темы, отмечайте их тегами, ставьте лайки и отвечайте друг другу в комментариях. Пожалуйста, уважайте собеседников.",
                'tags' => ['tech'],
                'comments' => [
                    ['author' => $user, 'body' => 'Привет! Отличный форум, буду заходить.', 'replies' => [
                        ['author' => $admin, 'body' => 'Спасибо, рады видеть!'],
                    ]],
                ],
                'likes' => [$user],
            ],
            [
                'author' => $user,
                'title' => 'Во что сейчас играете?',
                'body' => 'Делитесь, во что играете в последнее время и стоит ли оно того. Я сейчас прохожу Baldur\'s Gate 3 — затягивает на десятки часов.',
                'tags' => ['games'],
                'comments' => [
                    ['author' => $admin, 'body' => 'Перепрохожу Hollow Knight в ожидании Silksong.', 'replies' => [
                        ['author' => $user, 'body' => 'Классика! Тоже давно хочу перепройти.'],
                    ]],
                ],
                'likes' => [$admin],
            ],
            [
                'author' => $admin,
                'title' => 'Какой язык программирования выбрать первым?',
                'body' => 'Часто спрашивают, с чего начать путь в программировании. Python, JavaScript, C#, PHP? Расскажите, с чего начинали вы и что посоветуете новичкам.',
                'tags' => ['tech', 'science'],
                'comments' => [
                    ['author' => $user, 'body' => 'Я начинал с Python — простой синтаксис и сразу видно результат.'],
                ],
                'likes' => [$user, $admin],
            ],
            [
                'author' => $user,
                'title' => 'Лучший фильм, который вы посмотрели в этом году',
                'body' => 'Давайте соберём список рекомендаций. Напишите название фильма и пару слов, чем он зацепил.',
                'tags' => ['movies'],
                'comments' => [],
                'likes' => [],
            ],
            [
                'author' => $admin,
                'title' => 'Спорт и учёба: как всё успевать?',
                'body' => 'Многие бросают тренировки во время сессии. Как вы совмещаете спорт с учёбой или работой? Делитесь своим расписанием и лайфхаками.',
                'tags' => ['sports', 'science'],
                'comments' => [
                    ['author' => $user, 'body' => 'Хожу в зал рано утром, до пар — потом весь день бодрее.'],
                ],
                'likes' => [$user],
            ],
        ];

        foreach ($posts as $data) {
            $post = Post::firstOrCreate(
                ['title' => $data['title']],
                ['body' => $data['body'], 'user_id' => $data['author']->id],
            );

            // Комментарии и связи создаём только для новой темы, чтобы при повторном запуске не было дублей
            if (! $post->wasRecentlyCreated) {
                continue;
            }

            $post->tags()->sync(Tag::whereIn('slug', $data['tags'])->pluck('id'));

            foreach ($data['comments'] as $comment) {
                $parent = $post->allComments()->create([
                    'body' => $comment['body'],
                    'user_id' => $comment['author']->id,
                ]);

                foreach ($comment['replies'] ?? [] as $reply) {
                    $post->allComments()->create([
                        'body' => $reply['body'],
                        'user_id' => $reply['author']->id,
                        'parent_id' => $parent->id,
                    ]);
                }
            }

            foreach ($data['likes'] as $liker) {
                Like::firstOrCreate(['user_id' => $liker->id, 'post_id' => $post->id]);
            }
        }
    }
}
