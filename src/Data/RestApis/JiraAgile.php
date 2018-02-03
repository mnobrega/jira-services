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

class JiraAgile implements JiraAgileInterface
{
    const BOARD_TYPE_SCRUM = 'scrum';
    const BOARD_TYPE_KANBAN = 'kanban';

    const FIELD_NAME_SPRINT = 'Sprint';

    private $boardService;

    /**
     * JiraAgile constructor.
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
     * @return \JiraAgileRestApi\Board\Board[]|null
     * @throws \JiraAgileRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function getBoardByName($boardName)
    {
        $boardSearchResult = $this->boardService->getAllBoards(['name'=>$boardName]);
        return count($boardSearchResult->values)==1?$boardSearchResult->values[0]:null;
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