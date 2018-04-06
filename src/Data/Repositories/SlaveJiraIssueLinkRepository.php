<?php
/**
 * Created by PhpStorm.
 * User: mnobrega
 * Date: 15/01/2018
 * Time: 01:10
 */

namespace App\Data\Repositories;

use App\Data\SlaveJiraIssueLink;

class SlaveJiraIssueLinkRepository extends Repository
{
    public function create(Array $attributes)
    {
        $this->model = new SlaveJiraIssueLink();
        return $this->fillAndSave($attributes);
    }

    /**
     * @param $masterIssueLinkJiraId
     * @return mixed|null
     * @throws \Exception
     */
    public function searchByMasterIssueLinkJiraId($masterIssueLinkJiraId)
    {
        $slaveIssueLinks = $this->getByAttributes(["master_issue_link_jira_id"=>$masterIssueLinkJiraId]);
        switch(count($slaveIssueLinks)) {
            case 0:
                return null;
                break;
            case 1:
                return $slaveIssueLinks[0];
                break;
            default:
                throw new \Exception("More than 1 slave issue link found. Found ".count($slaveIssueLinks));
        }
    }
}