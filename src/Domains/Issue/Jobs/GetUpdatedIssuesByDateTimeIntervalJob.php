<?php
namespace App\Domains\Issue\Jobs;

use Lucid\Foundation\Job;
use App\Data\Issue;
use App\Data\Repositories\IssueRepository;

class GetUpdatedIssuesByDateTimeIntervalJob extends Job
{
    private $repository;
    private $fromDateTime;
    private $toDateTime;

    /**
     * GetCreatedIssuesByDateTimeIntervalJob constructor.
     * @param \DateTime $fromDateTime
     * @param \DateTime $toDateTime
     */
    public function __construct(\DateTime $fromDateTime, \DateTime $toDateTime)
    {
        $this->fromDateTime = $fromDateTime;
        $this->toDateTime = $toDateTime;
        $this->repository = new IssueRepository(new Issue());
    }


    public function handle()
    {
        return $this->repository->getUpdatedIssuesByDateTimeInterval($this->fromDateTime->format("Y-m-d H:i:s"),
            $this->toDateTime->format("Y-m-d H:i:s"));
    }
}
