<?php
namespace App\Services\JiraWrapper\Features;

use App\Data\RestApis\Config;
use App\Data\RestApis\JiraAgile;
use App\Domains\Issue\Jobs\CreateOrUpdateIssuesJob;
use App\Domains\Issue\Jobs\UpdateIssuesRankJob;
use App\Domains\Jira\Jobs\GetCustomFieldIdJob;
use App\Domains\Jira\Jobs\SearchJiraBoardByNameJob;
use App\Domains\Jira\Jobs\SearchJiraBoardSprintsJob;
use App\Domains\Jira\Jobs\SearchJiraIssuesByJQLJob;
use App\Domains\Sprint\Jobs\CreateOrUpdateSprintsJob;
use App\Domains\Sprint\Jobs\SyncSprintsIssuesJob;
use Lucid\Foundation\Feature;

class CopyJiraIssuesToDatabaseFeature extends Feature
{
    /** TODO: move this to the database */
    const JIRA_ISSUES_QUERY = 'project IN (VVESTIOS,VVESTAND) AND resolution IS NULL';
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
        if (static::JIRA_ISSUES_BOARD_TYPE==JiraAgile::BOARD_TYPE_SCRUM) {

            $jiraIssuesWithSprintSortedByRankAsc = $this->run(SearchJiraIssuesByJQLJob::class,[
                'jiraInstance'=>Config::JIRA_MASTER_INSTANCE,
                'jiraQuery'=>static::JIRA_ISSUES_QUERY." AND sprint IS NOT EMPTY ORDER BY rank ASC",
            ]);
            $this->run(UpdateIssuesRankJob::class,[
                'jiraIssues'=>$jiraIssuesWithSprintSortedByRankAsc,
            ]);

            $jiraBoard = $this->run(SearchJiraBoardByNameJob::class,[
                'jiraInstance'=>Config::JIRA_MASTER_INSTANCE,
                'jiraBoardName'=>static::JIRA_ISSUES_BOARD_NAME,
            ]);

            if (!is_null($jiraBoard)) {
                $jiraSprints = $this->run(SearchJiraBoardSprintsJob::class,[
                    'jiraInstance' => Config::JIRA_MASTER_INSTANCE,
                    'jiraBoardId' => $jiraBoard->id,
                ]);
                $jiraSprintCustomFieldId = $this->run(GetCustomFieldIdJob::class,[
                    'jiraInstance'=>Config::JIRA_MASTER_INSTANCE,
                    'fieldName'=>JiraAgile::FIELD_NAME_SPRINT,
                ]);
                $sprintsCreatedOrUpdated = $this->run(CreateOrUpdateSprintsJob::class,[
                    'jiraSprints' => $jiraSprints,
                ]);
                $this->run(SyncSprintsIssuesJob::class,[
                    'jiraSprintCustomFieldId'=>$jiraSprintCustomFieldId,
                    'jiraIssues'=>$jiraIssues,
                ]);
            }
        }

        return [
            'createdIssues'=>count($issuesCreatedOrUpdated['created']),
            'updatedIssues'=>count($issuesCreatedOrUpdated['updated']),
            'createdSprints'=>count($sprintsCreatedOrUpdated['created']),
            'updatedSprints'=>count($sprintsCreatedOrUpdated['updated']),
        ];
    }
}
