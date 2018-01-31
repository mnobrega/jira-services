<?php
namespace App\Domains\Sprint\Jobs;

use App\Data\Issue;
use App\Data\Repositories\IssueRepository;
use App\Data\Repositories\SprintRepository;
use App\Data\Sprint;
use Lucid\Foundation\Job;

class SyncSprintsIssuesJob extends Job
{
    private $issueRepository;
    private $sprintRepository;
    private $jiraIssues;
    private $jiraSprintCustomFieldId;

    /**
     * SyncSprintsIssuesJob constructor.
     * @param $jiraIssues \JiraRestApi\Issue\Issue[]
     * @param $sprints Sprint[]
     * @param $jiraSprintCustomFieldId string
     */
    public function __construct($jiraIssues, $jiraSprintCustomFieldId)
    {
        $this->issueRepository = new IssueRepository(new Issue());
        $this->sprintRepository = new SprintRepository(new Sprint());
        $this->jiraIssues = $jiraIssues;
        $this->jiraSprintCustomFieldId = $jiraSprintCustomFieldId;
    }

    /**
     *
     */
    public function handle()
    {
        $jiraSprintIdToSprintIdMap = $this->getJiraSprintIdToSprintIdMap();
        foreach ($this->jiraIssues as $jiraIssue) {
            $issue = $this->issueRepository->getByAttributes(['key'=>$jiraIssue->key])[0];
            $issueSprintIds = $this->getJiraIssueSprintIds($jiraIssue,$jiraSprintIdToSprintIdMap);
            $this->issueRepository->syncSprints($issue, $issueSprintIds);
        }
    }

    private function getJiraSprintIdToSprintIdMap()
    {
        $map = array();
        $allSprints = $this->sprintRepository->all();
        /** @var Sprint $sprint */
        foreach ($allSprints as $sprint) {
            $map[$sprint->jira_id] = $sprint->id;
        }
        return $map;
    }

    private function getJiraIssueSprintIds(\JiraRestApi\Issue\Issue $jiraIssue, Array $jiraSprintIdToSprintIdMap)
    {
        $sprintIds = array();
        if (key_exists($this->jiraSprintCustomFieldId,$jiraIssue->fields->customFields)) {
            $sprintStrings = $jiraIssue->fields->customFields[$this->jiraSprintCustomFieldId];
            foreach ($sprintStrings as $sprintString) {
                $sprintStringMod = str_replace('[',',',$sprintString);
                $sprintStrExploded = explode(',',$sprintStringMod);
                $jiraSprintId = explode('=',$sprintStrExploded[1])[1];
                if (key_exists($jiraSprintId,$jiraSprintIdToSprintIdMap)) {
                    $sprintIds[] = $jiraSprintIdToSprintIdMap[$jiraSprintId];
                }
            }
        }
        return $sprintIds;
    }
}
