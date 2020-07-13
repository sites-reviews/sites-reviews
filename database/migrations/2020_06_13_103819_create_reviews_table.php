<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->integer('site_id')->index()->comment(__('review.site_id'));
            $table->text('advantages')->comment(__('review.advantages'));
            $table->text('disadvantages')->comment(__('review.disadvantages'));
            $table->text('comment')->comment(__('review.comment'));
            $table->integer('create_user_id')->index()->comment(__('review.create_user_id'));
            $table->tinyInteger('rate')->comment(__('review.rate'));
            $table->integer('all_text_length')->default(0)->comment(__('review.all_text_length'));
            $table->integer('rating')->index()->default(0)->comment(__('review.rating'));
            $table->integer('rate_up')->default(0)->comment(__('review.rate_up'));
            $table->integer('rate_down')->default(0)->comment(__('review.rate_down'));
            $table->smallInteger('children_count')->default(0)->comment(__('review.children_count'));
            $table->timestamp('created_at')->index();
            $table->timestamp('updated_at');
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
        Schema::dropIfExists('reviews');
    }
}
