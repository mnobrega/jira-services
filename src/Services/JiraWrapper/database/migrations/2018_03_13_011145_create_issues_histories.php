<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIssuesHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jira_wrapper_issues_histories', function(Blueprint $table){
            $table->increments('id');
            $table->integer('issue_id');
            $table->integer('jira_id');
            $table->dateTime('created');
            $table->string('field');
            $table->string('field_type');
            $table->text('from_string')->nullable();
            $table->text('to_string')->nullable();
            $table->string('author_name');
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
        Schema::drop('jira_wrapper_issues_histories');
    }
}
