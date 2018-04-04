<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIssuesLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jira_wrapper_issues_links', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('jira_id');
            $table->integer('issue_id');
            $table->string('type');
            $table->string('inward');
            $table->string('outward');
            $table->integer('inward_issue_id')->nullable();
            $table->integer('outward_issue_id')->nullable();
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
        Schema::dropIfExists('jira_wrapper_issues_links');
    }
}
