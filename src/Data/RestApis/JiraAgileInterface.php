<?php
/**
 * Created by PhpStorm.
 * User: mnobrega
 * Date: 28/01/2018
 * Time: 21:24
 */

namespace App\Data\RestApis;

use App\Data\Issue;
use App\Data\Sprint;

interface JiraAgileInterface
{
    public function getBoardByName($boardName);
    public function getBoardOpenSprints($boardId);
    public function createBoardSprint($boardId, Sprint $sprint);
    public function updateSprint($sprintId, Sprint $sprint);
    public function moveIssueToSprint($issueKey, $sprintId);
    public function moveIssueToBacklog($issueKey);
    public function rankIssueABeforeIssueB($issueAKey, $issueBKey);
}