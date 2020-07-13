<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment(__('user.name'));
            $table->string('email')->unique()->comment(__('user.email'));
            $table->timestamp('email_verified_at')->nullable()->comment(__('user.email_verified_at'));
            $table->string('password')->comment(__('user.password'));
            $table->integer('number_of_reviews')->default(0)->comment(__('user.number_of_reviews'));
            $table->integer('rating')->default(0)->comment(__('user.rating'));
            $table->integer('avatar_image_id')->nullable()->comment(__('user.avatar_image_id'));
            $table->integer('avatar_preview_image_id')->nullable()->comment(__('user.avatar_preview_image_id'));
            $table->tinyInteger('gender')->default(0)->comment(__('user.gender'));
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
