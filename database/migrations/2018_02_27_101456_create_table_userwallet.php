<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserwallet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('user_wallet', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('wallet_account')->unique();
            $table->string('wallet_password');
            $table->float('wallet_amount', 10, 2)->default(0);
            $table->boolean('is_active');
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
        Schema::dropIfExists('user_wallet');
    }
}
