<?php
namespace App\Domains\Jira\Jobs;

use App\Data\Issue;
use App\Data\RestApis\JiraApi;
use Lucid\Foundation\Job;

class PublishIssueToJiraJob extends Job
{
    /** @var JiraApi */
    private $jiraApi;
    /** @var Issue */
    private $issue;
    /** @var \App\Data\SlaveJiraIssue */
    private $slaveJiraIssue;

    /**
     * PublishIssueToJiraJob constructor.
     * @param $jiraInstance
     * @param Issue $issue
     * @param \App\Data\SlaveJiraIssue $slaveJiraIssue
     */
    public function __construct($jiraInstance, Issue $issue, \App\Data\SlaveJiraIssue $slaveJiraIssue)
    {
        $this->jiraApi = new JiraApi($jiraInstance);
        $this->issue = $issue;
        $this->slaveJiraIssue = $slaveJiraIssue;
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        if (is_null($this->slaveJiraIssue)) {
            return $this->jiraApi->create($this->issue);
        } else {
            return $this->jiraApi->update($this->slaveJiraIssue->slave_issue_key, $issue);
        }
    }

}
