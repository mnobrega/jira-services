<?php
namespace App\Domains\Jira\Jobs;

use App\Data\RestApis\Config;
use Lucid\Foundation\Job;


class SearchJiraBoardSprintsJob extends Job
{
    private $jiraAgile;
    private $jiraBoardId;

    /**
     * SearchJiraBoardSprintsJob constructor.
     * @param $jiraInstance
     * @param $jiraBoardId
     * @throws \Exception
     */
    public function __construct($jiraInstance, $jiraBoardId)
    {
        $this->jiraBoardId = $jiraBoardId;
        $this->jiraAgile = Config::getJiraAgile($jiraInstance);
    }


    public function handle()
    {
        return $this->jiraAgile->getBoardOpenSprints($this->jiraBoardId);
    }
}
