<?php
namespace App\Domains\Jira\Jobs;

use App\Data\RestApis\Config;
use App\Data\SlaveJiraIssue;
use Lucid\Foundation\Job;

class PublishIssueRankJob extends Job
{
    private $jiraAgileApi;
    private $slaveJiraIssue;
    private $rankBeforeSlaveJiraIssue;

    /**
     * PublishIssueRankJob constructor.
     * @param $jiraInstance
     * @param SlaveJiraIssue $slaveJiraIssue
     * @param SlaveJiraIssue $rankBeforeSlaveJiraIssue
     * @throws \Exception
     */
    public function __construct($jiraInstance, SlaveJiraIssue $slaveJiraIssue, SlaveJiraIssue $rankBeforeSlaveJiraIssue)
    {
        $this->jiraAgileApi = Config::getJiraAgile($jiraInstance);
        $this->slaveJiraIssue = $slaveJiraIssue;
        $this->rankBeforeSlaveJiraIssue = $rankBeforeSlaveJiraIssue;
    }

    public function handle()
    {
        return $this->jiraAgileApi->rankIssueABeforeIssueB($this->slaveJiraIssue->slave_issue_key,
            $this->rankBeforeSlaveJiraIssue->slave_issue_key);
    }
}
