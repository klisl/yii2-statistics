<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class KslStatIp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kslStatistics', function (Blueprint $table) {
            $table->increments('id');

            $table->string('ip',15);
            $table->string('str_url');
            $table->boolean('black_list_ip')->default(0);
            $table->string('comment')->nullable();

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
        Schema::dropIfExists('kslStatistics');
    }
}
