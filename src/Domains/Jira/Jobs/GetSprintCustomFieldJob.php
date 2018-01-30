<?php
namespace App\Domains\Jira\Jobs;

use Lucid\Foundation\Job;

class GetSprintCustomFieldJob extends Job
{
    private $restApi;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

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
