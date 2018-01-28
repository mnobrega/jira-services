<?php
namespace App\Domains\Jira\Jobs;

use App\Data\RestApis\JiraApi;
use Lucid\Foundation\Job;

class SearchJiraIssuesByJQLJob extends Job
{
    private $jiraApi;
    private $jiraQuery;

    /**
     * SearchIssuesByJQLJob constructor.
     * @param $jiraInstance
     * @param $jiraQuery
     */
    public function __construct($jiraInstance, $jiraQuery)
    {
        $this->jiraApi = new JiraApi($jiraInstance);
        $this->jiraQuery = $jiraQuery;
    }

    /**
     * @return \JiraRestApi\Issue\Issue[]
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function handle()
    {
        return $this->jiraApi->getJiraIssuesByJQL($this->jiraQuery);
    }
}
