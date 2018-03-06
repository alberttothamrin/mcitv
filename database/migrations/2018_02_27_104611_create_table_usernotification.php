<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUsernotification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('user_notification_log', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('target_user_id');
            $table->string('notification_type');//top up, transfer, reduction
            $table->string('notification_detail');
            $table->string('notification_url')->nullable();
            $table->tinyInteger('read')->default(0);
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
        //
        Schema::dropIfExists('user_notification_log');
    }
}
