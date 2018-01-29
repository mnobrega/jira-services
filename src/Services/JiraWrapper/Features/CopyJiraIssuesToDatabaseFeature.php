<?php
namespace App\Services\JiraWrapper\Features;

use App\Data\RestApis\Config;
use App\Domains\Issue\Jobs\CreateOrUpdateIssuesJob;
use App\Domains\Issue\Jobs\UpdateIssuesRankJob;
use App\Domains\Jira\Jobs\GetJiraBoardSprintsJob;
use App\Domains\Jira\Jobs\GetJiraSprintsFromIssuesJob;
use App\Domains\Jira\Jobs\SearchJiraIssuesByJQLJob;
use App\Domains\Sprint\Jobs\CreateOrUpdateSprintsIssuesJob;
use App\Domains\Sprint\Jobs\CreateOrUpdateSprintsJob;
use App\Domains\Sprint\Tests\Jobs\SyncSprintsIssuesJobTest;
use Lucid\Foundation\Feature;

class CopyJiraIssuesToDatabaseFeature extends Feature
{
    const BOARD_TYPE_SCRUM = 'scrum';
    const BOARD_TYPE_KANBAN = 'kanban';

    /** TODO: move this to the database */
    const JIRA_ISSUES_QUERY = 'project IN (VVESTIOS) AND resolution IS NULL';
    const JIRA_ISSUES_BOARD_NAME = 'Mobile';
    const JIRA_ISSUES_BOARD_TYPE = 'scrum';

    public function handle()
    {
        $jiraIssues = $this->run(SearchJiraIssuesByJQLJob::class, [
            'jiraInstance'=>Config::JIRA_MASTER_INSTANCE,
            'jiraQuery'=>static::JIRA_ISSUES_QUERY." ORDER BY created ASC",
        ]);

        $issues = $this->run(CreateOrUpdateIssuesJob::class,[
            'jiraIssues'=>$jiraIssues,
        ]);

        if (static::JIRA_ISSUES_BOARD_TYPE==static::BOARD_TYPE_SCRUM) {

            $jiraSprints = $this->run(GetJiraBoardSprintsJob::class,[
                'jiraInstance' => Config::JIRA_MASTER_INSTANCE,
                'jiraBoardName' => static::JIRA_ISSUES_BOARD_NAME,
            ]);

            $sprints = $this->run(CreateOrUpdateSprintsJob::class,[
                'sprints' => $jiraSprints
            ]);
//
//            $this->run(SyncSprintsIssuesJobTest::any(),[
//                'sprints' => $sprints,
//                'issues' => $issues,
//            ]);

            $jiraIssuesSortedByRankAsc = $this->run(SearchJiraIssuesByJQLJob::class,[
                'jiraInstance'=>Config::JIRA_MASTER_INSTANCE,
                'jiraQuery'=>static::JIRA_ISSUES_QUERY." AND sprint IS NOT EMPTY ORDER BY rank ASC",
            ]);

            $this->run(UpdateIssuesRankJob::class,[
                'jiraIssues'=>$jiraIssuesSortedByRankAsc
            ]);
        }

        return [
            'createdIssues'=>count($issues['created']),
            'updatedIssues'=>count($issues['updated'])
        ];
    }
}
