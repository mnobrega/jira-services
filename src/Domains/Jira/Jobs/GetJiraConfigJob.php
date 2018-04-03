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
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function handle()
    {
        return $this->repository->all();
    }
}
