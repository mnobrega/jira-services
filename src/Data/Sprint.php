<?php

namespace App\Data;

use Illuminate\Database\Eloquent\Model;

class Sprint extends Model
{
    protected $table = 'jira_wrapper_sprints';
    protected $fillable = ['sprint_id','state','start_date','end_date','origin_board_id'];

    public function issues()
    {
        return $this->belongsToMany('App\Data\Issue','jira_wrapper_sprints_issues',
            'sprint_id','issue_id');
    }
}