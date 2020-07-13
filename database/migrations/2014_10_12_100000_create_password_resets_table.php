<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePasswordResetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('password_resets', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index()->comment(__('password_reset.user_id'));
            $table->string('email')->index()->comment(__('password_reset.email'));
            $table->string('token')->unique()->comment(__('password_reset.token'));
            $table->timestamp('created_at')->nullable()->comment(__('password_reset.created_at'));
            $table->timestamp('used_at')->nullable()->comment(__('password_reset.used_at'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('password_resets');
    }
}
