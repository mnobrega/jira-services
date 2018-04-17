<?php
namespace App\Services\JiraWrapper\Features;

use App\Domains\Issue\Jobs\GetIssuesDateIntervalTimeSpentJob;
use App\Domains\Issue\Jobs\GetIssuesActiveByDateIntervalJob;
use Illuminate\Support\Carbon;
use Lucid\Foundation\Feature;

class GetActivityReportFeature extends Feature
{
    public function handle()
    {
        $now = Carbon::now();
        $previousMonth = new \DateTime($now->format("Y-m-01 00:00:00"));
        $previousMonth->modify("-1 month");

        $issues = $this->run(GetIssuesActiveByDateIntervalJob::class,[
            'from'=>$previousMonth,
            'to'=>new \DateTime($previousMonth->format("Y-m-t 23:59:59")),
        ]);

        return $this->run(GetIssuesDateIntervalTimeSpentJob::class,[
            'issues'=>$issues,
            'from'=>$previousMonth,
            'to'=>new \DateTime($previousMonth->format("Y-m-t 23:59:59")),
        ]);
    }
}
