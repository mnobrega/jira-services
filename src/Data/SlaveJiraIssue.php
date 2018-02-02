<?php

namespace App\Data;

use Illuminate\Database\Eloquent\Model;

class SlaveJiraIssue extends Model
{
    protected $table = 'jira_sync_slave_jira_issues';
    protected $fillable = ['master_issue_key','slave_issue_key'];

    public function issue()
    {
        return $this->hasOne('App\Data\Issue');
    }
}
