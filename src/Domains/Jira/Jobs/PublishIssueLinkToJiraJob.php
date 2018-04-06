<?php
namespace App\Domains\Jira\Jobs;

use App\Data\IssueLink;
use App\Data\RestApis\JiraApi;
use App\Data\SlaveJiraIssue;
use Lucid\Foundation\Job;

class PublishIssueLinkToJiraJob extends Job
{
    private $jiraApi;
    private $issueLink;
    private $slaveJiraIssueLink;
    private $slaveJiraIssue;
    private $inwardSlaveJiraIssue;
    private $outwardSlaveJiraIssue;

    /**
     * PublishIssueLinkToJiraJob constructor.
     * @param $jiraInstance
     * @param IssueLink $issueLink
     * @param $slaveJiraIssueLink
     * @param SlaveJiraIssue $slaveJiraIssue
     * @param SlaveJiraIssue|null $inwardSlaveJiraIssue
     * @param SlaveJiraIssue|null $outwardSlaveJiraIssue
     */
    public function __construct($jiraInstance, IssueLink $issueLink, $slaveJiraIssueLink,
                                SlaveJiraIssue $slaveJiraIssue, $inwardSlaveJiraIssue, $outwardSlaveJiraIssue)
    {
        $this->jiraApi = new JiraApi($jiraInstance);
        $this->issueLink = $issueLink;
        $this->slaveJiraIssueLink = $slaveJiraIssueLink;
        $this->slaveJiraIssue = $slaveJiraIssue;
        $this->inwardSlaveJiraIssue = $inwardSlaveJiraIssue;
        $this->outwardSlaveJiraIssue = $outwardSlaveJiraIssue;
    }

    /**
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function handle()
    {
        if (is_null($this->slaveJiraIssueLink)) {
            $jiraIssueLink = $this->jiraApi->createIssueLink($this->issueLink, $this->slaveJiraIssue,
                $this->inwardSlaveJiraIssue, $this->outwardSlaveJiraIssue);
        }

        $slaveJiraIssue = $this->jiraApi->getIssue($this->slaveJiraIssue->issue_key);
        dd($slaveJiraIssue);
    }
}
