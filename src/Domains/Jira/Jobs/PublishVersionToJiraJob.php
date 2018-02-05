<?php
namespace App\Domains\Jira\Jobs;

use App\Data\RestApis\JiraApi;
use JiraRestApi\Version\Version;
use Lucid\Foundation\Job;

class PublishVersionToJiraJob extends Job
{
    private $jiraApi;
    private $version;
    private $remoteVersionId;
    private $remoteProjectId;

    /**
     * PublishVersionToJiraJob constructor.
     * @param $jiraInstance
     * @param Version $version
     * @param $remoteVersionId
     * @param $remoteProjectId
     */
    public function __construct($jiraInstance, Version $version, $remoteVersionId, $remoteProjectId)
    {
        $this->jiraApi = new JiraApi($jiraInstance);
        $this->version = $version;
        $this->remoteVersionId = $remoteVersionId;
        $this->remoteProjectId = $remoteProjectId;
    }

    /**
     * @return Version|object
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function handle()
    {
        $this->version->projectId = $this->remoteProjectId;
        $this->version->userStartDate = null;
        $this->version->userReleaseDate = null;

        if (is_null($this->remoteVersionId)) {
            return $this->jiraApi->createVersion($this->version);
        } else {
            return $this->jiraApi->updateVersion($this->remoteVersionId,$this->version);
        }
    }
}
