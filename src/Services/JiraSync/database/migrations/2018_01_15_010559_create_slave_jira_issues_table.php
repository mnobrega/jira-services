<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSlaveJiraIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::create('jira_sync_slave_jira_issues',function(Blueprint $table) {
            $table->increments('id');
            $table->string('master_issue_key', 50);
            $table->string('slave_issue_key',50);
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
        \Schema::drop('jira_sync_slave_jira_issues');
    }
}
