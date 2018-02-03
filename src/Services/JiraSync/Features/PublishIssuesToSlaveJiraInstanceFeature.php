<?php
namespace App\Services\JiraSync\Features;

use App\Data\RestApis\Config;
use App\Data\RestApis\JiraAgile;
use App\Domains\Issue\Jobs\CreateSlaveJiraIssueJob;
use App\Domains\Issue\Jobs\GetAllSlaveJiraIssuesJob;
use App\Domains\Issue\Jobs\GetIssueByKeyJob;
use App\Domains\Issue\Jobs\GetUpdatedIssuesByDateTimeIntervalJob;
use App\Domains\Issue\Jobs\SearchSlaveJiraIssueByMasterJiraIssueJob;
use App\Domains\Jira\Jobs\PublishIssuesToSlaveJiraJob;
use App\Domains\Jira\Jobs\PublishIssueToJiraJob;
use App\Domains\Jira\Jobs\SearchJiraIssuesByJQLJob;
use App\Domains\Sync\Jobs\CreateSyncEventJob;
use App\Domains\Sync\Jobs\GetLatestSyncEventJob;
use App\Services\JiraWrapper\Features\CopyJiraIssuesToDatabaseFeature;
use Lucid\Foundation\Feature;

class PublishIssuesToSlaveJiraInstanceFeature extends Feature
{
    /** TODO: move this to the database */
    const JIRA_ISSUES_BOARD_NAME = 'VVESTIOS';
    const JIRA_ISSUES_BOARD_TYPE = 'scrum';

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
            'publishedIssues'=>0,
        ];
        foreach ($updatedIssues as $issue) {
            /** @var $issue \App\Data\Issue */
            $slaveJiraIssue = $this->run(SearchSlaveJiraIssueByMasterJiraIssueJob::class,[
                'masterJiraIssue'=>$issue,
            ]);
            $jiraIssue = $this->run(PublishIssueToJiraJob::class,[
                'jiraInstance'=>Config::JIRA_SLAVE_INSTANCE,
                'issue'=>$issue,
                'remoteIssueKey'=>is_null($slaveJiraIssue)?null:$slaveJiraIssue->slave_issue_key,
            ]);
            $this->run(CreateSlaveJiraIssueJob::class,[
                'masterJiraIssue'=>$issue,
                'slaveJiraIssue'=>$jiraIssue,
            ]);
            $publishResult['publishedIssues']++;
        }

        if (static::JIRA_ISSUES_BOARD_TYPE==JiraAgile::BOARD_TYPE_SCRUM) {

            //TODO - publish sprints with issues to Slave JIRA (create or update)

            //TODO - create slave jira sprints in local database for mapping

            //TODO - foreach issue in slave jira issues, get master issue and sprints and associate
//            $slaveJiraIssues = $this->run(GetAllSlaveJiraIssuesJob::class);
//            foreach ($slaveJiraIssues as $slaveJiraIssue) {
//                $issue = $this->run(GetIssueByKeyJob::class,[
//                    'issueKey'=>$slaveJiraIssue->master_issue_key,
//                ]);
//                foreach ($issue->sprints as $sprint) {
//
//                }
//            }
        }

        return $publishResult;
    }
}
