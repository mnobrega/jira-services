<?php
namespace App\Domains\Jira\Jobs;

use App\Data\RestApis\JiraApi;
use Lucid\Foundation\Job;

class GetJiraVersionJob extends Job
{
    private $jiraApi;
    private $versionId;

    /**
     * GetJiraVersionJob constructor.
     * @param $jiraInstance
     * @param $versionId
     */
    public function __construct($jiraInstance, $versionId)
    {
        $this->jiraApi = new JiraApi($jiraInstance);
        $this->versionId = $versionId;
    }

    /**
     * @return \JiraRestApi\Version\Version|object
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function handle()
    {
        return $this->jiraApi->getVersionById($this->versionId);
    }
}
