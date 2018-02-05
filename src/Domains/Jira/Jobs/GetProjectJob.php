<?php
namespace App\Domains\Jira\Jobs;

use App\Data\RestApis\JiraApi;
use Lucid\Foundation\Job;

class GetProjectJob extends Job
{
    private $jiraApi;
    private $projectKey;

    /**
     * GetProjectJob constructor.
     * @param $jiraInstance
     * @param $projectKey
     */
    public function __construct($jiraInstance, $projectKey)
    {
        $this->jiraApi = new JiraApi($jiraInstance);
        $this->projectKey = $projectKey;
    }

    /**
     * @return \JiraRestApi\Project\Project|object
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function handle()
    {
        return $this->jiraApi->getProject($this->projectKey);
    }
}
