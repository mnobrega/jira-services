<?php
namespace App\Domains\JiraClient\Jobs;

use JiraRestApi\Issue\IssueService;
use Lucid\Foundation\Job;
use App\Data\Issue;

class SearchIssuesByJQLJob extends Job
{
    private $jiraApi;
    private $query;

    /**
     * SearchIssuesByJQLJob constructor.
     * @param IssueService $jiraApi
     * @param $query
     */
    public function __construct(IssueService $jiraApi, $query)
    {
        $this->jiraApi = $jiraApi;
        $this->query = $query;
    }

    /**
     * @return \JiraRestApi\Issue\Issue[]
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function handle()
    {
        $searchResult = $this->jiraApi->search($this->query);
        return $searchResult->getIssues();
    }
}
