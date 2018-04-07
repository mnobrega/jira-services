<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSprintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jira_wrapper_sprints', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('jira_id');
            $table->string('name');
            $table->string('state');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->timestamps();
        });

        Schema::create('jira_wrapper_sprints_issues', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('sprint_id');
            $table->integer('issue_id');
            $table->softDeletes();
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
        Schema::dropIfExists('jira_wrapper_sprints');
        Schema::dropIfExists('jira_wrapper_sprints_issues');
    }
}
