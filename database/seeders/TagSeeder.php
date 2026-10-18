<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            ['name' => 'Политика', 'slug' => 'politics'],
            ['name' => 'Игры', 'slug' => 'games'],
            ['name' => 'Спорт', 'slug' => 'sports'],
            ['name' => 'Технологии', 'slug' => 'tech'],
            ['name' => 'Кино', 'slug' => 'movies'],
            ['name' => 'Наука', 'slug' => 'science'],
        ];

        // firstOrCreate: сидеры запускаются при каждом старте контейнера
        foreach ($tags as $tag) {
            Tag::firstOrCreate(['slug' => $tag['slug']], ['name' => $tag['name']]);
        }
    }
}
