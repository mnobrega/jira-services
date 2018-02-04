<?php
namespace App\Domains\Issue\Jobs;

use App\Data\Issue;
use App\Data\Repositories\IssueRepository;
use Lucid\Foundation\Job;

class GetIssueByKeyJob extends Job
{
    private $repository;
    private $issueKey;

    /**
     * GetIssueByKeyJob constructor.
     * @param $issueKey
     */
    public function __construct($issueKey)
    {
        $this->repository = new IssueRepository(new Issue());
        $this->issueKey = $issueKey;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {
        return $this->repository->getByKey($this->issueKey);
    }
}
