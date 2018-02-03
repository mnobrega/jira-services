<?php
/**
 * Created by PhpStorm.
 * User: mnobrega
 * Date: 27/01/2018
 * Time: 23:59
 */

namespace App\Data\RestApis;

use App\Data\Sprint;
use JiraAgileRestApi\Sprint\Sprint as JiraSprint;
use JiraAgileRestApi\Board\BoardService;
use JiraAgileRestApi\Configuration\ArrayConfiguration;
use JiraAgileRestApi\Sprint\SprintService;

class JiraAgile implements JiraAgileInterface
{
    const BOARD_TYPE_SCRUM = 'scrum';
    const BOARD_TYPE_KANBAN = 'kanban';

    const FIELD_NAME_SPRINT = 'Sprint';

    private $boardService;
    private $sprintService;

    /**
     * JiraAgile constructor.
     * @param $instance
     * @throws \Exception
     */
    public function __construct($instance)
    {
        $configuration = new ArrayConfiguration(Config::getCredentials($instance));
        $this->boardService = new BoardService($configuration);
        $this->sprintService = new SprintService($configuration);
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

    public function createSprint(Sprint $sprint)
    {
        $jiraSprint = new JiraSprint();
        //TODO - set params
    }

    public function updateSprint($sprintId, Sprint $sprint)
    {

    }

}