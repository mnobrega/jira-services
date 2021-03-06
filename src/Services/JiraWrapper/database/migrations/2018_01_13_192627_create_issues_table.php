<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::create('jira_wrapper_issues',function(Blueprint $table) {
            $table->increments('id');
            $table->string('issue_key',50);
            $table->string('project_key',50);
            $table->string('priority',50)->nullable();
            $table->integer('ranking')->nullable();
            $table->string('type',50);
            $table->string('status',50);
            $table->text('summary');
            $table->dateTime('created');
            $table->dateTime('updated');
            $table->string('fix_version_id',20)->nullable();
            $table->string('epic_link',50)->nullable();
            $table->string('epic_name',50)->nullable();
            $table->string('epic_color',50)->nullable();
            $table->string('assignee',50)->nullable();
            $table->integer('remaining_estimate')->nullable();
            $table->integer('original_estimate')->nullable();
            $table->index('issue_key','idx_issue_key');
            $table->index('project_key','idx_project_key');
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
        \Schema::drop('jira_wrapper_issues');
    }
}
