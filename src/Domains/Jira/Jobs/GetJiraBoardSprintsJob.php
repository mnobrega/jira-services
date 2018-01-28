<?php
namespace App\Domains\Jira\Jobs;

use App\Data\RestApis\CredentialsFactory;
use Lucid\Foundation\Job;


class GetJiraBoardSprintsJob extends Job
{
    private $jiraAgile;
    private $jiraBoardName;

    /**
     * GetJiraBoardSprintsJob constructor.
     * @param $jiraInstance
     * @param $jiraVersion
     * @param $jiraBoardName
     * @throws \Exception
     */
    public function __construct($jiraInstance, $jiraVersion, $jiraBoardName)
    {
        $this->jiraBoardName = $jiraBoardName;
        $this->jiraAgile = CredentialsFactory::getJiraAgile($jiraVersion,$jiraInstance);
    }

    public function handle()
    {

    }
}
