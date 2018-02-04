<?php
/**
 * Created by PhpStorm.
 * User: mnobrega
 * Date: 28/01/2018
 * Time: 20:49
 */

namespace App\Data\RestApis;

use App\Data\Issue;
use App\Data\Sprint;
use JiraGreenhopperRestApi\Configuration\ArrayConfiguration;
use JiraGreenhopperRestApi\ExperimentalApi\Board\BoardService;

class JiraAgileGreenhopper implements JiraAgileInterface
{
    private $boardService;

    /**
     * JiraAgileGreenhopper constructor.
     * @param $instance
     * @throws \Exception
     */
    public function __construct($instance)
    {
        $configuration = new ArrayConfiguration(Config::getCredentials($instance));
        $this->boardService = new BoardService($configuration);
    }

    /**
     * @param $boardName
     * @return \JiraGreenhopperRestApi\ExperimentalApi\Board\Board|null
     * @throws \JiraGreenhopperRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function getBoardByName($boardName)
    {
        $boardsSearchResult = $this->boardService->getAllBoards();
        foreach ($boardsSearchResult->values as $board) {
            if ($board->name==$boardName) {
                return $board;
            }
        }
        return null;
    }

    /**
     * @param $boardId
     * @return \JiraGreenhopperRestApi\ExperimentalApi\Sprint\Sprint[]
     * @throws \JiraGreenhopperRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function getBoardOpenSprints($boardId)
    {
        $sprints = array();
        $startAt = 0;
        $maxResults = 50;
        while ($maxResults > 0) {
            $sprintsSearch = $this->boardService->getSprints($boardId,["startAt"=>$startAt]);
            foreach ($sprintsSearch->values as $sprint){
                if ($sprint->state!=\JiraGreenhopperRestApi\ExperimentalApi\Sprint\Sprint::STATE_CLOSED) {
                    $sprints[] = $sprint;
                }
            }
            $maxResults = $sprintsSearch->maxResults;
            $startAt = $startAt + $maxResults + 1;
        }
        return $sprints;
    }

    /**
     * @param $boardId
     * @param Sprint $sprint
     * @throws \Exception
     */
    public function createBoardSprint($boardId, Sprint $sprint)
    {
        throw new \Exception("Not Implemented yet");
    }

    /**
     * @param $sprintId
     * @param Sprint $sprint
     * @throws \Exception
     */
    public function updateSprint($sprintId, Sprint $sprint)
    {
        throw new \Exception("Not implemented yet");
    }

    /**
     * @param $issueKey
     * @param $sprintId
     * @throws \Exception
     */
    public function moveIssueToSprint($issueKey, $sprintId)
    {
        throw new \Exception("Not implemented yet");
    }

    /**
     * @param $issueKey
     * @throws \Exception
     */
    public function moveIssueToBacklog($issueKey)
    {
        throw new \Exception("Not implemented yet");
    }

    /**
     * @param $issueA
     * @param $issueB
     * @throws \Exception
     */
    public function rankIssueABeforeIssueB($issueA, $issueB)
    {
        throw new \Exception("Not implemented yet");
    }
}