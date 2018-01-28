<?php
namespace App\Domains\Issue\Jobs;

use App\Data\Issue;
use App\Data\Repositories\IssueRepository;
use Lucid\Foundation\Job;

class UpdateIssuesRankJob extends Job
{
    private $repository;
    private $jiraIssues;

    /**
     * UpdateIssuesRankJob constructor.
     * @param $jiraIssues \JiraRestApi\Issue\Issue[]
     */
    public function __construct(Array $jiraIssues)
    {
        $this->jiraIssues = $jiraIssues;
        $this->repository = new IssueRepository(new Issue());
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ranking = 1;
        foreach ($this->jiraIssues as $jiraIssue) {
            $issue = $this->repository->findBy('key',$jiraIssue->key);
            $this->repository->update($issue,['ranking'=>$ranking]);
            $ranking++;
        }
    }
}
