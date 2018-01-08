<?php
namespace App\Services\JiraWrapper\Features;

use App\Domains\JiraClient\Jobs\GetConnectionJob;
use App\Domains\JiraClient\Jobs\SearchIssuesByJQLJob;
use Lucid\Foundation\Feature;

class CopyJiraIssuesToDatabaseFeature extends Feature
{
    public function handle()
    {
        $getConnectionJobParams = [
            'host'=>env('JIRA_HOST'),
            'user'=>env('JIRA_USERNAME'),
            'pass'=>env('JIRA_PASSWORD'),
        ];
        $jiraApi = $this->run(GetConnectionJob::class, $getConnectionJobParams);

        $searchIssuesByJQLParams = [
            'jiraApi'=>$jiraApi,
            'query'=>env('JIRA_ISSUES_QUERY'),
        ];
        $jiraIssues = $this->run(SearchIssuesByJQLJob::class, $searchIssuesByJQLParams);

        dd($jiraIssues);
    }
}
