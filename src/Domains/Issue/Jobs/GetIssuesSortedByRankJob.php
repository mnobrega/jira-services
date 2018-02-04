<?php
namespace App\Domains\Issue\Jobs;

use App\Data\Issue;
use App\Data\Repositories\IssueRepository;
use Lucid\Foundation\Job;

class GetIssuesSortedByRankJob extends Job
{
    private $repository;
    private $sortOrder;

    public function __construct($sortOrder)
    {
        $this->repository = new IssueRepository(new Issue());
        $this->sortOrder = $sortOrder;
    }

    public function handle()
    {
        return $this->repository->getIssuesSortedByRank($this->sortOrder);
    }
}
