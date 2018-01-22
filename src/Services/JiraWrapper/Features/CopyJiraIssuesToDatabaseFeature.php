<?php
namespace App\Services\JiraWrapper\Features;

use App\Domains\Issue\Jobs\CreateOrUpdateIssuesJob;
use App\Domains\Issue\Jobs\UpdateIssuesRankJob;
use App\Domains\JiraClient\Jobs\GetConnectionJob;
use App\Domains\JiraClient\Jobs\SearchIssuesByJQLJob;
use Lucid\Foundation\Feature;

class CopyJiraIssuesToDatabaseFeature extends Feature
{
    public function handle()
    {
        $jiraApi = $this->run(GetConnectionJob::class, [
            'host'=>env('JIRA_HOST'),
            'user'=>env('JIRA_USERNAME'),
            'pass'=>env('JIRA_PASSWORD'),
        ]);

        $jiraIssues = $this->run(SearchIssuesByJQLJob::class, [
            'jiraApi'=>$jiraApi,
            'query'=>env('JIRA_ISSUES_QUERY'." ORDER BY sprint DESC"),
        ]);

        $jobResult = $this->run(CreateOrUpdateIssuesJob::class,[
            'jiraIssues'=>$jiraIssues
        ]);

        $jiraIssuesSortedByRankAsc = $this->run(SearchIssuesByJQLJob::class,[
            'jiraApi'=>$jiraApi,
            'query'=>env('JIRA_ISSUES_QUERY')." AND sprint IS NOT EMPTY ORDER BY rank ASC",
        ]);

        dd($jiraIssuesSortedByRankAsc[0]);
        $this->run(UpdateIssuesRankJob::class,[
            'jiraIssues'=>$jiraIssuesSortedByRankAsc
        ]);

        return $jobResult;
    }
}
