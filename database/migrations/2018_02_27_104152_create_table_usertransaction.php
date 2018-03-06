<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUsertransaction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('user_transaction_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('target_user_id');
            $table->string('history_type');//top up, transfer, reduction
            $table->float('transaction_amount', 10, 2)->default(0);
            $table->tinyInteger('is_reversed')->default(0);//if 1, it means already reversed before, and we will not do anything again with this history
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
        Schema::dropIfExists('user_transaction_history');
    }
}
