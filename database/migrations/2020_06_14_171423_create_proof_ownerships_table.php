<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProofOwnershipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proof_ownerships', function (Blueprint $table) {
            $table->id();
            $table->integer('site_owner_id')->index()->comment(__('proof_ownership.site_owner_id'));
            $table->string('dns_code')->comment(__('proof_ownership.dns_code'));
            $table->string('file_code')->comment(__('proof_ownership.file_code'));
            $table->string('file_path')->comment(__('proof_ownership.file_path'));
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
        Schema::dropIfExists('proof_ownerships');
    }
}
