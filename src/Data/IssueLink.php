<?php

namespace App\Data;

use Illuminate\Database\Eloquent\Model;

class IssueLink extends Model
{
    protected $table = 'jira_wrapper_issues_links';
    protected $fillable = ['jira_id','issue_id','type','inward','outward','inward_issue_id','outward_issue_id'];

    public function issue()
    {
        return $this->belongsTo('App\Data\Issue','issue_id');
    }

    public function inwardIssue()
    {
        return $this->hasOne('App\Data\Issue','id','inward_issue_id');
    }

    public function outwardIssue()
    {
        return $this->hasOne('App\Data\Issue','id','outward_issue_id');
    }
}