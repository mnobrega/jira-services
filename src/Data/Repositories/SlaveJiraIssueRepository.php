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
            'key' => $slaveJiraIssueKey,
            'issue_id' => $issue->id,
        ];
        return $this->fillAndSave($attributes);
    }
}