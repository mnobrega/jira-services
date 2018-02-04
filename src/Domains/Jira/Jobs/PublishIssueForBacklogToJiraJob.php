<?php
namespace App\Domains\Jira\Jobs;

use App\Data\RestApis\Config;
use App\Data\SlaveJiraIssue;
use Lucid\Foundation\Job;

class PublishIssueForBacklogToJiraJob extends Job
{
    private $jiraAgileApi;
    private $slaveJiraIssue;

    /**
     * PublishIssueForBacklogToJiraJob constructor.
     * @param $jiraInstance
     * @param $slaveJiraIssue
     * @throws \Exception
     */
    public function __construct($jiraInstance, SlaveJiraIssue $slaveJiraIssue)
    {
        $this->jiraAgileApi = Config::getJiraAgile($jiraInstance);
        $this->slaveJiraIssue =$slaveJiraIssue;
    }

    public function handle()
    {
        return $this->jiraAgileApi->moveIssueToBacklog($this->slaveJiraIssue->slave_issue_key);
    }
}
