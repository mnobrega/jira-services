<?php
namespace App\Domains\Issue\Jobs;

use App\Data\Issue;
use App\Data\Repositories\IssueRepository;
use Lucid\Foundation\Job;

class GetIssueDistinctVersionsJob extends Job
{
    private $repository;

    /**
     * GetAllIssueVersionIdsJob constructor.
     */
    public function __construct()
    {
        $this->repository = new IssueRepository(new Issue());
    }

    /**
     * @return mixed
     */
    public function handle()
    {
        return $this->repository->getIssuesDistinctVersions();
    }
}
