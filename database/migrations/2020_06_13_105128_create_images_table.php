<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment(__('image.name'));
            $table->string('storage')->comment(__('image.storage'));
            $table->integer('create_user_id')->nullable()->comment(__('image.create_user_id'));
            $table->integer('filesize')->comment(__('image.filesize'));
            $table->string('type')->comment(__('image.type'));
            $table->string('dirname')->nullable()->comment(__('image.dirname'));
            $table->string('phash', 16)->nullable()->comment(__('image.phash'));
            $table->string('sha256_hash', 64)->nullable()->comment(__('image.sha256_hash'));
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
        Schema::dropIfExists('images');
    }
}
