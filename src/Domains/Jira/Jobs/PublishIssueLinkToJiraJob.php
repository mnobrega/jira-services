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
     * @return mixed
     * @throws \Exception
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function handle()
    {
        if (is_null($this->slaveJiraIssueLink)) {
            $this->jiraApi->createIssueLink($this->issueLink, $this->slaveJiraIssue, $this->inwardSlaveJiraIssue,
                $this->outwardSlaveJiraIssue);
        }
        if (!is_null($this->issueLink->deleted_at)) {
            $this->jiraApi->deleteIssueLink($this->slaveJiraIssueLink);
        } else {
            $issue = $this->jiraApi->getIssue($this->slaveJiraIssue->slave_issue_key);
            foreach ($issue->fields->issuelinks as $issueLink) {
                if ($issueLink->type->name==$this->issueLink->type && $this->issueLink->type &&
                    (
                        (property_exists($issueLink,"inwardIssue") && $issueLink->inwardIssue->key==$this->inwardSlaveJiraIssue->slave_issue_key) ||
                        (property_exists($issueLink,"outwardIssue") && $issueLink->outwardIssue->key==$this->outwardSlaveJiraIssue->slave_issue_key)
                    )
                ) {
                    return $issueLink;
                }
            }
        }

        return null;
    }
}
