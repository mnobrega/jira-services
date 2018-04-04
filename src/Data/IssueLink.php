<?php

namespace App\Data;

use Illuminate\Database\Eloquent\Model;

class IssueLink extends Model
{
    protected $table = 'jira_wrapper_issues_links';
    protected $fillable = ['jira_id','type','issue_id','outward_issue_id'];

    public function issue()
    {
        return $this->belongsTo('App\Data\Issue','issue_id');
    }

    public function outwardIssue()
    {
        return $this->belongsTo('App\Data\Issue','outward_issue_id');
    }
}