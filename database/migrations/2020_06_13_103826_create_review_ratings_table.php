<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('review_ratings', function (Blueprint $table) {
            $table->id();
            $table->integer('rateable_id')->index()->comment(__('review_vote.rateable_id'));
            $table->tinyInteger('rating')->comment(__('review_vote.vote'));
            $table->integer('create_user_id')->comment(__('review_vote.create_user_id'));
            $table->timestamps();
            $table->softDeletes();
            $table->index(['rateable_id', 'create_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('review_ratings');
    }
}
