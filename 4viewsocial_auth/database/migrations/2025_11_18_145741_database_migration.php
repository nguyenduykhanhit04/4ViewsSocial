<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        /* USERS */
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->string('user_name', 255)->nullable();
            $table->string('full_name', 255)->nullable();
            $table->string('avatar_url', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('password', 255)->nullable();
            $table->string('facebook_url', 255)->nullable();
            $table->string('thread_url', 255)->nullable();
            $table->string('instagram_url', 255)->nullable();
            $table->string('bio', 255)->nullable();
            $table->tinyInteger('role')->default(1); // 0 = admin, 1 = client
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->tinyInteger('online_status')->default(0); // 0 offline, 1 online
            $table->integer('status')->default(0); // 0 sử dụng, 1 banned
            $table->integer('login_fail')->default(0);
            $table->string('token', 255)->nullable();
        });

        /* POSTS */
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->integer('user_id')->index()->nullable();
            $table->string('caption', 255)->nullable();
            $table->integer('total_like')->default(0);
            $table->integer('total_comment')->default(0);
            $table->string('thumbnail_url', 255)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });

        /* FOLLOWS */
        Schema::create('follows', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->integer('user_id')->index()->nullable();
            $table->integer('following_id')->index()->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        /* COMMENTS */
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->integer('user_id')->index()->nullable();
            $table->integer('post_id')->index()->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('comment', 255)->nullable();
            $table->integer('parent_id')->index()->nullable();
        });

        /* LIKE_POSTS */
        Schema::create('like_posts', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->integer('user_id')->index()->nullable();
            $table->integer('post_id')->index()->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        /* STORIES */
        Schema::create('stories', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->string('video_url', 255)->nullable();
            $table->integer('user_id')->index()->nullable();
            $table->timestamp('expired_time')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        /* LIKE_STORIES */
        Schema::create('like_stories', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->integer('user_id')->index()->nullable();
            $table->integer('story_id')->index()->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        /* FAVOURITES */
        Schema::create('favourites', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->integer('user_id')->index()->nullable();
            $table->integer('post_id')->index()->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        /* NOTIFICATIONS */
        Schema::create('notifications', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->integer('user_id')->index()->nullable();
            $table->integer('actor_id')->index()->nullable();
            $table->string('content', 255)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->tinyInteger('is_view')->default(0);
            $table->integer('status')->default(0);
        });

        /* VIOLENCE_WARNINGS */
        Schema::create('violence_warnings', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->integer('user_id')->index()->nullable();
            $table->integer('infringe_id')->index()->nullable();
        });

        Schema::create('user_verifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index(); // Khóa ngoại tới bảng users
            $table->string('code'); // Mã xác thực (VD: 6 ký tự)
            $table->timestamp('expires_at')->nullable(); // Thời gian hết hạn mã
            $table->timestamps();

            // 🔗 Khóa ngoại
            // $table->foreign('user_id')
            //     ->references('id')
            //     ->on('users')
            //     ->onDelete('cascade'); // Nếu user bị xóa thì xóa luôn mã
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('violence_warnings');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('favourites');
        Schema::dropIfExists('like_stories');
        Schema::dropIfExists('stories');
        Schema::dropIfExists('like_posts');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('follows');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('users');
        Schema::dropIfExists('user_verifications');
    }
};
