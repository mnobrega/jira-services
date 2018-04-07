<?php

namespace App\Data;

use Illuminate\Database\Eloquent\SoftDeletes;
use Lucid\Foundation\Model;

class Issue extends Model
{
    use SoftDeletes;

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
        return $this->hasMany('App\Data\IssueHistory','issue_id', 'id');
    }

    public function links()
    {
        return $this->hasMany('App\Data\IssueLink','issue_id', 'id');
    }
}