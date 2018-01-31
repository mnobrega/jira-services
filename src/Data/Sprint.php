<?php

namespace App\Data;

use Illuminate\Database\Eloquent\Model;

class Sprint extends Model
{
    protected $table = 'jira_wrapper_sprints';
    protected $fillable = ['jira_id','name','state','start_date','end_date'];

    public function issues()
    {
        return $this->belongsToMany('App\Data\Issue','jira_wrapper_sprints_issues',
            'sprint_id','issue_id')->withTimestamps();
    }
}
