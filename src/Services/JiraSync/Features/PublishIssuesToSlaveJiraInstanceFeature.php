<?php
namespace App\Services\JiraSync\Features;

use App\Data\Issue;
use App\Data\RestApis\Config;
use App\Data\RestApis\JiraAgile;
use App\Domains\Issue\Jobs\CreateSlaveJiraIssueJob;
use App\Domains\Issue\Jobs\GetAllEpicIssuesJob;
use App\Domains\Issue\Jobs\GetAllIssueVersionIdsJob;
use App\Domains\Issue\Jobs\GetAllSlaveJiraIssuesJob;
use App\Domains\Issue\Jobs\GetIssueByKeyJob;
use App\Domains\Issue\Jobs\GetIssueDistinctVersionsJob;
use App\Domains\Issue\Jobs\GetIssuesSortedByRankJob;
use App\Domains\Issue\Jobs\GetSlaveJiraIssuesByIssuesJob;
use App\Domains\Issue\Jobs\GetUpdatedIssuesByDateTimeIntervalJob;
use App\Domains\Issue\Jobs\SearchSlaveJiraIssueByMasterJiraIssueJob;
use App\Domains\Jira\Jobs\GetJiraVersionJob;
use App\Domains\Jira\Jobs\GetProjectJob;
use App\Domains\Jira\Jobs\PublishIssueForBacklogToJiraJob;
use App\Domains\Jira\Jobs\PublishIssueForSprintToJiraJob;
use App\Domains\Jira\Jobs\PublishIssueRankJob;
use App\Domains\Jira\Jobs\PublishIssueToJiraJob;
use App\Domains\Jira\Jobs\PublishSprintToJiraJob;
use App\Domains\Jira\Jobs\PublishVersionToJiraJob;
use App\Domains\Jira\Jobs\SearchJiraBoardByNameJob;
use App\Domains\Sprint\Jobs\CreateSlaveJiraSprintJob;
use App\Domains\Sprint\Jobs\GetAllSprintsJob;
use App\Domains\Sprint\Jobs\GetIssueUnclosedSprintJob;
use App\Domains\Sprint\Jobs\SearchSlaveJiraSprintByMasterJiraSprintJob;
use App\Domains\Sync\Jobs\CreateSyncEventJob;
use App\Domains\Sync\Jobs\GetLatestSyncEventJob;
use App\Domains\Version\Jobs\CreateSlaveJiraVersionJob;
use App\Domains\Version\Jobs\SearchSlaveJiraVersionByMasterJiraVersionIdJob;
use Lucid\Foundation\Feature;

class PublishIssuesToSlaveJiraInstanceFeature extends Feature
{
    /** TODO: move this to the database */
    const JIRA_ISSUES_BOARD_NAME = 'ViaVerde';
    const JIRA_ISSUES_BOARD_TYPE = 'scrum';

    /**
     * @throws \Exception
     */
    public function handle()
    {
        $publishResult = [
            'publishedIssues'=>0,
            'publishedIssueRanks'=>0,
            'publishedSprints'=>0,
            'publishedVersions'=>0,
            'issuesMovedToSprint'=>0,
            'issuesMovedToBacklog'=>0,
        ];

        $issueVersions = $this->run(GetIssueDistinctVersionsJob::class);
        $publishResult = $this->publishVersions($issueVersions, $publishResult);

        $epicIssues = $this->run(GetAllEpicIssuesJob::class);
        $publishResult = $this->publishIssues($epicIssues,$publishResult);

        $latestSyncEvent = $this->run(GetLatestSyncEventJob::class);
        $syncEvent = $this->run(CreateSyncEventJob::class,[
            'fromDateTime'=>new \DateTime($latestSyncEvent->to_datetime),
            'toDateTime'=>now()
        ]);
        $updatedIssues = $this->run(GetUpdatedIssuesByDateTimeIntervalJob::class,[
            'fromDateTime'=>new \DateTime($syncEvent->from_datetime),
            'toDateTime'=>new \DateTime($syncEvent->to_datetime)
        ]);
        $publishResult = $this->publishIssues($updatedIssues,$publishResult);

        if (static::JIRA_ISSUES_BOARD_TYPE==JiraAgile::BOARD_TYPE_SCRUM) {
            $sprints = $this->run(GetAllSprintsJob::class);
            $jiraBoard = $this->run(SearchJiraBoardByNameJob::class,[
                'jiraInstance'=>Config::JIRA_SLAVE_INSTANCE,
                'jiraBoardName'=>static::JIRA_ISSUES_BOARD_NAME,
            ]);

            if (!is_null($jiraBoard)) {
                $publishResult = $this->publishSprints($sprints, $jiraBoard->id, $publishResult);
                $slaveJiraIssues = $this->run(GetAllSlaveJiraIssuesJob::class);
                $publishResult = $this->publishSlaveJiraIssuesForSprintOrBacklog($slaveJiraIssues, $publishResult);
            }
        }

        $publishResult = $this->publishIssuesRank($publishResult);

        return $publishResult;
    }

