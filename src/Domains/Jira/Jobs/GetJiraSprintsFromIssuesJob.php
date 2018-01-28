<?php
namespace App\Domains\Jira\Jobs;

use App\Data\RestApis\JiraAgile;
use Lucid\Foundation\Job;
use Lucid\Foundation\Model;

class GetJiraSprintsFromIssuesJob extends Job
{
    private $jiraAgile;
    private $jiraIssues;

    /**
     * GetJiraSprintsFromIssuesJob constructor.
     * @param $jiraInstance string
     * @param $jiraIssues \JiraRestApi\Issue\Issue[]
     */
    public function __construct($jiraInstance, Array $jiraIssues)
    {
        $this->jiraAgile = new JiraAgile($jiraInstance);
        $this->jiraIssues = $jiraIssues;
    }

    /**
     * @return array
     * @throws \JiraAgileRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function handle()
    {
        $sprintsIssues = array();
        foreach ($this->jiraIssues as $jiraIssue) {
            $jiraIssueWithSprint = $this->jiraAgile->getSprintFromIssue($jiraIssue->key);
            dd($jiraIssueWithSprint);
        }
        return array();
    }
}
