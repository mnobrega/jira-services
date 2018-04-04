<?php
namespace App\Domains\Issue\Jobs;

use App\Data\Issue;
use App\Data\Repositories\IssueRepository;
use Lucid\Foundation\Job;

class CreateOrUpdateIssuesJob extends Job
{
    private $jiraIssues;
    private $jiraFields;
    private $repository;

    /**
     * CreateOrUpdateIssuesJob constructor.
     * @param $jiraIssues \JiraRestApi\Issue\Issue[]
     * @param $jiraFields \JiraRestApi\Field\Field[]
     */
    public function __construct($jiraIssues, $jiraFields)
    {
        $this->jiraIssues = $jiraIssues;
        $this->jiraFields = $jiraFields;
        $this->repository = new IssueRepository(new Issue());
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        $issues = [
            'created'=>array(),
            'updated'=>array(),
        ];
        foreach ($this->jiraIssues as $jiraIssue) {
            $foundIssues = $this->repository->getByAttributes(['issue_key' => $jiraIssue->key]);
            switch (count($foundIssues)) {
                case 0:
                    $createdIssue = $this->repository->create(IssueRepository::getAttributesFromJiraIssue($jiraIssue,
                        $this->jiraFields));
                    $issues['created'][] = $createdIssue;
                    break;
                case 1:
                    $foundIssue = $foundIssues[0];
                    if ($foundIssue->updated != $jiraIssue->fields->updated->format("Y-m-d H:i:s")) {
                        $updatedIssue = $this->repository->update($foundIssue,
                            IssueRepository::getAttributesFromJiraIssue($jiraIssue, $this->jiraFields));
                        $issues['updated'][] = $updatedIssue;
                    }
                    break;
                default:
                    throw new \Exception("Found more than 1 issue with the same key:".$jiraIssue->key);
            }
        }
        return $issues;
    }
}
