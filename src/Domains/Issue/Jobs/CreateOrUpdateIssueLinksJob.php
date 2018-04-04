<?php
namespace App\Domains\Issue\Jobs;

use App\Data\Issue;
use App\Data\IssueLink;
use App\Data\Repositories\IssueLinkRepository;
use App\Data\Repositories\IssueRepository;
use JiraRestApi\JiraRestApiServiceProvider;
use Lucid\Foundation\Job;

class CreateOrUpdateIssueLinksJob extends Job
{
    private $jiraIssue;
    private $issueLinkRepository;
    private $issueRepository;
    private $jiraIssueLinks;

    /**
     * CreateOrUpdateIssueLinksJob constructor.
     * @param \JiraRestApi\Issue\Issue $jiraIssue
     * @param $jiraIssueLinks
     */
    public function __construct(\JiraRestApi\Issue\Issue $jiraIssue, Array $jiraIssueLinks)
    {
        $this->issueLinkRepository = new IssueLinkRepository(new IssueLink());
        $this->issueRepository = new IssueRepository(new Issue());
        $this->jiraIssue = $jiraIssue;
        $this->jiraIssueLinks = $jiraIssueLinks;
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        $issue = $this->issueRepository->getByKey($this->jiraIssue->key);

        $currentLinksJiraIds = array();
        foreach ($this->jiraIssueLinks as $jiraIssueLink) {
            $currentLinksJiraIds[] = $jiraIssueLink->id;
            $issueLinks = $this->issueLinkRepository->getByAttributes(["jira_id"=>$jiraIssueLink->id]);
            switch(count($issueLinks)) {
                case 0:
                    $issueLinkAttributes = IssueLinkRepository::getAttributesFromJiraIssueLink($jiraIssueLink);
                    $outwardIssue = $this->issueRepository->getByKey($issueLinkAttributes['outward_issue_key']);
                    $issueLinkAttributes['issue_id'] = $issue->id;
                    $issueLinkAttributes['outward_issue_id'] = $outwardIssue->id;
                    $this->issueLinkRepository->create($issueLinkAttributes, $issue);
                    break;
                case 1:
                    //do nothing
                    break;
                default:
                    throw new \Exception("Wrong number of issue links found. It should be 1.");
            }
        }
    }
}
