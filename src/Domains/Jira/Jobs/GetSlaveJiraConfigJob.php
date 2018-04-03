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
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function handle()
    {
        return $this->repository->all();
    }
}
