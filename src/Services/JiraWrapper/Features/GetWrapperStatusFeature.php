<?php
namespace App\Services\JiraWrapper\Features;

use App\Domains\Issue\Jobs\GetIssuesCountJob;
use App\Domains\Issue\Jobs\GetLatestUpdatedIssueJob;
use Lucid\Foundation\Feature;
use Illuminate\Http\Request;

class GetWrapperStatusFeature extends Feature
{
    public function handle()
    {
        $featureResponse = [
          'issuesCount'=>$this->run(GetIssuesCountJob::class),
          'latestUpdatedIssue'=>$this->run(GetLatestUpdatedIssueJob::class),
        ];
        return $featureResponse;
    }
}
