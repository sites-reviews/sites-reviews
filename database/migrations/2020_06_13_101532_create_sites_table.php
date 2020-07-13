<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string('domain')->unique()->comment(__('site.domain'));
            $table->string('title')->comment(__('site.title'));
            $table->text('description')->nullable()->comment(__('site.description'));
            $table->integer('create_user_id')->nullable()->comment(__('site.create_user_id'));
            $table->integer('preview_image_id')->nullable()->comment(__('site.preview_image_id'));
            $table->double('rating')->nullable()->comment(__('site.rating'));
            $table->integer('number_of_views')->default(0)->comment(__('site.number_of_views'));
            $table->integer('number_of_reviews')->default(0)->comment(__('site.number_of_reviews'));
            $table->boolean('update_the_preview')->default(false)->index()->comment(__('site.update_the_preview'));
            $table->boolean('update_the_page')->default(false)->index()->comment(__('site.update_the_page'));
            $table->timestamp('latest_rating_changes_at')->nullable()->comment(__('site.latest_rating_changes_at'));
            $table->timestamps();
            $table->softDeletes();
        });

        \Illuminate\Support\Facades\DB::statement('CREATE INDEX sites_title_gin_trgm_index ON sites USING gin (title gin_trgm_ops);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sites');
    }
}
