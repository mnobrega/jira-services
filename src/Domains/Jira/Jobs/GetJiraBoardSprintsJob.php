<?php
namespace App\Domains\Jira\Jobs;

use App\Data\RestApis\JiraAgile;
use App\Data\RestApis\JiraApi;
use Lucid\Foundation\Job;

class GetJiraBoardSprintsJob extends Job
{
    private $jiraApi;
    private $jiraBoardName;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($jiraInstance, $jiraBoardName)
    {
        $this->jiraApi = new JiraAgile($jiraInstance);
        $this->jiraBoardName = $jiraBoardName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
    }
}
