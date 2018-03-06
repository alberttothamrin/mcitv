<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableRolegroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('role_group', function (Blueprint $table) {
            $table->increments('id');
            $table->string('role_group_name');//top up, transfer, reduction
            $table->string('role_group_detail');
            $table->string('role_group_permission');
            $table->boolean('is_active');
            $table->dateTime('created_date');
            $table->dateTime('modified_date');
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
        Schema::dropIfExists('role_group');
    }
}
