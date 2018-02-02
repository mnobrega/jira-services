<?php
namespace App\Domains\Issue\Jobs;

use App\Data\Issue;
use App\Data\Repositories\SlaveJiraIssueRepository;
use App\Data\SlaveJiraIssue;
use Lucid\Foundation\Job;

class SearchSlaveJiraIssueByMasterJiraIssueJob extends Job
{
    private $repository;
    private $masterJiraIssue;

    /**
     * SearchSlaveJiraIssueByMasterJiraIssueJob constructor.
     * @param Issue $masterJiraIssue
     */
    public function __construct(Issue $masterJiraIssue)
    {
        $this->repository = new SlaveJiraIssueRepository(new SlaveJiraIssue());
        $this->masterJiraIssue = $masterJiraIssue;
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        return $this->repository->searchByMasterJiraKey($this->masterJiraIssue->key);
    }
}
