<?php
namespace App\Domains\JiraClient\Jobs;

use Lucid\Foundation\Job;

class GetJiraIssuesByJQLJob extends Job
{
    private $host;
    private $username;
    private $password;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($host, $username, $password)
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        dd($this->host);
    }
}
