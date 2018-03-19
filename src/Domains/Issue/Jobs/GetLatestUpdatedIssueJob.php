<?php
namespace App\Domains\Issue\Jobs;

use App\Data\Issue;
use App\Data\Repositories\IssueRepository;
use Lucid\Foundation\Job;

class GetLatestUpdatedIssueJob extends Job
{
    private $repository;

    /**
     * GetLatestUpdatedIssueJob constructor.
     */
    public function __construct()
    {
        $this->repository = new IssueRepository(new Issue());
    }

    /**
     * @return $this
     */
    public function handle()
    {
        return $this->repository->getLatestUpdatedIssue();
    }
}
