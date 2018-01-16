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
     * @param $pass
     */
    public function __construct($host, $user, $pass)
    {
        $this->jiraApi = new IssueService(new ArrayConfiguration(
            array(
                'jiraHost' => $host,
                'jiraUser' => $user,
                'jiraPassword'=>$pass
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
