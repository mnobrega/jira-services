<?php

namespace App\Data;

use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    protected $table = 'issues';

    public $key;
    public $type;
    public $project;
    public $originalEstimate;
    public $summary;
    public $created;
    public $updated;


    static function loadFromJiraApiIssue(\Jira_Issue $jiraApiIssue)
    {
        $issue = new Issue();
        $issue->key = $jiraApiIssue->getKey();
        $issue->summary = $jiraApiIssue->getSummary();
        $issue->type = $jiraApiIssue->getIssueType()["name"];
        $issue->project = $jiraApiIssue->getProject()["key"];
        $issue->created = $jiraApiIssue->getCreated();
        $issue->updated = $jiraApiIssue->getUpdated();
        $issue->originalEstimate = $jiraApiIssue->getFields()["Original Estimate"];
        return $issue;
    }
}
