<?php
namespace App\Domains\Issue\Jobs;

use App\Data\Issue;
use App\Data\Repositories\IssueRepository;
use Lucid\Foundation\Job;

class CreateOrUpdateIssuesJob extends Job
{
    private $jiraIssues;
    private $repository;

    /**
     * CreateOrUpdateIssuesJob constructor.
     * @param $jiraIssues \Jira_Issue[]
     */
    public function __construct($jiraIssues)
    {
        $this->jiraIssues = $jiraIssues;
        $this->repository = new IssueRepository(new Issue());
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        $jobResult = [
            'createdIssues'=>0,
            'updatedIssues'=>0,
        ];
        foreach ($this->jiraIssues as $jiraIssue) {
            $foundIssues = $this->repository->getByAttributes(['key' => $jiraIssue->getKey()]);
            switch (count($foundIssues)) {
                case 0:
                    $this->repository->create($jiraIssue);
                    $jobResult['createdIssues']++;
                    break;
                case 1:
                    $issue = $foundIssues[0];
                    $updated = new \DateTime($jiraIssue->getUpdated());
                    if ($issue->updated != $updated->format("Y-m-d H:i:s")) {
                        $this->repository->update($issue, $jiraIssue);
                        $jobResult['updatedIssues']++;
                    }
                    break;
                default:
                    throw new \Exception("Found more than 1 issue with the same key:".$jiraIssue->getKey());
            }
        }
        return $jobResult;
    }
}
