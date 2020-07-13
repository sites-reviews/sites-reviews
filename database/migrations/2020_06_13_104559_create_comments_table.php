<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->integer('review_id')->index()->comment(__('comment.review_id'));
            $table->integer('create_user_id')->comment(__('comment.create_user_id'));
            $table->text('text')->comment(__('comment.text'));
            $table->integer('rating')->default(0)->comment(__('comment.rating'));
            $table->integer('rate_up')->default(0)->comment(__('comment.rate_up'));
            $table->integer('rate_down')->default(0)->comment(__('comment.rate_down'));
            $table->smallInteger('children_count')->default(0)->comment(__('comment.children_count'));
            $table->smallInteger('level')->default(0)->comment(__('comment.level'));
            $table->string('tree')->nullable()->comment(__('comment.tree'));
            $table->timestamp('created_at')->index();
            $table->timestamp('updated_at');
            $table->softDeletes();
        });

        \Illuminate\Support\Facades\DB::statement('CREATE INDEX comments_tree_gin_trgm_index ON comments USING gin (tree gin_trgm_ops);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
