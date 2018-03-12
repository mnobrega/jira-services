<?php
namespace App\Domains\Jira\Jobs;

use App\Data\RestApis\JiraApi;
use Lucid\Foundation\Job;

class GetIssueHistoriesForDateIntervalJob extends Job
{
    private $jiraApi;
    private $issueIdOrkey;
    private $fromDateTime;
    private $toDateTime;

    /**
     * GetIssueChangelogJob constructor.
     * @param $jiraInstance
     * @param $issueIdOrKey
     * @param \DateTime $fromDateTime
     * @param \DateTime $toDateTime
     */
    public function __construct($jiraInstance, $issueIdOrKey, \DateTime $fromDateTime, \DateTime $toDateTime)
    {
        $this->jiraApi = new JiraApi($jiraInstance);
        $this->issueIdOrkey = $issueIdOrKey;
        $this->fromDateTime = $fromDateTime;
        $this->toDateTime = $toDateTime;
    }

    /**
     * @return mixed
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function handle()
    {
        return $this->jiraApi->getIssueHistoriesByDateInterval($this->issueIdOrkey, $this->fromDateTime,
            $this->toDateTime);
    }
}
