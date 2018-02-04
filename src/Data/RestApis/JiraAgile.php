<?php
/**
 * Created by PhpStorm.
 * User: mnobrega
 * Date: 27/01/2018
 * Time: 23:59
 */

namespace App\Data\RestApis;

use App\Data\Issue;
use App\Data\Sprint;
use JiraAgileRestApi\BacklogIssue\BacklogIssue;
use JiraAgileRestApi\BacklogIssue\BacklogIssueService;
use JiraAgileRestApi\JiraClient;
use JiraAgileRestApi\Sprint\Sprint as JiraSprint;
use JiraAgileRestApi\Board\BoardService;
use JiraAgileRestApi\Configuration\ArrayConfiguration;
use JiraAgileRestApi\Sprint\SprintIssue;
use JiraAgileRestApi\Sprint\SprintService;

class JiraAgile implements JiraAgileInterface
{
    const BOARD_TYPE_SCRUM = 'scrum';
    const BOARD_TYPE_KANBAN = 'kanban';

    const FIELD_NAME_SPRINT = 'Sprint';

    private $boardService;
    private $sprintService;
    private $backlogIssueService;

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
        $this->backlogIssueService = new BacklogIssueService($configuration);
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

    /**
     * @param $boardId
     * @param Sprint $sprint
     * @return JiraSprint|object
     * @throws \JiraAgileRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function createBoardSprint($boardId, Sprint $sprint)
    {
        $jiraSprint = $this->getJiraSprintFromSprint($sprint);
        $jiraSprint->setOriginBoardId($boardId);
        return $this->sprintService->create($jiraSprint);
    }

    /**
     * @param $jiraSprintId
     * @param Sprint $sprint
     * @return string
     * @throws \JiraAgileRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function updateSprint($jiraSprintId, Sprint $sprint)
    {
        $jiraSprint = $this->getJiraSprintFromSprint($sprint);
        return $this->sprintService->update($jiraSprintId,$jiraSprint);
    }

    /**
     * @param Issue $issueKey
     * @param Sprint $sprintId
     * @return string
     * @throws \JiraAgileRestApi\JiraException
     */
    public function moveIssueToSprint($issueKey, $sprintId)
    {
        $sprintIssue = new SprintIssue();
        $sprintIssue->issues = array($issueKey);
        return $this->sprintService->addIssues($sprintId, $sprintIssue);
    }

    /**
     * @param $issueKey
     * @return string
     * @throws \JiraAgileRestApi\JiraException
     */
    public function moveIssueToBacklog($issueKey)
    {
        $backlogIssue = new BacklogIssue();
        $backlogIssue->issues = array($issueKey);
        return $this->backlogIssueService->create($backlogIssue);
    }

    /**
     * @param Sprint $sprint
     * @return JiraSprint
     */
    private function getJiraSprintFromSprint(Sprint $sprint)
    {
        $startDate = new \DateTime($sprint->start_date);
        $endDate = new \DateTime($sprint->end_date);

        if ($startDate->getTimestamp()==$endDate->getTimestamp()) {
            $startDate->modify("-1 second");
        }

        $jiraSprint = new JiraSprint();
        $jiraSprint->setState($sprint->state)
            ->setName($sprint->name)
            ->setStartDate($startDate->format(JiraClient::JIRA_DATE_FORMAT))
            ->setEndDate($endDate->format(JiraClient::JIRA_DATE_FORMAT));
        return $jiraSprint;
    }

}