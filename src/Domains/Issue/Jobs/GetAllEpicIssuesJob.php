<?php
namespace App\Domains\Issue\Jobs;

use App\Data\Issue;
use App\Data\Repositories\IssueRepository;
use Lucid\Foundation\Job;

class GetAllEpicIssuesJob extends Job
{
    private $repository;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->repository = new IssueRepository(new Issue());
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function handle()
    {
        return $this->repository->getByAttributes(["type"=>Issue::TYPE_EPIC]);
    }
}
