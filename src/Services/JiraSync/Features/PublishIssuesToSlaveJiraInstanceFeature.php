<?php
namespace App\Services\JiraSync\Features;

use App\Data\RestApis\Config;
use App\Data\RestApis\JiraAgile;
use App\Domains\Issue\Jobs\CreateSlaveJiraIssueJob;
use App\Domains\Issue\Jobs\GetAllSlaveJiraIssuesJob;
use App\Domains\Issue\Jobs\GetUpdatedIssuesByDateTimeIntervalJob;
use App\Domains\Issue\Jobs\SearchSlaveJiraIssueByMasterJiraIssueJob;
use App\Domains\Jira\Jobs\PublishIssueToJiraJob;
use App\Domains\Jira\Jobs\PublishSprintToJiraJob;
use App\Domains\Jira\Jobs\SearchJiraBoardByNameJob;
use App\Domains\Sprint\Jobs\GetAllSprintsJob;
use App\Domains\Sprint\Jobs\SearchSlaveJiraSprintByMasterJiraSprintJob;
use App\Domains\Sync\Jobs\CreateSyncEventJob;
use App\Domains\Sync\Jobs\GetLatestSyncEventJob;
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

            $sprints = $this->run(GetAllSprintsJob::class);

            $jiraBoard = $this->run(SearchJiraBoardByNameJob::class,[
                'jiraInstance'=>Config::JIRA_SLAVE_INSTANCE,
                'jiraBoardName'=>static::JIRA_ISSUES_BOARD_NAME,
            ]);

            if (!is_null($jiraBoard)) {
                foreach ($sprints as $sprint) {
                    dd($sprint);
                    $slaveJiraSprint = $this->run(SearchSlaveJiraSprintByMasterJiraSprintJob::class,[
                        'masterJiraSprint'=>$sprint
                    ]);
                    $jiraSprint =  $this->run(PublishSprintToJiraJob::class,[
                        'jiraInstance'=>Config::JIRA_SLAVE_INSTANCE,
                        'boardId'=>$jiraBoard->id,
                        'sprint'=>$sprint,
                        'remoteSprintId'=>is_null($slaveJiraSprint)?null:$slaveJiraSprint->slave_jira_id,
                    ]);
                }

                //TODO - foreach sprint

                //TODO - local - search slave jira sprint

                //TODO - remote - publish sprint with issues to Slave JIRA (create or update)

                //TODO - local - create slave jira sprints in local database for mapping

                $slaveJiraIssues = $this->run(GetAllSlaveJiraIssuesJob::class);
                //TODO - foreach issue in slave jira issues
                // TODO - get master issue
                // TODO - foreach master issue sprint
                //TODO - get slave jira sprint
                //TODO - associate slave issue to slave jira
//            foreach ($slaveJiraIssues as $slaveJiraIssue) {
//                $issue = $this->run(GetIssueByKeyJob::class,[
//                    'issueKey'=>$slaveJiraIssue->master_issue_key,
//                ]);
//                foreach ($issue->sprints as $sprint) {
//
//                }
//            }
            }

        }

        return $publishResult;
    }
}
