<?php
namespace App\Domains\Issue\Jobs;

use App\Data\Repositories\SlaveJiraIssueRepository;
use App\Data\SlaveJiraIssue;
use Lucid\Foundation\Job;

class GetAllSlaveJiraIssuesJob extends Job
{
    private $repository;

    /**
     * GetAllSlaveJiraIssuesJob constructor.
     */
    public function __construct()
    {
        $this->repository = new SlaveJiraIssueRepository(new SlaveJiraIssue());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function handle()
    {
        return $this->repository->all();
    }
}
