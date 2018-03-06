<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserwalletHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('user_wallet_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('wallet_id');
            $table->string('history_type');//top up, transfer, reduction
            $table->float('transaction_amount', 10, 2)->default(0);
            $table->dateTime('transaction_date');
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
        Schema::dropIfExists('user_wallet_history');
    }
}
