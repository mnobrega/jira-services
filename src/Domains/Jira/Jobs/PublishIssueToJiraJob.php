<?php
namespace App\Domains\Jira\Jobs;

use App\Data\Issue;
use App\Data\RestApis\JiraApi;
use App\Data\SlaveJiraIssue;
use Lucid\Foundation\Job;

class PublishIssueToJiraJob extends Job
{
    /** @var JiraApi */
    private $jiraApi;
    /** @var Issue */
    private $issue;
    /** @var string|null */
    private $remoteIssueKey;
    /** @var string|null */
    private $remoteEpicIssueKey;

    /**
     * PublishIssueToJiraJob constructor.
     * @param string $jiraInstance
     * @param Issue $issue
     * @param string|null $remoteEpicIssueKey
     * @param string|null $remoteIssueKey
     */
    public function __construct($jiraInstance, Issue $issue, $remoteIssueKey=null, $remoteEpicIssueKey=null)
    {
        $this->jiraApi = new JiraApi($jiraInstance);
        $this->issue = $issue;
        $this->remoteIssueKey = $remoteIssueKey;
        $this->remoteEpicIssueKey = $remoteEpicIssueKey;
    }

    /**
     * @return \JiraRestApi\Issue\Issue|mixed|object
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function handle()
    {
        $this->issue->epic_link = $this->remoteEpicIssueKey;
        if (is_null($this->remoteIssueKey)) {
            return $this->jiraApi->createIssue($this->issue);
        } else {
            return $this->jiraApi->updateIssue($this->remoteIssueKey, $this->issue);
        }
    }

}
