<?php
namespace App\Domains\Issue\Jobs;

use App\Data\Repositories\SlaveJiraIssueRepository;
use App\Data\SlaveJiraIssue;
use Lucid\Foundation\Job;

class DeleteDeadSlaveIssuesJob extends Job
{
    private $repository;
    private $deletedMasterIssueKeys;

    /**
     * DeleteDeadSlaveIssuesJob constructor.
     * @param array $deletedMasterIssueKeys
     */
    public function __construct(Array $deletedMasterIssueKeys)
    {
        $this->repository = new SlaveJiraIssueRepository(new SlaveJiraIssue());
        $this->deletedMasterIssueKeys = $deletedMasterIssueKeys;
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        foreach ($this->deletedMasterIssueKeys as $masterIssueKey) {
            $slaveJiraIssue = $this->repository->searchByMasterIssueKey($masterIssueKey);
            if (!is_null($slaveJiraIssue)) {
                $this->repository->delete($slaveJiraIssue);
            }
        }
    }
}
