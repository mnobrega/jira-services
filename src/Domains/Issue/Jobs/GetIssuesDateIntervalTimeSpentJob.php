<?php
namespace App\Domains\Issue\Jobs;

use App\Data\IssueHistory;
use App\Data\Repositories\IssueHistoryRepository;
use App\Data\Repositories\IssueRepository;
use Lucid\Foundation\Job;
use Nuxia\BusinessDayManipulator\Manipulator;

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
    const WORKING_HOURS = 8;

    private $repository;
    private $issues;
    private $from;
    private $to;
    private $businessDayManipulator;

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
        $this->businessDayManipulator = new Manipulator(config('business-day-manipulator.freeDays'),
            config('business-day-manipulator.freeWeekDays'),config('business-day-manipulator.holidays'));
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
                'time_impeded_in_hours' => 0,
            ];

            $activeIntervals = array();
            $activeIntervalStart = null;
            $impedimentIntervals = array();
            $impedimentIntervalStart = null;
            foreach ($issue->histories()->orderBy('created', 'asc')->get() as $history) {
                /** @var $history IssueHistory */
                if ($history->field == IssueHistoryRepository::FIELD_NAME_STATUS) {
                    if ($history->to_string == IssueRepository::ISSUE_STATUS_IN_PROGRESS) {
                        $activeIntervalStart = new \DateTime($history->created);
                    }
                    if (!is_null($activeIntervalStart) && $history->from_string == IssueRepository::ISSUE_STATUS_IN_PROGRESS) {
                        $activeIntervalEnd = new \DateTime($history->created);
                        if ($activeIntervalEnd->getTimestamp() >= $this->from->getTimestamp() &&
                            $activeIntervalStart->getTimestamp() <= $this->to->getTimestamp()) {
                            $activeIntervals[] = new DateInterval($activeIntervalStart, $activeIntervalEnd);
                        }
                        $activeIntervalStart = null;
                    }
                }
                if ($history->field == IssueHistoryRepository::FIELD_NAME_FLAGGED) {
                    if ($history->to_string == IssueRepository::ISSUE_IMPEDIMENT) {
                        $impedimentIntervalStart = new \DateTime($history->created);
                    }
                    if (!is_null($impedimentIntervalStart) && $history->from_string == IssueRepository::ISSUE_IMPEDIMENT) {
                        $inactiveIntervalEnd = new \DateTime($history->created);
                        if ($inactiveIntervalEnd->getTimestamp() >= $this->from->getTimestamp() &&
                            $impedimentIntervalStart->getTimestamp() <= $this->to->getTimestamp()) {
                            $impedimentIntervals[] = new DateInterval($impedimentIntervalStart,$inactiveIntervalEnd);
                        }
                        $impedimentIntervalStart = null;
                    }
                }
            }
            if (!is_null($activeIntervalStart) &&
                $activeIntervalStart->getTimestamp() <= $this->to->getTimestamp()) {
                $activeIntervals[] = new DateInterval($activeIntervalStart,now());
            }
            if (!is_null($impedimentIntervalStart) &&
                $impedimentIntervalStart->getTimestamp() <= $this->to->getTimestamp()) {
                $impedimentIntervals[] = new DateInterval($impedimentIntervalStart,now());
            }

            foreach ($activeIntervals as $activeInterval) {
                /** @var $activeInterval DateInterval */
                $startActive = (new \DateTime)->setTimestamp(max($activeInterval->start->getTimestamp(),
                    $this->from->getTimestamp()));
                $endActive = (new \DateTime)->setTimestamp(min($activeInterval->end->getTimestamp(),
                    $this->to->getTimestamp()));
                $this->businessDayManipulator->setStartDate($startActive);
                $this->businessDayManipulator->setEndDate($endActive);
                $issueResult['time_spent_in_hours'] += $this->businessDayManipulator->getBusinessDays() *
                    static::WORKING_HOURS;
            }
            foreach ($impedimentIntervals as $impedimentInterval) {
                /** @var $impedimentInterval DateInterval */
                $startInactive = (new \DateTime)->setTimestamp(max($impedimentInterval->start->getTimestamp(),
                    $this->from->getTimestamp()));
                $endInactive = (new \DateTime)->setTimestamp(min($impedimentInterval->end->getTimestamp(),
                    $this->to->getTimestamp()));
                $this->businessDayManipulator->setStartDate($startInactive);
                $this->businessDayManipulator->setEndDate($endInactive);
                $issueResult['time_impeded_in_hours'] += $this->businessDayManipulator->getBusinessDays() *
                    static::WORKING_HOURS;
            }

            if ($issueResult['time_spent_in_hours']>0) {
                $result['issues'][] = $issueResult;
                $result['total_time_spent_in_hours'] += $issueResult['time_spent_in_hours'];
            }
        }

        return $result;
    }

}
