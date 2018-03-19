<?php
namespace App\Domains\Issue\Jobs;

use App\Data\Issue;
use App\Data\Repositories\IssueRepository;
use Lucid\Foundation\Job;

class GetIssuesCountJob extends Job
{
    private $repository;

    /**
     * GetIssuesCountJob constructor.
     */
    public function __construct()
    {
        $this->repository = new IssueRepository(new Issue());
    }

    /**
     * @return int
     */
    public function handle()
    {
        return $this->repository->getCount();
    }
}
