<?php
namespace App\Domains\Jira\Jobs;

use Lucid\Foundation\Job;

class GetJiraIssueKeysJob extends Job
{
    private $jiraIssues;

    /**
     * GetJiraIssueKeysJob constructor.
     * @param \JiraRestApi\Issue\Issue[] $jiraIssues
     */
    public function __construct(Array $jiraIssues)
    {
        $this->jiraIssues = $jiraIssues;
    }

    /**
     * @return array
     */
    public function handle()
    {
        $jiraIssueKeys = array();
        foreach ($this->jiraIssues as $jiraIssue) {
            $jiraIssueKeys[] = $jiraIssue->key;
        }
        return $jiraIssueKeys;
    }
}
