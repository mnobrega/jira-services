<?php
namespace App\Services\JiraWrapper\Features;

use App\Data\RestApis\CredentialsFactory;
use App\Domains\Issue\Jobs\CreateOrUpdateIssuesJob;
use App\Domains\Issue\Jobs\UpdateIssuesRankJob;
use App\Domains\Jira\Jobs\GetJiraSprintsFromIssuesJob;
use App\Domains\Jira\Jobs\SearchJiraIssuesByJQLJob;
use App\Domains\Sprint\Jobs\CreateOrUpdateSprintsIssuesJob;
use Lucid\Foundation\Feature;

class CopyJiraIssuesToDatabaseFeature extends Feature
{
    /** TODO: move this to the database */
    const JIRA_ISSUES_QUERY = 'project IN (VVESTIOS) AND resolution IS NULL';

    public function handle()
    {
        $jiraIssues = $this->run(SearchJiraIssuesByJQLJob::class, [
            'jiraInstance'=>CredentialsFactory::JIRA_MASTER_INSTANCE,
            'jiraQuery'=>static::JIRA_ISSUES_QUERY." ORDER BY created ASC",
        ]);

        $featureResult = $this->run(CreateOrUpdateIssuesJob::class,[
            'jiraIssues'=>$jiraIssues,
        ]);

        if (env('JIRA_AGILE_ENABLED')) {
            $jiraIssuesSortedByRankAsc = $this->run(SearchJiraIssuesByJQLJob::class,[
                'jiraInstance'=>CredentialsFactory::JIRA_MASTER_INSTANCE,
                'jiraQuery'=>static::JIRA_ISSUES_QUERY." AND sprint IS NOT EMPTY ORDER BY rank ASC",
            ]);

            $this->run(UpdateIssuesRankJob::class,[
                'jiraIssues'=>$jiraIssuesSortedByRankAsc
            ]);

            $sprintsIssues = $this->run(GetJiraSprintsFromIssuesJob::class,[
                'jiraInstance' => CredentialsFactory::JIRA_MASTER_INSTANCE,
                'jiraIssues'=>$jiraIssuesSortedByRankAsc
            ]);

            $this->run(CreateOrUpdateSprintsIssuesJob::class,[
                'sprintsIssues'=>$sprintsIssues
            ]);
        }

        return $featureResult;
    }
}
