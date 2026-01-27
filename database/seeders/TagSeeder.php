<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
    ];
    DB::table('tags')->insert($tags);

    }
}
