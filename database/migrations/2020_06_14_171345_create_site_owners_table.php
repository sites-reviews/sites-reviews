<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteOwnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_owners', function (Blueprint $table) {
            $table->id();
            $table->integer('create_user_id')->comment(__('site_owner.create_user_id'));
            $table->integer('site_id')->index()->comment(__('site_owner.site_id'));
            $table->smallInteger('status')->nullable()->comment(__('site_owner.status'));
            $table->dateTime('status_changed_at')->nullable()->comment(__('site_owner.status_changed_at'));
            $table->integer('status_changed_user_id')->nullable()->comment(__('site_owner.status_changed_user_id'));
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
        Schema::dropIfExists('site_owners');
    }
}
