<?php
namespace App\Services\JiraSync\Features;

use App\Domains\Database\Jobs\BeginDatabaseTransactionJob;
use App\Domains\Database\Jobs\CommitDatabaseTransactionJob;
use App\Domains\Database\Jobs\RollbackDatabaseTransactionJob;
use App\Domains\Issue\Jobs\GetUpdatedIssuesByDateTimeIntervalJob;
use App\Domains\JiraClient\Jobs\GetConnectionJob;
use App\Domains\JiraClient\Jobs\PublishIssuesToSlaveJiraJob;
use App\Domains\Sync\Jobs\CreateSyncEventJob;
use App\Domains\Sync\Jobs\GetLatestSyncEventJob;
use App\Domains\Sync\Jobs\UpdateSyncEventJob;
use Lucid\Foundation\Feature;

class PublishIssuesToSlaveJiraInstanceFeature extends Feature
{
    /**
     * @throws \Exception
     */
    public function handle()
    {
        $latestSyncEvent = $this->run(GetLatestSyncEventJob::class);
        $syncEvent = $this->run(CreateSyncEventJob::class,[
            'fromDateTime'=>new \DateTime($latestSyncEvent->to_datetime),
            'toDateTime'=>now()
        ]);
//        $updatedIssues = $this->run(GetUpdatedIssuesByDateTimeIntervalJob::class,[
//            'fromDateTime'=>new \DateTime($syncEvent->from_datetime),
//            'toDateTime'=>new \DateTime($syncEvent->to_datetime)
//        ]);
        $updatedIssues = $this->run(GetUpdatedIssuesByDateTimeIntervalJob::class,[
            'fromDateTime'=>new \DateTime("2018-01-12 13:00:00"),
            'toDateTime'=>new \DateTime()
        ]);
        $slaveJiraApi = $this->run(GetConnectionJob::class, [
            'host'=>env('JIRA_SLAVE_HOST'),
            'user'=>env('JIRA_SLAVE_USERNAME'),
            'pass'=>env('JIRA_SLAVE_PASSWORD'),
        ]);
        $publishResult = $this->run(PublishIssuesToSlaveJiraJob::class,[
            'slaveJiraApi'=>$slaveJiraApi,
            'updatedIssues'=>$updatedIssues,
        ]);
        dd($publishResult);
        //$this->run(UpdateSyncEventJob::class,['SyncEvent'=>$syncEvent]);

        throw new \Exception("debug");
    }
}
