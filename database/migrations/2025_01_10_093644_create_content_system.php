<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title', 300);
            $table->string('slug')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('hub_id')->constrained('hubs')->cascadeOnDelete();
            $table->text('content');
            $table->integer('vote_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->fullText(['title', 'content']);
            $table->index('vote_count');
        });

        Schema::create('post_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('posts')->cascadeOnDelete();
            $table->string('image_path');
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('comments')->onDelete('cascade');
            $table->text('content');
            $table->integer('vote_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users');
            $table->morphs('votable');
            $table->smallInteger('vote');  // Options +1, 0, -1
            $table->unique(['user_id', 'votable_id', 'votable_type']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_system');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('post_images');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('votes');
    }
};
