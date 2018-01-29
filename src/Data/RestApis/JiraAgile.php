<?php
/**
 * Created by PhpStorm.
 * User: mnobrega
 * Date: 27/01/2018
 * Time: 23:59
 */

namespace App\Data\RestApis;

use JiraAgileRestApi\Board\BoardService;
use JiraAgileRestApi\Configuration\ArrayConfiguration;
use JiraAgileRestApi\Issue\IssueService;

class JiraAgile implements JiraAgileInterface
{
    private $issueService;
    private $boardService;

    /**
     * JiraAgile constructor.
     * @param $instance
     * @throws \Exception
     */
    public function __construct($instance)
    {
        $configuration = new ArrayConfiguration(Config::getCredentials($instance));
        $this->issueService = new IssueService($configuration);
        $this->boardService = new BoardService($configuration);
    }

    /**
     * @param $boardName
     * @return \JiraAgileRestApi\Board\Board[]|null
     * @throws \JiraAgileRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function getBoardByName($boardName)
    {
        $boardSearchResult = $this->boardService->getAllBoards(['name'=>$boardName]);
        return $boardSearchResult->values[0];
    }

    /**
     * @param $boardId
     * @return \JiraAgileRestApi\Sprint\Sprint[]|null
     * @throws \JiraAgileRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function getBoardOpenSprints($boardId)
    {
        $sprintSearchResult = $this->boardService->getSprints($boardId,['state'=>'future,active']);
        return $sprintSearchResult->values;
    }
}