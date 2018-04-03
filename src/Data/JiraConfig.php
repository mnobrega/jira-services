<?php

namespace App\Data;

use Illuminate\Database\Eloquent\Model;

class JiraConfig extends Model
{
    protected $table = 'jira_wrapper_jira_config';
    protected $fillable = ['jira_issues_query','jira_board_name','jira_board_type'];
}
