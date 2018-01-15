<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSyncTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::create('sync_events', function(Blueprint $table) {
            $table->increments('id');
            $table->dateTime('from_datetime');
            $table->dateTime('to_datetime')->nullable();
            $table->integer('tuples_created')->default(0);
            $table->integer('tuples_updated')->default(0);
            $table->integer('tuples_deleted')->default(0);
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
        \Schema::drop('sync_events');
    }
}
