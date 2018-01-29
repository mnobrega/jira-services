<?php
namespace App\Domains\Sprint\Jobs;

use App\Data\Repositories\SprintRepository;
use App\Data\Sprint;
use Lucid\Foundation\Job;

class CreateOrUpdateSprintsJob extends Job
{
    private $repository;
    private $jiraSprints;

    /**
     * CreateOrUpdateSprintsJob constructor.
     * @param \JiraAgileRestApi\Sprint\Sprint[]|\JiraGreenhopperRestApi\ExperimentalApi\Sprint\Sprint[] $jiraSprints
     */
    public function __construct(Array $jiraSprints)
    {
        $this->repository = new SprintRepository(new Sprint());
    }


    public function handle()
    {
        $sprints = [
            'created'=>array(),
            'updated'=>array(),
        ];
        foreach ($this->jiraSprints as $jiraSprint) {

        }
    }

    public function removethis(){
        $issues = [
            'created'=>array(),
            'updated'=>array(),
        ];
        foreach ($this->jiraIssues as $jiraIssue) {
            $foundIssues = $this->repository->getByAttributes(['key' => $jiraIssue->key]);
            switch (count($foundIssues)) {
                case 0:
                    $createdIssue = $this->repository->create(IssueRepository::getAttributesFromJiraIssue($jiraIssue));
                    $issues['created'][] = $createdIssue;
                    break;
                case 1:
                    $foundIssue = $foundIssues[0];
                    if ($foundIssue->updated != $jiraIssue->fields->updated->format("Y-m-d H:i:s")) {
                        $updatedIssue = $this->repository->update($foundIssue, IssueRepository::getAttributesFromJiraIssue($jiraIssue));
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
