<?php

namespace App\Data;

use Illuminate\Database\Eloquent\Model;

class SlaveJiraIssueLink extends Model
{
    protected $table = 'jira_sync_slave_jira_issue_link';
    protected $fillable = ['master_issue_link_jira_id','slave_issue_link_jira_id'];

    public function issueLink()
    {
        $this->hasOne('App\Data\IssueLink','jira_id','master_issue_link_jira_id');
    }
}
