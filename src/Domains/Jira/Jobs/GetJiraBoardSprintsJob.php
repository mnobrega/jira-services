<?php
namespace App\Domains\Jira\Jobs;

use Lucid\Foundation\Job;

class GetJiraBoardSprintsJob extends Job
{
    private $api;
    private $jiraBoardName;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($jiraBoardName)
    {
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
