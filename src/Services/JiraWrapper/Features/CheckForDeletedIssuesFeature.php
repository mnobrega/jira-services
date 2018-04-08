<?php
namespace App\Services\JiraWrapper\Features;

use App\Data\RestApis\Config;
use App\Domains\Issue\Jobs\DeleteDeadIssuesJob;
use App\Domains\Issue\Jobs\DeleteDeadSlaveIssuesJob;
use App\Domains\Jira\Jobs\GetJiraConfigJob;
use App\Domains\Jira\Jobs\GetJiraIssueKeysJob;
use App\Domains\Jira\Jobs\SearchJiraIssuesByJQLJob;
use Lucid\Foundation\Feature;
use Illuminate\Http\Request;

class CheckForDeletedIssuesFeature extends Feature
{
    public function handle(Request $request)
    {
        $jiraConfig = $this->run(GetJiraConfigJob::class);

        $jiraIssues = $this->run(SearchJiraIssuesByJQLJob::class, [
            'jiraInstance'=>Config::JIRA_MASTER_INSTANCE,
            'jiraQuery'=>$jiraConfig->jira_issues_query." ORDER BY created ASC",
        ]);
        $liveJiraIssueKeys = $this->run(GetJiraIssueKeysJob::class,[
            'jiraIssues'=>$jiraIssues,
        ]);
        $result = $this->run(DeleteDeadIssuesJob::class,[
            'liveIssueKeys'=>$liveJiraIssueKeys,
        ]);
        return $result;
    }
}
