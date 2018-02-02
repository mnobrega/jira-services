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

    /**
     * PublishIssueToJiraJob constructor.
     * @param string $jiraInstance
     * @param Issue $issue
     * @param string|null $remoteIssueKey
     */
    public function __construct($jiraInstance, Issue $issue, $remoteIssueKey=null)
    {
        $this->jiraApi = new JiraApi($jiraInstance);
        $this->issue = $issue;
        $this->remoteIssueKey = $remoteIssueKey;
    }

    /**
     * @return \JiraRestApi\Issue\Issue|mixed|object
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function handle()
    {
        if (is_null($this->remoteIssueKey)) {
            return $this->jiraApi->create($this->issue);
        } else {
            return $this->jiraApi->update($this->remoteIssueKey, $this->issue);
        }
    }

}