    /**
     * @param Issue[] $issueVersions
     * @param $publishResult
     * @return mixed
     */
    private function publishVersions($issueVersions, $publishResult)
    {
        foreach ($issueVersions as $issueVersion) {
            $masterJiraVersion = $this->run(GetJiraVersionJob::class,[
                'jiraInstance'=>Config::JIRA_MASTER_INSTANCE,
                'versionId'=>$issueVersion->fix_version_id,
            ]);
            $slaveJiraVersion = $this->run(SearchSlaveJiraVersionByMasterJiraVersionIdJob::class,[
                'masterVersionId'=>$masterJiraVersion->id
            ]);
            $slaveJiraProject = $this->run(GetProjectJob::class,[
                'jiraInstance'=>Config::JIRA_SLAVE_INSTANCE,
                'projectKey'=>$issueVersion->project_key,
            ]);
            $jiraVersion = $this->run(PublishVersionToJiraJob::class,[
                'jiraInstance'=>Config::JIRA_SLAVE_INSTANCE,
                'version'=>$masterJiraVersion,
                'remoteVersionId'=>is_null($slaveJiraVersion)?null:$slaveJiraVersion->slave_version_id,
                'remoteProjectId'=>$slaveJiraProject->id,
            ]);
            if (is_null($slaveJiraVersion)) {
                $this->run(CreateSlaveJiraVersionJob::class,[
                    'masterVersionId'=>$masterJiraVersion->id,
                    'slaveVersionId'=>$jiraVersion->id,
                ]);
            }
            $publishResult['publishedVersions']++;
        }
        return $publishResult;
    }

    private function publishIssues($issues, $publishResult)
    {
        foreach ($issues as $issue) {
            $slaveJiraEpicIssue = null;
            /** @var $issue \App\Data\Issue */
            $slaveJiraIssue = $this->run(SearchSlaveJiraIssueByMasterJiraIssueJob::class,[
                'masterJiraIssue'=>$issue,
            ]);
            $slaveJiraVersion = $this->run(SearchSlaveJiraVersionByMasterJiraVersionIdJob::class,[
                'masterVersionId'=>$issue->fix_version_id,
            ]);
            if (!is_null($issue->epic_link)) {
                $epicIssue = $this->run(GetIssueByKeyJob::class,[
                    'issueKey'=>$issue->epic_link,
                ]);
                $slaveJiraEpicIssue = $this->run(SearchSlaveJiraIssueByMasterJiraIssueJob::class,[
                    'masterJiraIssue'=>$epicIssue,
                ]);
            }
            $jiraIssue = $this->run(PublishIssueToJiraJob::class,[
                'jiraInstance'=>Config::JIRA_SLAVE_INSTANCE,
                'issue'=>$issue,
                'remoteIssueKey'=>is_null($slaveJiraIssue)?null:$slaveJiraIssue->slave_issue_key,
                'remoteEpicIssueKey'=>is_null($slaveJiraEpicIssue)?null:$slaveJiraEpicIssue->slave_issue_key,
                'remoteVersionId'=>is_null($slaveJiraVersion)?null:$slaveJiraVersion->slave_version_id,
            ]);
            if (is_null($slaveJiraIssue)) {
                $this->run(CreateSlaveJiraIssueJob::class,[
                    'masterJiraIssue'=>$issue,
                    'slaveJiraIssue'=>$jiraIssue,
                ]);
            }
            $publishResult['publishedIssues']++;
        }
        return $publishResult;
    }

