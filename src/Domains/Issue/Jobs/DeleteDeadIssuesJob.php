<?php
namespace App\Domains\Issue\Jobs;

use App\Data\Issue;
use App\Data\Repositories\IssueRepository;
use Lucid\Foundation\Job;

class DeleteDeadIssuesJob extends Job
{
    private $repository;
    private $liveIssueKeys;

    /**
     * DeleteDeadIssuesJob constructor.
     * @param array $liveIssueKeys
     */
    public function __construct(Array $liveIssueKeys)
    {
        $this->repository = new IssueRepository(new Issue());
        $this->liveIssueKeys = $liveIssueKeys;
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        $jobResult = [
            'deleted'=>0,
            'keeped'=>0,
            'deletedIssueKeys'=>array(),
        ];
        $issues = $this->repository->all();
        foreach ($issues as $issue) {
            /** @var $issue \App\Data\Issue */
            if (!in_array($issue->issue_key,$this->liveIssueKeys)) {
                $this->repository->delete($issue);
                $jobResult['deleted']++;
                $jobResult['deletedIssueKeys'][] = $issue->issue_key;
            } else {
                $jobResult['keeped']++;
            }
        }
        return $jobResult;
    }
}
