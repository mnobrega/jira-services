<?php
namespace App\Domains\Jira\Jobs;

use App\Data\JiraConfig;
use App\Data\Repositories\JiraConfigRepository;
use Lucid\Foundation\Job;

class GetJiraConfigJob extends Job
{
    private $repository;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->repository = new JiraConfigRepository(new JiraConfig());
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {
        $configs =  $this->repository->all();
        if (count($configs)==1) {
            return $configs[0];
        } else {
            throw new \Exception("Missing JIRA wrapper configuration. Please add it to the database");
        }
    }
}
