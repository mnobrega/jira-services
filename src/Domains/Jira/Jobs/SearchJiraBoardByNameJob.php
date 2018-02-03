<?php
namespace App\Domains\Jira\Jobs;

use App\Data\RestApis\Config;
use Lucid\Foundation\Job;

class SearchJiraBoardByNameJob extends Job
{
    private $jiraAgileApi;
    private $jiraBoardName;

    /**
     * SearchJiraBoardByNameJob constructor.
     * @param $jiraInstance
     * @param $boardName
     * @throws \Exception
     */
    public function __construct($jiraInstance, $jiraBoardName)
    {
        $this->jiraAgileApi = Config::getJiraAgile($jiraInstance);
        $this->jiraBoardName = $jiraBoardName;
    }

    /**
     * @return \JiraAgileRestApi\Board\Board[]|\JiraGreenhopperRestApi\ExperimentalApi\Board\Board|null
     */
    public function handle()
    {
        return $this->jiraAgileApi->getBoardByName($this->jiraBoardName);
    }
}
