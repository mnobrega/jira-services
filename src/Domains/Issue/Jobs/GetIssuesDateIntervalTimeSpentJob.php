<?php
namespace App\Domains\Issue\Jobs;

use App\Data\IssueHistory;
use App\Data\Repositories\IssueHistoryRepository;
use App\Data\RestApis\JiraApi;
use Lucid\Foundation\Job;

class GetIssuesDateIntervalTimeSpentJob extends Job
{
    private $repository;
    private $issues;
    private $from;
    private $to;

    /**
     * GetIssuesDateIntervalTimeSpentJob constructor.
     * @param \App\Data\Issue [] $issues
     * @param \DateTime $from
     * @param \DateTime $to
     */
    public function __construct($issues, \DateTime $from, \DateTime $to)
    {
        $this->repository = new IssueHistoryRepository(new IssueHistory());
        $this->issues = $issues;
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        $result = array();
        foreach ($this->issues as $issue) {
            $issueResult = [
                'issue'=>$issue,
                'time_spent_in_seconds'=>0,
            ];
            $issueHistories = $this->repository->getIssueHistoriesByIssueId($issue->id);
            dump(count($issueHistories));
            if (!is_null($issueHistories)) {
                foreach ($issueHistories as $key=>$issueHistory) {
                    $toStatus = JiraApi::$slaveIssueStatusTransitionMapping[$issueHistory->to_string];
                    if ($key>0 && in_array($toStatus,["To Do","Done"]) && $issueHistory->from_string=='In Progress') {
                        $currentDateTime = new \DateTime($issueHistory->created);
                        $previousDateTime = new \DateTime($issueHistories[$key-1]->created);
                        $issueResult['time_spent_in_seconds'] +=
                            $currentDateTime->getTimestamp() - $previousDateTime->getTimestamp();
                    }
                }
            }
            $result[] = $issueResult;
        }
        dd($result);
    }
}
