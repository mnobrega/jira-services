<?php
namespace App\Services\JiraWrapper\Features;

use App\Data\RestApis\Config;
use App\Domains\Issue\Jobs\CreateOrUpdateIssueHistoriesJob;
use App\Domains\Issue\Jobs\GetJiraUpdatedIssuesByDateTimeIntervalJob;
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

        $updatedIssues = $this->run(GetJiraUpdatedIssuesByDateTimeIntervalJob::class,[
            'fromDateTime'=>new \DateTime($syncEvent->from_datetime),
            'toDateTime'=>new \DateTime($syncEvent->to_datetime),
        ]);

        $issuesHistories = [
            'created'=>array(),
            'updated'=>array(),
        ];
        foreach ($updatedIssues as $updatedIssue) {
            $issueHistoriesForDateInterval = $this->run(GetIssueHistoriesForDateIntervalJob::class,[
                'jiraInstance'=>Config::JIRA_MASTER_INSTANCE,
                'issueIdOrKey'=>$updatedIssue->issue_key,
                'fromDateTime'=>new \DateTime($syncEvent->from_datetime),
                'toDateTime'=>new \DateTime($syncEvent->to_datetime),
            ]);
            $jobResult = $this->run(CreateOrUpdateIssueHistoriesJob::class,[
                'issue'=>$updatedIssue,
                'issueHistories'=>$issueHistoriesForDateInterval
            ]);

            $issuesHistories['created'] = array_merge($issuesHistories['created'],$jobResult['created']);
            $issuesHistories['updated'] = array_merge($issuesHistories['updated'],$jobResult['updated']);
        }

        return $issuesHistories;
    }
}
