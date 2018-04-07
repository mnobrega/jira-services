<?php
namespace App\Domains\Jira\Jobs;

use App\Data\RestApis\Config;
use App\Data\Sprint;
use Lucid\Foundation\Job;

class PublishSprintToJiraJob extends Job
{
    private $jiraAgileApi;
    private $boardId;
    private $sprint;
    private $slaveSprintId;

    /**
     * PublishSprintToJiraJob constructor.
     * @param $jiraInstance
     * @param $boardId
     * @param Sprint $sprint
     * @param $slaveSprintId
     * @throws \Exception
     */
    public function __construct($jiraInstance, $boardId, Sprint $sprint, $slaveSprintId)
    {
        $this->jiraAgileApi = Config::getJiraAgile($jiraInstance);
        $this->boardId = $boardId;
        $this->sprint = $sprint;
        $this->slaveSprintId = $slaveSprintId;
    }


    public function handle()
    {
        if(is_null($this->slaveSprintId)) {
            return $this->jiraAgileApi->createBoardSprint($this->boardId,$this->sprint);
        } else {
            return $this->jiraAgileApi->updateSprint($this->slaveSprintId,$this->sprint);
        }
    }
}
