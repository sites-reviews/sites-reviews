<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserNotificationSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_notification_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index()->comment(__('user_notification_setting.user_id'));
            $table->boolean('email_response_to_my_review')->comment(__('user_notification_setting.email_response_to_my_review'));
            $table->boolean('email_response_to_my_comment')->comment(__('user_notification_setting.email_response_to_my_comment'));
            $table->boolean('db_response_to_my_review')->comment(__('user_notification_setting.db_response_to_my_review'));
            $table->boolean('db_response_to_my_comment')->comment(__('user_notification_setting.db_response_to_my_comment'));
            $table->boolean('db_when_review_was_liked')->comment(__('user_notification_setting.db_when_review_was_liked'));
            $table->boolean('db_when_comment_was_liked')->comment(__('user_notification_setting.db_when_comment_was_liked'));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_notification_settings');
    }
}
