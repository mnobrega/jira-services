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
        $result = [
            'issues'=>array(),
            'total_time_spent_in_hours'=>0,
        ];
        foreach ($this->issues as $issue) {
            $issueResult = [
                'issue'=>$issue,
                'time_spent_in_hours'=>0,
            ];
            if ($issue->status==\App\Data\Issue::STATUS_IN_PROGRESS) {

            } else {

            }


            if (!is_null($issueHistories)) {
                foreach ($issueHistories as $key=>$issueHistory) {
                    $currentHistoryDateTime = new \DateTime($issueHistory->created);
                    if ($issue->status==\App\Data\Issue::STATUS_IN_PROGRESS) {

                    } elseif ($key>0 && $issueHistory->from_string == \App\Data\Issue::STATUS_IN_PROGRESS) {

                    }


//                    if ($key>0 &&
//                        $issueHistory->from_string==\App\Data\Issue::STATUS_IN_PROGRESS &&
//                        $currentHistoryDateTime->getTimestamp()>=$this->from->getTimestamp()) {
//
//                        $previousHistoryDateTime = new \DateTime($issueHistories[$key-1]->created);
//
//                        $issueResult['time_spent_in_hours'] +=
//                            round((min($currentHistoryDateTime->getTimestamp(),$this->to->getTimestamp()) -
//                                    max($previousHistoryDateTime->getTimestamp(),$this->from->getTimestamp()))/(60*60),
//                                2);
//                    }
                }
//                if ($issueResult['time_spent_in_hours']>0) {
//                    $result['issues'][] = $issueResult;
//                    $result['total_time_spent_in_hours'] += $issueResult['time_spent_in_hours'];
//                }
            }
        }
        return $result;
    }

    private function getInProgressTimeSpent($issueId)
    {
        $issueHistories = $this->repository->getIssueHistoriesByIssueId($issueId);
        foreach ($issueHistories as $issueHistory) {

        }
    }
}
