<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJiraConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jira_wrapper_jira_config', function (Blueprint $table) {
            $table->increments('id');
            $table->string('jira_issues_query');
            $table->string('jira_board_name');
            $table->string('jira_board_type');
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
        Schema::dropIfExists('jira_wrapper_jira_config');
    }
}
