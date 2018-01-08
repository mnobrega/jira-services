<?php

namespace App\Data;

use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    static function loadFromJiraApiIssue(\Jira_Issue $jiraApiIssue)
    {
        dd($jiraApiIssue);
    }
}
