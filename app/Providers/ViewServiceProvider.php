<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Tag;
use Illuminate\Support\Facades\View;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Запрос выполняется только при рендере, иначе artisan (в т.ч. migrate) падает на пустой БД
        View::composer('*', function ($view) {
            $view->with('tags', once(fn () => Tag::all()));
        });
    }
}
