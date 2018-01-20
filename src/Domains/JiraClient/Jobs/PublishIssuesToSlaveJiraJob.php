<?php
namespace App\Domains\JiraClient\Jobs;

use App\Data\Repositories\SlaveJiraIssueRepository;
use App\Data\SlaveJiraIssue;
use App\Data\Issue;
use Illuminate\Database\Eloquent\Collection;
use JiraRestApi\Configuration\ArrayConfiguration;
use JiraRestApi\Field\FieldService;
use JiraRestApi\Issue\IssueField;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\TimeTracking;
use JiraRestApi\Issue\Transition;
use Lucid\Foundation\Job;

class PublishIssuesToSlaveJiraJob extends Job
{
    //TODO: HARDCODED - Move this to a table so that it can be configure dynamicaly
    private static $slaveIssueTypeMappings = [
        'Task'=>'Task',
        'Bug'=>'Bug',
        'Epic'=>'Epic',
        'Story'=>'Story',
        'New Feature'=>'Story',
        'Improvement'=>'Story',
    ];
    private static $slaveIssueStatusTransitionMapping = [
        "To Do"=>"To Do",
        "In Progress"=>"In Progress",
        "Ready To Review"=>"In Progress",
        "Review"=>"In Progress",
        "Done"=>"Done"
    ];
    private static $slaveIssuePrioritiesMapping = [
        "Blocker"=>"Highest",
        "Critical"=>"High",
        "Major"=>"Medium",
        "Minor"=>"Low",
        "Trivial"=>"Lowest",
        "Highest"=>"Highest",
    ];
    private static $slaveCustomFieldsMapping = [
        "rank"=>"customfield_10005",
    ];
    private static $slaveUsersMapping = [
        "smartins"=>"smartinsvv",
        "rfrade"=>"rfradevv",
        "ana.martins"=>"ana.martins"
    ];

    private $slaveJiraApi;
    private $masterJiraHost;
    /** @var \App\Data\Issue[] */
    private $updatedIssues;
    private $repository;

    /**
     * PublishIssuesToSlaveJiraJob constructor.
     * @param IssueService $slaveJiraApi
     * @param Collection $updatedIssues
     * @param String $masterJiraHost
     */
    public function __construct(IssueService $slaveJiraApi, Collection $updatedIssues, $masterJiraHost)
    {
        $this->slaveJiraApi = $slaveJiraApi;
        $this->masterJiraHost = $masterJiraHost;
        $this->updatedIssues = $updatedIssues;
        $this->repository = new SlaveJiraIssueRepository(new SlaveJiraIssue());
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function handle()
    {
        $publishResult = [
          'created'=>0,
          'updated'=>0,
          'deleted'=>0,
        ];
        foreach ($this->updatedIssues as $issue) {
            $foundSlaveJiraIssues = $this->repository->getByAttributes(['issue_id'=>$issue->id]);
            switch (count($foundSlaveJiraIssues)) {
                case 0:
                    $slaveJiraIssueKey = $this->createSlaveJiraIssue($issue);
                    $this->repository->create($issue, $slaveJiraIssueKey);
                    $publishResult['created']++;
                    break;
                case 1:
                    $jiraSlaveIssueKey = $foundSlaveJiraIssues[0]->key;
                    $this->updateSlaveJiraIssue($jiraSlaveIssueKey, $issue);
                    $publishResult['updated']++;
                    break;
                default:
                    throw new \Exception("More than 1 slave Jira issue found for issue_id:".$issue->id);
            }
        }
        return $publishResult;
    }

    /**
     * @param Issue $issue
     * @return mixed
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    private function createSlaveJiraIssue(Issue $issue)
    {
        $issueField = new IssueField();

        $issueField->setProjectKey($issue->project_key)
            ->setPriorityName(static::$slaveIssuePrioritiesMapping[$issue->priority])
            ->setSummary($issue->summary)
            ->setIssueType(static::$slaveIssueTypeMappings[$issue->type]);

        $createdJiraIssue = $this->slaveJiraApi->create($issueField);

        return $this->updateSlaveJiraIssue($createdJiraIssue->key, $issue);
    }

    /**
     * @param $slaveJiraIssueKey
     * @param Issue $issue
     * @return mixed
     * @throws \JiraRestApi\JiraException
     * TODO: Sync Rank, Epic Link
     */
    private function updateSlaveJiraIssue($slaveJiraIssueKey, Issue $issue)
    {
        $editParams = [
            'notifyUsers' => false
        ];

        $issueField = new IssueField();
        $issueField->setProjectKey($issue->project_key)
            ->setPriorityName(static::$slaveIssuePrioritiesMapping[$issue->priority])
            ->setSummary($issue->summary)
            ->setIssueType(static::$slaveIssueTypeMappings[$issue->type])
            ->setDescription("Master JIRA link: ".$this->masterJiraHost."/browse/".$issue->key);
        if (!is_null($issue->assignee)) {
            $issueField->setAssigneeName(static::$slaveUsersMapping[$issue->assignee]);
        }
        $this->slaveJiraApi->update($slaveJiraIssueKey, $issueField, $editParams);

        $timeTracking = new TimeTracking();
        $timeTracking->setOriginalEstimate($issue->original_estimate/(60*60*8)."d");
        $timeTracking->setRemainingEstimate($issue->remaining_estimate/(60*60*8)."d");
        $this->slaveJiraApi->timeTracking($slaveJiraIssueKey,$timeTracking);

        $transition = new Transition();
        $transition->setTransitionName(static::$slaveIssueStatusTransitionMapping[$issue->status]);
        $this->slaveJiraApi->transition($slaveJiraIssueKey,$transition);

        return $slaveJiraIssueKey;
    }

}
