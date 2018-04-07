<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSlaveJiraSprintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jira_sync_slave_jira_sprints', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('master_sprint_jira_id');
            $table->integer('slave_sprint_jira_id');
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
        Schema::dropIfExists('jira_sync_slave_jira_sprints');
    }
}
