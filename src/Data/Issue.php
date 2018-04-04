<?php

namespace App\Data;

use Lucid\Foundation\Model;

class Issue extends Model
{
    const TYPE_EPIC = 'Epic';

    protected $table = 'jira_wrapper_issues';
    protected $fillable = ['issue_key','project_key','priority','ranking','type','status','summary','created','updated',
        'fix_version_id', 'epic_link','epic_name','epic_color','assignee','remaining_estimate','original_estimate'];

    public function sprints()
    {
        return $this->belongsToMany('App\Data\Sprint','jira_wrapper_sprints_issues',
            'issue_id','sprint_id')->withTimestamps();
    }

    public function histories()
    {
        return $this->hasMany('App\Data\IssueHistory','jira_wrapper_issues_histories',
            'issue_id');
    }

    public function links()
    {
        return $this->hasMany('App\Data\IssueLink','jira_wrapper_issues_links',
            'issue_id');
    }
}