<?php
namespace App\Domains\Issue\Jobs;

use App\Data\Issue;
use App\Data\Repositories\SlaveJiraIssueRepository;
use App\Data\SlaveJiraIssue;
use Lucid\Foundation\Job;

class GetSlaveJiraIssuesByIssuesJob extends Job
{
    private $repository;
    /** @var Issue[] */
    private $issues;

    /**
     * GetSlaveJiraIssuesByIssuesJob constructor.
     * @param $issues
     */
    public function __construct($issues)
    {
        $this->repository = new SlaveJiraIssueRepository(new SlaveJiraIssue());
        $this->issues = $issues;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function handle()
    {
        $slaveIssues = array();
        foreach ($this->issues as $issue) {
            $foundIssues = $this->repository->getByAttributes(['master_issue_key'=>$issue->key]);
            if (count($foundIssues)==1) {
                $slaveIssues[] = $foundIssues[0];
            }
        }
        return $slaveIssues;
    }
}