    private function publishSprints($sprints, $jiraBoardId, $publishResult)
    {
        foreach ($sprints as $sprint) {
            if (count($sprint->issues)) {
                $slaveJiraSprint = $this->run(SearchSlaveJiraSprintByMasterJiraSprintJob::class,[
                    'masterJiraSprint'=>$sprint
                ]);
                $jiraSprint =  $this->run(PublishSprintToJiraJob::class,[
                    'jiraInstance'=>Config::JIRA_SLAVE_INSTANCE,
                    'boardId'=>$jiraBoardId,
                    'sprint'=>$sprint,
                    'remoteSprintId'=>is_null($slaveJiraSprint)?null:$slaveJiraSprint->slave_sprint_id,
                ]);
                if (is_null($slaveJiraSprint)) {
                    $this->run(CreateSlaveJiraSprintJob::class,[
                        'masterJiraSprint'=>$sprint,
                        'slaveJiraSprint'=>$jiraSprint,
                    ]);
                }
                $publishResult['publishedSprints']++;
            }
        }
        return $publishResult;
    }

    private function publishSlaveJiraIssuesForSprintOrBacklog($slaveJiraIssues, $publishResult)
    {
        foreach ($slaveJiraIssues as $slaveJiraIssue) {
            $issue = $this->run(GetIssueByKeyJob::class,[
                'issueKey'=>$slaveJiraIssue->master_issue_key,
            ]);
            $sprint = $this->run(GetIssueUnclosedSprintJob::class,[
                'issue'=>$issue,
            ]);
            if (!is_null($sprint)) {
                $slaveJiraSprint = $this->run(SearchSlaveJiraSprintByMasterJiraSprintJob::class,[
                    'masterJiraSprint'=>$sprint
                ]);
                $this->run(PublishIssueForSprintToJiraJob::class,[
                    'jiraInstance'=>Config::JIRA_SLAVE_INSTANCE,
                    'slaveJiraIssue'=>$slaveJiraIssue,
                    'slaveJiraSprint'=>$slaveJiraSprint,
                ]);
                $publishResult['issuesMovedToSprint']++;
            } else {
                $this->run(PublishIssueForBacklogToJiraJob::class,[
                    'jiraInstance'=>Config::JIRA_SLAVE_INSTANCE,
                    'slaveJiraIssue'=>$slaveJiraIssue,
                ]);
                $publishResult['issuesMovedToBacklog']++;
            }
        }
        return $publishResult;
    }

    private function publishIssuesRank($publishResult)
    {
        $issuesSortedByRank = $this->run(GetIssuesSortedByRankJob::class,[
            'sortOrder'=>'desc',
        ]);
        $slaveJiraIssuesSortedByRank = $this->run(GetSlaveJiraIssuesByIssuesJob::class,[
            'issues'=>$issuesSortedByRank,
        ]);
        foreach ($slaveJiraIssuesSortedByRank as $arrayIndex=>$slaveJiraIssue) {
            if ($arrayIndex > 0) {
                $this->run(PublishIssueRankJob::class,[
                    'jiraInstance'=>Config::JIRA_SLAVE_INSTANCE,
                    'slaveJiraIssue'=>$slaveJiraIssue,
                    'rankBeforeSlaveJiraIssue'=>$slaveJiraIssuesSortedByRank[$arrayIndex-1],
                ]);
                $publishResult['publishedIssueRanks']++;
            }
        }
        return $publishResult;
    }
}
