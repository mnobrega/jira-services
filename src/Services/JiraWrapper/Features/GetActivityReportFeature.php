<?php
namespace App\Services\JiraWrapper\Features;

use App\Domains\Issue\Jobs\GetIssuesDateIntervalTimeSpentJob;
use App\Domains\Issue\Jobs\GetJiraUpdatedIssuesByDateTimeIntervalJob;
use App\Domains\Issue\Jobs\GetUpdatedIssuesByDateTimeIntervalJob;
use Illuminate\Support\Carbon;
use Lucid\Foundation\Feature;

class GetActivityReportFeature extends Feature
{
    public function handle()
    {
        $now = Carbon::now();
        $twoMonthsAgo = new \DateTime($now->format("Y-m-01 00:00:00"));
        $twoMonthsAgo->modify("-2 month");
        $issues = $this->run(GetJiraUpdatedIssuesByDateTimeIntervalJob::class,[
            'fromDateTime'=> $twoMonthsAgo,
            'toDateTime'=> $now,
        ]);

        $previousMonth = new \DateTime($now->format("Y-m-01 00:00:00"));
        $previousMonth->modify("-1 month");
        return $this->run(GetIssuesDateIntervalTimeSpentJob::class,[
            'issues'=>$issues,
            'from'=>$previousMonth,
            'to'=>new \DateTime($previousMonth->format("Y-m-t 23:59:59")),
        ]);
    }
}
