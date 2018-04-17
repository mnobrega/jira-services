<?php
namespace App\Domains\Issue\Jobs;

use App\Data\IssueHistory;
use App\Data\Repositories\IssueHistoryRepository;
use App\Data\RestApis\JiraApi;
use Lucid\Foundation\Job;

class DateInterval
{
    /** @var \DateTime */
    public $start;
    /** @var \DateTime */
    public $end;

    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
    }
}

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
                'issue' => $issue,
                'time_spent_in_hours' => 0,
            ];
            $activeIntervals = array();
            $activeIntervalStart = null;
            foreach ($issue->histories()->orderBy('created', 'asc')->get() as $history) {
                /** @var $history IssueHistory */
                if ($history->to_string == \App\Data\Issue::STATUS_IN_PROGRESS) {
                    $activeIntervalStart = new \DateTime($history->created);
                }
                if (!is_null($activeIntervalStart) && $history->from_string == \App\Data\Issue::STATUS_IN_PROGRESS) {
                    $activeIntervalEnd = new \DateTime($history->created);
                    $activeIntervals[] = new DateInterval($activeIntervalStart,$activeIntervalEnd);
                    $activeIntervalStart = null;
                }
            }
            if ($issue->status==\App\Data\Issue::STATUS_IN_PROGRESS) {
                $activeIntervals[] = new DateInterval($activeIntervalStart,now());
            }

            foreach ($activeIntervals as $activeInterval) {
                $start = max($activeInterval->start->getTimestamp(),$this->from->getTimestamp());
                $end = min($activeInterval->end->getTimestamp(),$this->to->getTimestamp());
                /** @var $activeInterval DateInterval */
                $issueResult['time_spent_in_hours'] += round(($end-$start)/3600,2);

            }

            if ($issueResult['time_spent_in_hours']>0) {
                $result['issues'][] = $issueResult;
                $result['total_time_spent_in_hours'] += $issueResult['time_spent_in_hours'];
            }
        }

        return $result;
    }

}
