<?php
namespace App\Domains\JiraClient\Jobs;

use JiraRestApi\Configuration\ArrayConfiguration;
use JiraRestApi\Issue\IssueService;
use Lucid\Foundation\Job;

class GetConnectionJob extends Job
{
    private $jiraApi;

    /**
     * Get a Jira REST API connection
     *
     * @param $host
     * @param $user
     * @param $password
     */
    public function __construct($host, $user, $password)
    {
        $this->jiraApi = new IssueService(new ArrayConfiguration(
            array(
                'jiraHost' => $host,
                'jiraUser' => $user,
                'jiraPassword'=>$password
            )
        ));
    }

    /**
     * Return the JIRA API connection.
     *
     * @return IssueService
     */
    public function handle()
    {
        return $this->jiraApi;
    }
}
