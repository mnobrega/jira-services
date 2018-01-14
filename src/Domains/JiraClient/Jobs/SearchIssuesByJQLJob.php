<?php
namespace App\Domains\JiraClient\Jobs;

use Lucid\Foundation\Job;
use App\Data\Issue;

class SearchIssuesByJQLJob extends Job
{
    private $walker;
    private $query;

    /**
     * SearchIssuesByJQLJob constructor.
     * @param \Jira_Api $jiraApi
     * @param $query
     */
    public function __construct(\Jira_Api $jiraApi, $query)
    {
        $this->walker = new \Jira_Issues_Walker($jiraApi);
        $this->query = $query;
    }

    /**
     * @return Issue []
     */
    public function handle()
    {
        $issues = array();
        $this->walker->push($this->query);
        foreach ($this->walker as $issue) {
            $issues[] = $issue;
        }
        return $issues;
    }
}
