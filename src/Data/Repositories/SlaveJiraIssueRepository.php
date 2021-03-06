<?php
/**
 * Created by PhpStorm.
 * User: mnobrega
 * Date: 15/01/2018
 * Time: 01:10
 */

namespace App\Data\Repositories;

use App\Data\Issue;
use App\Data\SlaveJiraIssue;
use App\Data\SyncEvent;

class SlaveJiraIssueRepository extends Repository
{
    public function create(Issue $issue, $slaveJiraIssueKey)
    {
        $this->model = new SlaveJiraIssue();
        $attributes = [
            'slave_issue_key' => $slaveJiraIssueKey,
            'master_issue_key' => $issue->issue_key,
        ];
        return $this->fillAndSave($attributes);
    }

    /**
     * @param SlaveJiraIssue $slaveJiraIssue
     * @return bool|null
     * @throws \Exception
     */
    public function delete(SlaveJiraIssue $slaveJiraIssue)
    {
        $this->model = $slaveJiraIssue;
        return $this->model->delete();
    }

    /**
     * @param $masterIssueKey
     * @return mixed|null
     * @throws \Exception
     */
    public function searchByMasterIssueKey($masterIssueKey)
    {
        $issues = $this->getByAttributes(["master_issue_key"=>$masterIssueKey]);
        switch (count($issues)) {
            case 0:
                return null;
                break;
            case 1:
                return $issues[0];
                break;
            default:
                throw new \Exception("More than 1 result found for the same Master Issue key:".$masterIssueKey);
        }
    }
}