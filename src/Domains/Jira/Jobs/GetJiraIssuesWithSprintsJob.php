<?php
namespace App\Domains\Jira\Jobs;

use App\Data\RestApis\JiraApi;
use Lucid\Foundation\Job;

class GetJiraIssuesWithSprintsJob extends Job
{
    private $jiraApi;
    private $jiraIssues;
    private $jiraSprintCustomFieldId;

    public function __construct($jiraInstance, $jiraIssues, $jiraSprintCustomFieldId)
    {
        $this->jiraApi = new JiraApi($jiraInstance);
        $this->jiraIssues = $jiraIssues;
        $this->jiraSprintCustomFieldId = $jiraSprintCustomFieldId;
    }

    public function handle()
    {
        //
    }
}
