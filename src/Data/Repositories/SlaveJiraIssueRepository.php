<?php
/**
 * Created by PhpStorm.
 * User: mnobrega
 * Date: 15/01/2018
 * Time: 01:10
 */

namespace App\Data\Repositories;

use App\Data\Issue;

class SlaveJiraIssueRepository extends Repository
{
    public function create(Issue $issue, $slaveJiraIssueKey)
    {
        $attributes = [
            'issue_id' => $issue->id,
            'key' => $slaveJiraIssueKey
        ];
        return $this->fillAndSave($attributes);
    }
}