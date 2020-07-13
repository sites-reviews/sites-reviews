<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type')->comment(__('notification.type'));
            $table->morphs('notifiable');
            $table->text('data')->comment(__('notification.data'));
            $table->timestamp('read_at')->nullable()->comment(__('notification.read_at'));
            $table->timestamps();
            $table->index(['notifiable_id', 'notifiable_type', 'read_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
