<?php
namespace App\Domains\Issue\Jobs;

use App\Data\Repositories\SlaveJiraIssueRepository;
use App\Data\SlaveJiraIssue;
use Lucid\Foundation\Job;
use App\Data\Issue;

class CreateSlaveJiraIssueJob extends Job
{
    /** @var SlaveJiraIssueRepository */
    private $repository;
    /** @var Issue */
    private $masterJiraIssue;
    /** @var \JiraRestApi\Issue\Issue */
    private $slaveJiraIssue;

    public function __construct(Issue $masterJiraIssue, \JiraRestApi\Issue\Issue $slaveJiraIssue)
    {
        $this->repository = new SlaveJiraIssueRepository(new SlaveJiraIssue());
        $this->masterJiraIssue = $masterJiraIssue;
        $this->slaveJiraIssue = $slaveJiraIssue;
    }

    public function handle()
    {
        return $this->repository->create($this->masterJiraIssue, $this->slaveJiraIssue->key);
    }
}
