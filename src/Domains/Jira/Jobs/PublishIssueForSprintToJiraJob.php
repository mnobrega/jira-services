<?php
namespace App\Domains\Jira\Jobs;

use App\Data\RestApis\Config;
use App\Data\SlaveJiraIssue;
use App\Data\SlaveJiraSprint;
use Lucid\Foundation\Job;

class PublishIssueForSprintToJiraJob extends Job
{
    private $jiraAgileApi;
    private $slaveJiraSprint;
    private $slaveJiraIssue;

    /**
     * PublishIssueForSprintToJiraJob constructor.
     * @param $jiraInstance
     * @param SlaveJiraIssue $slaveJiraIssue
     * @param SlaveJiraSprint $slaveJiraSprint
     * @throws \Exception
     */
    public function __construct($jiraInstance, SlaveJiraIssue $slaveJiraIssue, SlaveJiraSprint $slaveJiraSprint)
    {
        $this->jiraAgileApi = Config::getJiraAgile($jiraInstance);
        $this->slaveJiraIssue = $slaveJiraIssue;
        $this->slaveJiraSprint = $slaveJiraSprint;
    }


    public function handle()
    {
        $this->jiraAgileApi->moveIssueToSprint($this->slaveJiraIssue->slave_issue_key,
            $this->slaveJiraSprint->slave_sprint_jira_id);
    }
}
