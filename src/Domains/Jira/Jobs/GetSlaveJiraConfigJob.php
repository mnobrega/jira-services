<?php
namespace App\Domains\Jira\Jobs;

use App\Data\Repositories\SlaveJiraConfigRepository;
use App\Data\SlaveJiraConfig;
use Lucid\Foundation\Job;

class GetSlaveJiraConfigJob extends Job
{
    private $repository;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->repository = new SlaveJiraConfigRepository(new SlaveJiraConfig());
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
