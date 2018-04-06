<?php
namespace App\Domains\Sprint\Jobs;

use App\Data\Repositories\SprintRepository;
use App\Data\Sprint;
use Lucid\Foundation\Job;

class CreateOrUpdateSprintsJob extends Job
{
    private $repository;
    private $jiraSprints;

    /**
     * CreateOrUpdateSprintsJob constructor.
     * @param \JiraAgileRestApi\Sprint\Sprint[]|\JiraGreenhopperRestApi\ExperimentalApi\Sprint\Sprint[] $jiraSprints
     */
    public function __construct(Array $jiraSprints)
    {
        $this->jiraSprints = $jiraSprints;
        $this->repository = new SprintRepository(new Sprint());
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        $result = [
            'created'=>array(),
            'updated'=>array(),
        ];
        $currentSprintJiraIds = array();
        foreach ($this->jiraSprints as $jiraSprint) {
            $currentSprintJiraIds[] = $jiraSprint->id;
            $foundSprints = $this->repository->getByAttributes(['jira_id'=>$jiraSprint->id]);
            switch(count($foundSprints)) {
                case 0:
                    $createdSprint = $this->repository->create(
                        SprintRepository::getAttributesFromJiraSprint($jiraSprint));
                    $result['created'][] = $createdSprint;
                    break;
                case 1:
                    $updatedSprint = $this->repository->update($foundSprints[0],
                        SprintRepository::getAttributesFromJiraSprint($jiraSprint));
                    $result['updated'][] = $updatedSprint;
                    break;
                default:
                    throw new \Exception("Found more than 1 sprint with the same sprint_id:".$jiraSprint->id);
            }
        }

        $sprints = $this->repository->all();
        foreach ($sprints as $sprint) {
            if(!in_array($sprint->jira_id,$currentSprintJiraIds)) {
                $this->repository->update($sprint,['state'=>'closed']);
            }
        }
        return $result;
    }
}
