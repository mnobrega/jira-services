<?php
/**
 * Created by PhpStorm.
 * User: mnobrega
 * Date: 15/01/2018
 * Time: 01:10
 */

namespace App\Data\Repositories;

use App\Data\SlaveJiraSprint;
use App\Data\Sprint;

class SlaveJiraSprintRepository extends Repository
{
    /**
     * @param Sprint $sprint
     * @param $slaveJiraSprintId
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(Sprint $sprint, $slaveJiraSprintId)
    {
        $this->model = new SlaveJiraSprint();
        $attributes = [
            'master_sprint_jira_id'=>$sprint->jira_id,
            'slave_sprint_jira_id'=>$slaveJiraSprintId,
        ];
        return $this->fillAndSave($attributes);
    }

    /**
     * @param $masterSprintJiraId
     * @return mixed|null
     * @throws \Exception
     */
    public function searchByMasterSprintJiraId($masterSprintJiraId)
    {
        $slaveSprints = $this->getByAttributes(["master_sprint_jira_id"=>$masterSprintJiraId]);
        switch(count($slaveSprints)) {
            case 0:
                return null;
                break;
            case 1:
                return $slaveSprints[0];
                break;
            default:
                throw new \Exception("More than 1 slave sprint found. Found ".count($slaveSprints));
        }
    }
}