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
    /** @var integer|null */
    private $removeVersionId;

    /**
     * PublishIssueToJiraJob constructor.
     * @param string $jiraInstance
     * @param Issue $issue
     * @param string|null $remoteEpicIssueKey
     * @param string|null $remoteIssueKey
     * @param string|null $remoteVersionId
     */
    public function __construct($jiraInstance, Issue $issue, $remoteIssueKey=null, $remoteEpicIssueKey=null,
        $remoteVersionId=null)
    {
        $this->jiraApi = new JiraApi($jiraInstance);
        $this->issue = $issue;
        $this->remoteIssueKey = $remoteIssueKey;
        $this->remoteEpicIssueKey = $remoteEpicIssueKey;
        $this->removeVersionId = $remoteVersionId;
    }

    /**
     * @return \JiraRestApi\Issue\Issue|mixed|object
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function handle()
    {
        $this->issue->epic_link = $this->remoteEpicIssueKey;
        $this->issue->fix_version_id = $this->removeVersionId;
        if (is_null($this->remoteIssueKey)) {
            $jiraIssue = $this->jiraApi->createIssue($this->issue);
        } else {
            $jiraIssue = $this->jiraApi->updateIssue($this->remoteIssueKey, $this->issue);
        }
        if (!is_null($this->issue->deleted_at)) {
            $this->jiraApi->deleteIssue($this->issue->issue_key);
        }
        return $jiraIssue;
    }

}
