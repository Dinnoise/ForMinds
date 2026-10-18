<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comment_reactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('comment_id')
                  ->constrained('comments')
                  ->cascadeOnDelete();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // ключ из Comment::REACTIONS (like, heart, laugh...)
            $table->string('type', 20);
            $table->timestamp('created_at')->useCurrent();

            // один пользователь — одна реакция каждого типа на комментарий
            $table->unique(['comment_id', 'user_id', 'type'], 'uq_comment_reactions');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comment_reactions');
    }
};
