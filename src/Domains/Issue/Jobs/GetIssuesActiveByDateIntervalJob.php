<?php
namespace App\Domains\Issue\Jobs;

use App\Data\Issue;
use App\Data\Repositories\IssueRepository;
use Lucid\Foundation\Job;

class GetIssuesActiveByDateIntervalJob extends Job
{
    private $repository;
    private $from;
    private $to;

    /**
     * GetIssuesDoneByDateIntervalJob constructor.
     * @param \DateTime $from
     * @param \DateTime $to
     */
    public function __construct(\DateTime $from, \DateTime $to)
    {
        $this->repository = new IssueRepository(new Issue());
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function handle()
    {
        return $this->repository->getActiveIssuesByDateTimeInterval(
            $this->from->format("Y-m-d H:i:s"),
            $this->to->format("Y-m-d H:i:s"));
    }
}
