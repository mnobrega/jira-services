<?php
namespace App\Domains\Issue\Jobs;

use App\Data\IssueLink;
use App\Data\Repositories\IssueLinkRepository;
use Lucid\Foundation\Job;

class GetUpdatedIssuesLinksByDateTimeIntervalJob extends Job
{
    private $repository;
    private $fromDateTime;
    private $toDateTime;

    /**
     * GetUpdatedIssuesLinksByDateTimeIntervalJob constructor.
     * @param \DateTime $fromDateTime
     * @param \DateTime $toDateTime
     */
    public function __construct(\DateTime $fromDateTime, \DateTime $toDateTime)
    {
        $this->repository = new IssueLinkRepository(new IssueLink());
        $this->fromDateTime = $fromDateTime;
        $this->toDateTime = $toDateTime;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]
     */
    public function handle()
    {
        return $this->repository->getUpdatedIssuesLinksByDateTimeInterval(
            $this->fromDateTime->format("Y-m-d H:i:s"),
            $this->toDateTime->format("Y-m-d H:i:s"));
    }
}
