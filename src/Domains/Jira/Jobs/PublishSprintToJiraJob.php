<?php
namespace App\Domains\Jira\Jobs;

use App\Data\RestApis\Config;
use App\Data\Sprint;
use Lucid\Foundation\Job;

class PublishSprintToJiraJob extends Job
{
    private $jiraAgileApi;
    private $boardName;
    private $sprint;
    private $remoteSprintId;

    /**
     * PublishSprintToJiraJob constructor.
     * @param $jiraInstance
     * @param $boardName
     * @param Sprint $sprint
     * @param $remoteSprintId
     * @throws \Exception
     */
    public function __construct($jiraInstance, $boardName, Sprint $sprint, $remoteSprintId)
    {
        $this->jiraAgileApi = Config::getJiraAgile($jiraInstance);
        $this->boardName = $boardName;
        $this->sprint = $sprint;
        $this->remoteSprintId = $remoteSprintId;
    }

    public function handle()
    {
        if(is_null($this->remoteSprintId)) {
            return $this->jiraAgileApi->createSprint($this->sprint);
        } else {
            return $this->jiraAgileApi->updateSprint($this->remoteSprintId,$this->sprint);
        }
    }
}
