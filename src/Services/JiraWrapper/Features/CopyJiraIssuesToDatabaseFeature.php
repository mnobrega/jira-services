<?php
namespace App\Services\JiraWrapper\Features;

use App\Domains\Issue\Jobs\CreateOrUpdateIssuesJob;
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
            'password'=>env('JIRA_PASSWORD'),
        ]);

        $jiraIssues = $this->run(SearchIssuesByJQLJob::class, [
            'jiraApi'=>$jiraApi,
            'query'=>env('JIRA_ISSUES_QUERY'),
        ]);

        $jobResult = $this->run(CreateOrUpdateIssuesJob::class,[
            'jiraIssues'=>$jiraIssues
        ]);

        return $jobResult;
    }
}
