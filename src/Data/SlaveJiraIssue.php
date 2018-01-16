<?php

namespace App\Data;

use Illuminate\Database\Eloquent\Model;

class SlaveJiraIssue extends Model
{
    protected $table = 'slave_jira_issues';
    protected $fillable = ['issue_id','key'];

    public function issue()
    {
        return $this->hasOne('App\Data\Issue');
    }
}