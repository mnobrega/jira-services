<?php
namespace App\Services\JiraWrapper\Features;

use App\Data\RestApis\Config;
use App\Data\RestApis\JiraAgile;
use App\Domains\Issue\Jobs\CreateOrUpdateIssueLinksJob;
use App\Domains\Issue\Jobs\CreateOrUpdateIssuesJob;
use App\Domains\Issue\Jobs\UpdateIssuesRankJob;
use App\Domains\Jira\Jobs\GetCustomFieldIdJob;
use App\Domains\Jira\Jobs\GetFieldsJob;
use App\Domains\Jira\Jobs\GetIssueLinksJob;
use App\Domains\Jira\Jobs\GetJiraConfigJob;
use App\Domains\Jira\Jobs\SearchJiraBoardByNameJob;
use App\Domains\Jira\Jobs\SearchJiraBoardSprintsJob;
use App\Domains\Jira\Jobs\SearchJiraIssuesByJQLJob;
use App\Domains\Sprint\Jobs\CreateOrUpdateSprintsJob;
use App\Domains\Sprint\Jobs\SyncSprintsIssuesJob;
use Lucid\Foundation\Feature;

class CopyJiraIssuesToDatabaseFeature extends Feature
{
    public function handle()
    {
        $jiraConfig = $this->run(GetJiraConfigJob::class);

        $jiraIssues = $this->run(SearchJiraIssuesByJQLJob::class, [
            'jiraInstance'=>Config::JIRA_MASTER_INSTANCE,
            'jiraQuery'=>$jiraConfig->jira_issues_query." ORDER BY created ASC",
        ]);
        $jiraFields = $this->run(GetFieldsJob::class,[
            'jiraInstance'=>Config::JIRA_MASTER_INSTANCE,
        ]);
        $issuesCreatedOrUpdated = $this->run(CreateOrUpdateIssuesJob::class,[
            'jiraIssues'=>$jiraIssues,
            'jiraFields'=>$jiraFields,
        ]);

        foreach ($jiraIssues as $jiraIssue) {
            $this->run(CreateOrUpdateIssueLinksJob::class,[
                'jiraIssue'=>$jiraIssue,
            ]);
        }

        $sprintsCreatedOrUpdated = [
            'created'=>array(),
            'updated'=>array(),
        ];
        if ($jiraConfig->jira_board_type==JiraAgile::BOARD_TYPE_SCRUM) {

            $jiraIssuesWithSprintSortedByRankAsc = $this->run(SearchJiraIssuesByJQLJob::class,[
                'jiraInstance'=>Config::JIRA_MASTER_INSTANCE,
                'jiraQuery'=>$jiraConfig->jira_issues_query." AND sprint IS NOT EMPTY ORDER BY rank ASC",
            ]);
            $this->run(UpdateIssuesRankJob::class,[
                'jiraIssues'=>$jiraIssuesWithSprintSortedByRankAsc,
            ]);

            $jiraBoard = $this->run(SearchJiraBoardByNameJob::class,[
                'jiraInstance'=>Config::JIRA_MASTER_INSTANCE,
                'jiraBoardName'=>$jiraConfig->jira_board_name,
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
