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
            'master_issue_key' => $issue->key,
        ];
        return $this->fillAndSave($attributes);
    }

    public function searchByMasterJiraKey($key)
    {
        
    }
}