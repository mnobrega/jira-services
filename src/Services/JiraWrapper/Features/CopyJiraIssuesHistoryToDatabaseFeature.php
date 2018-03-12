<?php
namespace App\Services\JiraWrapper\Features;

use App\Data\RestApis\Config;
use App\Domains\Issue\Jobs\GetUpdatedIssuesByDateTimeIntervalJob;
use App\Domains\Jira\Jobs\GetIssueHistoriesForDateIntervalJob;
use App\Domains\Sync\Jobs\CreateWrapperSyncEventJob;
use App\Domains\Sync\Jobs\GetLatestWrapperSyncEventJob;
use Lucid\Foundation\Feature;

class CopyJiraIssuesHistoryToDatabaseFeature extends Feature
{
    public function handle()
    {
        $latestSyncEvent = $this->run(GetLatestWrapperSyncEventJob::class);
        $syncEvent = $this->run(CreateWrapperSyncEventJob::class,[
            'fromDateTime'=>new \DateTime($latestSyncEvent->to_datetime),
            'toDateTime'=>now()
        ]);

        $updatedIssues = $this->run(GetUpdatedIssuesByDateTimeIntervalJob::class,[
            'fromDateTime'=>new \DateTime($syncEvent->from_datetime),
            'toDateTime'=>new \DateTime($syncEvent->to_datetime),
        ]);

        foreach ($updatedIssues as $updatedIssue) {
            $issueHistoriesForDateInterval = $this->run(GetIssueHistoriesForDateIntervalJob::class,[
                'jiraInstance'=>Config::JIRA_MASTER_INSTANCE,
                'issueIdOrKey'=>$updatedIssue->key,
                'fromDateTime'=>new \DateTime($syncEvent->from_datetime),
                'toDateTime'=>new \DateTime($syncEvent->to_datetime),
            ]);
            dd($issueHistoriesForDateInterval);
        }
        //get local updated JIRA Issues
        //foreach
            //get history from JIRA
            //store it in database
    }
}
