<?php
namespace App\Domains\Issue\Jobs;

use App\Data\Issue;
use App\Data\IssueLink;
use App\Data\Repositories\IssueLinkRepository;
use App\Data\Repositories\IssueRepository;
use Lucid\Foundation\Job;

class CreateOrUpdateIssueLinksJob extends Job
{
    private $jiraIssue;
    private $issueLinkRepository;
    private $issueRepository;

    /**
     * CreateOrUpdateIssueLinksJob constructor.
     * @param \JiraRestApi\Issue\Issue $jiraIssue
     */
    public function __construct(\JiraRestApi\Issue\Issue $jiraIssue)
    {
        $this->issueLinkRepository = new IssueLinkRepository(new IssueLink());
        $this->issueRepository = new IssueRepository(new Issue());
        $this->jiraIssue = $jiraIssue;
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        $issue = $this->issueRepository->getByKey($this->jiraIssue->key);

        $currentLinksJiraIds = array();
        foreach ($this->jiraIssue->fields->issuelinks as $jiraIssueLink) {
            $currentLinksJiraIds[] = $jiraIssueLink->id;
            $issueLinks = $this->issueLinkRepository->getByAttributes(["jira_id"=>$jiraIssueLink->id]);
            switch(count($issueLinks)) {
                case 0:
                    $issueLinkAttributes = IssueLinkRepository::getAttributesFromJiraIssueLink($jiraIssueLink);
                    $inwardIssue = $this->issueRepository->searchByKey($issueLinkAttributes['inward_issue_key']);
                    $outwardIssue = $this->issueRepository->searchByKey($issueLinkAttributes['outward_issue_key']);
                    $issueLinkAttributes['inward_issue_id'] = !is_null($inwardIssue)?$inwardIssue->id:null;
                    $issueLinkAttributes['outward_issue_id'] = !is_null($outwardIssue)?$outwardIssue->id:null;
                    $this->issueLinkRepository->create($issueLinkAttributes, $issue);
                    break;
                case 1:
                    //do nothing
                    break;
                default:
                    throw new \Exception("Wrong number of issue links found. It should be 1.");
            }
        }

        //TODO: softdelete the ones that are missing in the currentLinks
    }
}
