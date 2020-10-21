<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_reviews', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid')->index();
            $table->integer('site_id')->index()->comment(__('temp_review.site_id'));
            $table->text('advantages')->comment(__('temp_review.advantages'));
            $table->text('disadvantages')->comment(__('temp_review.disadvantages'));
            $table->text('comment')->comment(__('temp_review.comment'));
            $table->tinyInteger('rate')->comment(__('temp_review.rate'));
            $table->string('email')->index()->comment(__('temp_review.email'));
            $table->string('token')->comment(__('temp_review.token'));
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
        Schema::dropIfExists('temp_reviews');
    }
}
