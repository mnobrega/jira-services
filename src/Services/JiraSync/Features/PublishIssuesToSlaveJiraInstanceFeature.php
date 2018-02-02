<?php
namespace App\Services\JiraSync\Features;

use App\Data\RestApis\Config;
use App\Domains\Issue\Jobs\CreateSlaveJiraIssueJob;
use App\Domains\Issue\Jobs\GetUpdatedIssuesByDateTimeIntervalJob;
use App\Domains\Issue\Jobs\SearchSlaveJiraIssueByMasterJiraIssueJob;
use App\Domains\Jira\Jobs\PublishIssuesToSlaveJiraJob;
use App\Domains\Jira\Jobs\PublishIssueToJiraJob;
use App\Domains\Sync\Jobs\CreateSyncEventJob;
use App\Domains\Sync\Jobs\GetLatestSyncEventJob;
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
        $updatedIssues = $this->run(GetUpdatedIssuesByDateTimeIntervalJob::class,[
            'fromDateTime'=>new \DateTime($syncEvent->from_datetime),
            'toDateTime'=>new \DateTime($syncEvent->to_datetime)
        ]);

        $publishResult = [
            'createdSlaveJiraIssues'=>0,
            'updatedSlaveJiraIssues'=>0,
        ];
        foreach ($updatedIssues as $issue) {
            $slaveJiraIssue = $this->run(SearchSlaveJiraIssueByMasterJiraIssueJob::class,[
                'masterJiraIssue'=>$issue,
            ]);
            $slaveJiraIssue = $this->run(PublishIssueToJiraJob::class,[
                'jiraInstance'=>Config::JIRA_SLAVE_INSTANCE,
                'issue'=>$issue,
                'slaveJiraIssue'=>$slaveJiraIssue,
            ]);
            $this->run(CreateSlaveJiraIssueJob::class,[
                'issue'=>$issue,
                'slaveJiraIssue'=>$slaveJiraIssue,
            ]);
        }
//        $publishResult = $this->run(PublishIssuesToSlaveJiraJob::class,[
//            'slaveJiraInstance'=>Config::JIRA_SLAVE_INSTANCE,
//            'updatedIssues'=>$updatedIssues
//        ]);

        return $publishResult;
    }
}
