<?php
namespace App\Domains\JiraClient\Jobs;

use App\Data\Repositories\SlaveJiraIssueRepository;
use App\Data\SlaveJiraIssue;
use App\Data\Issue;
use Illuminate\Database\Eloquent\Collection;
use Lucid\Foundation\Job;

class PublishIssuesToSlaveJiraJob extends Job
{
    //TODO: HARDCODED - Move this to a table so that it can be configure dynamicaly
    static $slaveIssueTypeMappings = [
        'Task'=>10100,
        'Bug'=>10102,
        'Epic'=>10000,
        'Story'=>10001,
        'New Feature'=>10001,
        'Improvement'=>10001,
    ];
    static $slaveIssueStatusMapping = [
        "To Do"=>"To Do",
        "In Progress"=>"In Progress",
        "Ready To Review"=>"In Progress",
        "Review"=>"In Progress",
        "Done"=>"Done"
    ];

    private $slaveJiraApi;
    /** @var \App\Data\Issue[] */
    private $updatedIssues;
    private $repository;

    /**
     * PublishIssuesToSlaveJiraJob constructor.
     * @param \Jira_Api $slaveJiraApi
     * @param Collection $updatedIssues
     */
    public function __construct(\Jira_Api $slaveJiraApi, Collection $updatedIssues)
    {
        $this->slaveJiraApi = $slaveJiraApi;
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
                    break;
                case 1:
                    $jiraSlaveIssueKey = $foundSlaveJiraIssues[0]->key;
                    $this->updateSlaveJiraIssue($jiraSlaveIssueKey, $issue);
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
     */
    private function createSlaveJiraIssue(Issue $issue)
    {
        /** @var $createdJiraIssue \Jira_Api_Result*/
        $createdJiraIssue = $this->slaveJiraApi->createIssue($issue->project_key,$issue->summary,
            static::$slaveIssueTypeMappings[$issue->type]);

        return $this->updateSlaveJiraIssue($createdJiraIssue->getResult()["key"],$issue);
    }

    private function updateSlaveJiraIssue($slaveJiraIssueKey, Issue $issue)
    {
        $editParams = [
            "update"=>[
                "timetracking"=>[
                    [
                        "edit" => [
                            'originalEstimate'=>$issue->original_estimate/(60*60*8)."d",
                            'remainingEstimate'=>$issue->remaining_estimate/(60*60*8)."d",
                        ]
                    ]
                ]
            ],
            "fields"=>[
                "summary"=>$issue->summary
            ]
        ];
        $this->slaveJiraApi->editIssue($slaveJiraIssueKey,$editParams);
        return $slaveJiraIssueKey;
    }

}
