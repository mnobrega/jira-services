<?php
namespace App\Services\JiraWrapper\Features;

use App\Data\RestApis\Config;
use App\Domains\Issue\Jobs\CreateOrUpdateIssuesJob;
use App\Domains\Issue\Jobs\UpdateIssuesRankJob;
use App\Domains\Jira\Jobs\GetCustomFieldIdJob;
use App\Domains\Jira\Jobs\SearchJiraBoardSprintsJob;
use App\Domains\Jira\Jobs\SearchJiraIssuesByJQLJob;
use App\Domains\Sprint\Jobs\CreateOrUpdateSprintsJob;
use App\Domains\Sprint\Jobs\SyncSprintsIssuesJob;
use Lucid\Foundation\Feature;

class CopyJiraIssuesToDatabaseFeature extends Feature
{
    const BOARD_TYPE_SCRUM = 'scrum';
    const BOARD_TYPE_KANBAN = 'kanban';

    const FIELD_NAME_SPRINT = 'Sprint';

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
        $issuesCreatedOrUpdated = $this->run(CreateOrUpdateIssuesJob::class,[
            'jiraIssues'=>$jiraIssues,
        ]);

        $sprintsCreatedOrUpdated = [
            'created'=>array(),
            'updated'=>array(),
        ];
        if (static::JIRA_ISSUES_BOARD_TYPE==static::BOARD_TYPE_SCRUM) {

            $jiraIssuesWithSprintSortedByRankAsc = $this->run(SearchJiraIssuesByJQLJob::class,[
                'jiraInstance'=>Config::JIRA_MASTER_INSTANCE,
                'jiraQuery'=>static::JIRA_ISSUES_QUERY." AND sprint IS NOT EMPTY ORDER BY rank ASC",
            ]);
            $this->run(UpdateIssuesRankJob::class,[
                'jiraIssues'=>$jiraIssuesWithSprintSortedByRankAsc,
            ]);

            $jiraSprints = $this->run(SearchJiraBoardSprintsJob::class,[
                'jiraInstance' => Config::JIRA_MASTER_INSTANCE,
                'jiraBoardName' => static::JIRA_ISSUES_BOARD_NAME,
            ]);
            $jiraSprintCustomFieldId = $this->run(GetCustomFieldIdJob::class,[
                'jiraInstance'=>Config::JIRA_MASTER_INSTANCE,
                'fieldName'=>static::FIELD_NAME_SPRINT,
            ]);
            $sprintsCreatedOrUpdated = $this->run(CreateOrUpdateSprintsJob::class,[
                'jiraSprints' => $jiraSprints,
            ]);
            $this->run(SyncSprintsIssuesJob::class,[
                'jiraSprintCustomFieldId'=>$jiraSprintCustomFieldId,
                'jiraIssues'=>$jiraIssues,
            ]);
        }

        return [
            'createdIssues'=>count($issuesCreatedOrUpdated['created']),
            'updatedIssues'=>count($issuesCreatedOrUpdated['updated']),
            'createdSprints'=>count($sprintsCreatedOrUpdated['created']),
            'updatedSprints'=>count($sprintsCreatedOrUpdated['updated']),
        ];
    }
}
