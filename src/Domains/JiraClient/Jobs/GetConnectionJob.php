<?php
namespace App\Domains\JiraClient\Jobs;

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
        $this->jiraApi = new \Jira_Api($host, new \Jira_Api_Authentication_Basic($user, $pass));
    }

    /**
     * Return the JIRA API connection.
     *
     * @return \Jira_Api
     */
    public function handle()
    {
        return $this->jiraApi;
    }
}
