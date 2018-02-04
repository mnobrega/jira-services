<?php
namespace App\Domains\Sprint\Jobs;

use App\Data\Issue;;

use App\Data\Sprint;
use Lucid\Foundation\Job;

class GetIssueUnclosedSprintJob extends Job
{
    private $issue;

    public function __construct(Issue $issue)
    {
        $this->issue = $issue;
    }

    /**
     * @return Sprint|mixed|null
     */
    public function handle()
    {
        foreach ($this->issue->sprints as $sprint) {
            if ($sprint->state==Sprint::STATE_ACTIVE || $sprint->state==Sprint::STATE_FUTURE) {
                return $sprint;
            }
        }
        return null;
    }
}
