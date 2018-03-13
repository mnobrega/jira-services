<?php
namespace App\Domains\Issue\Jobs;

use App\Data\IssueHistory;
use App\Data\Repositories\IssueHistoryRepository;
use Lucid\Foundation\Job;

class CreateOrUpdateIssueHistoriesJob extends Job
{
    private $repository;
    private $issue;
    private $issueHistories;

    /**
     * CreateOrUpdateIssueHistoriesJob constructor.
     * @param $issue
     * @param $issueHistories
     */
    public function __construct($issue, $issueHistories)
    {
        $this->repository = new IssueHistoryRepository(new IssueHistory());
        $this->issue = $issue;
        $this->issueHistories = $issueHistories;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function handle()
    {
        $issueHistories = array(
            'created'=>array(),
            'updated'=>array(),
        );
        foreach ($this->issueHistories as $issueHistory)
        {
            //WHY: because description history items are too long and i meanwhile I will not store them
            if(IssueHistoryRepository::getFieldFromJiraIssueHistory($issueHistory)==IssueHistoryRepository::FIELD_NAME_STATUS) {
                $searchResult = $this->repository->getByAttributes(array('jira_id'=>$issueHistory->id));
                switch(count($searchResult))
                {
                    case 0:
                        $issueHistory = $this->repository->create(IssueHistoryRepository::getAttributesFromJiraIssueHistory($issueHistory),
                            $this->issue);
                        $issuesHistories['created'][] = $issueHistory;
                        break;
                    case 1:
                        //do nothing for now
                        break;
                    default:
                        throw new \Exception("Found more than 1 issueHistory with the same id:".$issueHistory->id);
                }
            }
        }
        return $issueHistories;
    }
}
