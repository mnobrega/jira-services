<?php
namespace App\Domains\Issue\Jobs;

use App\Data\IssueLink;
use App\Data\Repositories\SlaveJiraIssueLinkRepository;
use App\Data\SlaveJiraIssueLink;
use Lucid\Foundation\Job;

class SearchSlaveJiraIssueLinkByMasterIssueLinkJob extends Job
{
    private $repository;
    private $masterIssueLink;

    /**
     * SearchSlaveJiraIssueLinkByMasterIssueLinkJob constructor.
     * @param IssueLink $masterIssueLink
     */
    public function __construct(IssueLink $masterIssueLink)
    {
        $this->repository = new SlaveJiraIssueLinkRepository(new SlaveJiraIssueLink());
        $this->masterIssueLink = $masterIssueLink;
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        return $this->repository->searchByMasterIssueLinkJiraId($this->masterIssueLink->jira_id);
    }
}
