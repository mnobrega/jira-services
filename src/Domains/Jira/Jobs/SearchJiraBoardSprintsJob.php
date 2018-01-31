<?php
namespace App\Domains\Jira\Jobs;

use App\Data\RestApis\Config;
use Lucid\Foundation\Job;


class SearchJiraBoardSprintsJob extends Job
{
    private $jiraAgile;
    private $jiraBoardName;

    /**
     * GetJiraBoardSprintsJob constructor.
     * @param $jiraInstance
     * @param $jiraBoardName
     * @throws \Exception
     */
    public function __construct($jiraInstance, $jiraBoardName)
    {
        $this->jiraBoardName = $jiraBoardName;
        $this->jiraAgile = Config::getJiraAgile($jiraInstance);
    }


    public function handle()
    {
        $board = $this->jiraAgile->getBoardByName($this->jiraBoardName);
        return !is_null($board)?$this->jiraAgile->getBoardOpenSprints($board->id):array();
    }
}
