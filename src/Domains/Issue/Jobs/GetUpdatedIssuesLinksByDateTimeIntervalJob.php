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
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(\DateTime $fromDateTime, \DateTime $toDateTime)
    {
        $this->repository = new IssueLinkRepository(new IssueLink());
        $this->fromDateTime = $fromDateTime;
        $this->toDateTime = $toDateTime;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function handle()
    {
        return $this->repository->getUpdatedIssuesLinksByDateTimeInterval(
            $this->fromDateTime->format("Y-m-d H:i:s"),
            $this->toDateTime->format("Y-m-d H:i:s"));
    }
}
