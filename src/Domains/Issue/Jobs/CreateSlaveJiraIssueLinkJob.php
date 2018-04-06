<?php
namespace App\Domains\Issue\Jobs;

use App\Data\IssueLink;
use App\Data\Repositories\SlaveJiraIssueLinkRepository;
use App\Data\SlaveJiraIssueLink;
use Lucid\Foundation\Job;

class CreateSlaveJiraIssueLinkJob extends Job
{
    private $repository;
    private $masterIssueLink;
    private $slaveJiraIssueLink;

    /**
     * CreateSlaveJiraIssueLinkJob constructor.
     * @param IssueLink $masterIssueLink
     * @param \stdClass $slaveJiraIssueLink
     */
    public function __construct(IssueLink $masterIssueLink, \stdClass $slaveJiraIssueLink)
    {
        $this->repository = new SlaveJiraIssueLinkRepository(new SlaveJiraIssueLink());
        $this->masterIssueLink = $masterIssueLink;
        $this->slaveJiraIssueLink = $slaveJiraIssueLink;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function handle()
    {
        $attributes = [
            'master_issue_link_jira_id'=>$this->masterIssueLink->jira_id,
            'slave_issue_link_jira_id'=>(integer)$this->slaveJiraIssueLink->id,
        ];
        return $this->repository->create($attributes);
    }
}
