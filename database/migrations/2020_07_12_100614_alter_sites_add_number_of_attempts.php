<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSitesAddNumberOfAttempts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->tinyInteger('number_of_attempts_update_the_preview')->nullable()->comment(__('site.number_of_attempts_update_the_preview'));
            $table->tinyInteger('number_of_attempts_update_the_page')->nullable()->comment(__('site.number_of_attempts_update_the_page'));
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn('number_of_attempts_update_the_preview');
            $table->dropColumn('number_of_attempts_update_the_page');
        });
    }
}
