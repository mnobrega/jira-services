<?php
/**
 * Created by PhpStorm.
 * User: mnobrega
 * Date: 15/01/2018
 * Time: 01:10
 */

namespace App\Data\Repositories;

use App\Data\SlaveJiraVersion;

class SlaveJiraVersionRepository extends Repository
{
    /**
     * @param $masterVersionId
     * @param $slaveVersionId
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create($masterVersionId, $slaveVersionId)
    {
        $this->model = new SlaveJiraVersion();
        $attributes = [
            'master_version_id'=>$masterVersionId,
            'slave_version_id'=>$slaveVersionId,
        ];
        return $this->fillAndSave($attributes);
    }

    /**
     * @param $masterVersionId
     * @return mixed|null
     * @throws \Exception
     */
    public function searchByMasterVersionId($masterVersionId)
    {
        $slaveVersions = $this->getByAttributes(["master_sprint_id"=>$masterVersionId]);
        switch(count($slaveVersions)) {
            case 0:
                return null;
                break;
            case 1:
                return $slaveVersions[0];
                break;
            default:
                throw new \Exception("More than 1 slave version found. Found ".count($slaveVersions));
        }
    }
}