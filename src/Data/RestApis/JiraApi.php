<?php
/**
 * Created by PhpStorm.
 * User: mnobrega
 * Date: 27/01/2018
 * Time: 23:59
 */

namespace App\Data\RestApis;

use JiraRestApi\Configuration\ArrayConfiguration;
use JiraRestApi\Field\FieldService;
use JiraRestApi\Issue\IssueField;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\Issue;
use JiraRestApi\Issue\TimeTracking;
use JiraRestApi\Issue\Transition;

class JiraApi
{
    //TODO: HARDCODED - Move this to a table so that it can be configure dynamicaly
    private static $slaveIssueTypeMappings = [
        'Task'=>'Task',
        'Bug'=>'Bug',
        'Epic'=>'Epic',
        'Story'=>'Story',
        'New Feature'=>'Story',
        'Improvement'=>'Story',
    ];
    //TODO: HARDCODED - Move this to a table so that it can be configure dynamicaly
    private static $slaveIssueStatusTransitionMapping = [
        "To Do"=>"To Do",
        "In Progress"=>"In Progress",
        "Ready To Review"=>"In Progress",
        "Review"=>"In Progress",
        "Done"=>"Done"
    ];
    //TODO: HARDCODED - Move this to a table so that it can be configure dynamicaly
    private static $slaveIssuePrioritiesMapping = [
        "Blocker"=>"Highest",
        "Critical"=>"High",
        "Major"=>"Medium",
        "Medium"=>"Medium",
        "Minor"=>"Low",
        "Trivial"=>"Lowest",
        "Highest"=>"Highest",
    ];
    //TODO: HARDCODED - Move this to a table so that it can be configure dynamicaly
    private static $slaveUsersMapping = [
        "smartins"=>"smartinsvv",
        "rfrade"=>"rfradevv",
        "ana.martins"=>"ana.martins",
        "cribeiro"=>"smartinsvv",
    ];

    const FIELD_NAME_EPIC_LINK = 'Epic Link';
    const FIELD_NAME_EPIC_NAME = 'Epic Name';
    const FIELD_NAME_EPIC_COLOR = 'Epic Colour';

    private $issueService;
    private $fieldService;

    private $epicLinkCustomFieldId=null;
    private $epicNameCustomFieldId=null;
    private $epicColorCustomFieldId=null;

    /**
     * JiraApi constructor.
     * @param $instance
     * @throws \Exception
     */
    public function __construct($instance)
    {
        $configuration = new ArrayConfiguration(Config::getCredentials($instance));
        $this->issueService = new IssueService($configuration);
        $this->fieldService = new FieldService($configuration);
    }

    /**
     * @param $issueIdOrKey
     * @return Issue|object
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function getIssue($issueIdOrKey)
    {
        return $this->issueService->get($issueIdOrKey);
    }

    /**
     * @param $query string
     * @return \JiraRestApi\Issue\Issue[]
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function getIssuesByJQL($query)
    {
        return $this->issueService
            ->search($query,0,1000)
            ->getIssues();
    }

    /**
     * @param $fieldName
     * @return \JiraRestApi\Field\Field|null
     * @throws \JiraRestApi\JiraException
     */
    public function getCustomFieldByName($fieldName)
    {
        $fields = $this->fieldService->getAllFields();
        /** @var \JiraRestApi\Field\Field $field */
        foreach ($fields as $field) {
            if ($field->name==$fieldName) {
                return $field;
            }
        }
        return null;
    }
    /**
     * @return null|string
     * @throws \JiraRestApi\JiraException
     */
    public function getEpicLinkCustomFieldId()
    {
        if (!is_null($this->epicLinkCustomFieldId)) {
            return $this->epicLinkCustomFieldId;
        }
        return $this->epicLinkCustomFieldId = $this->getCustomFieldByName(static::FIELD_NAME_EPIC_LINK)->id;
    }
    /**
     * @return null|string
     * @throws \JiraRestApi\JiraException
     */
    public function getEpicNameCustomFieldId()
    {
        if (!is_null($this->epicNameCustomFieldId)) {
            return $this->epicNameCustomFieldId;
        }
        return $this->epicNameCustomFieldId = $this->getCustomFieldByName(static::FIELD_NAME_EPIC_NAME)->id;
    }
    /**
     * @return null|string
     * @throws \JiraRestApi\JiraException
     */
    public function getEpicColorCustomFieldId()
    {
        if (!is_null($this->epicColorCustomFieldId)) {
            return $this->epicColorCustomFieldId;
        }
        return $this->epicColorCustomFieldId = $this->getCustomFieldByName(static::FIELD_NAME_EPIC_COLOR)->id;
    }

    /**
     * @param $issueKey
     * @return \JiraRestApi\Issue\IssueSearchResult|object
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function searchIssueByKey($issueKey)
    {
        return $this->issueService->search("key=".$issueKey);
    }

    /**
     * @param \App\Data\Issue $issue
     * @return mixed
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function createIssue(\App\Data\Issue $issue)
    {
        $issueField = new IssueField();

        $issueField->setProjectKey($issue->project_key)
            ->setPriorityName(static::$slaveIssuePrioritiesMapping[$issue->priority])
            ->setSummary($issue->summary)
            ->setIssueType(static::$slaveIssueTypeMappings[$issue->type]);

        if ($issue->type=='Epic') {
            $issueField->addCustomField($this->getEpicNameCustomFieldId(),$issue->epic_name);
            $issueField->addCustomField($this->getEpicColorCustomFieldId(),$issue->epic_color);
        } else if (!is_null($issue->epic_link)) {
            $issueField->addCustomField($this->getEpicLinkCustomFieldId(),$issue->epic_link);
        }

        $createdJiraIssue = $this->issueService->create($issueField);

        return $this->updateIssue($createdJiraIssue->key, $issue);
    }

    /**
     * @param $issueIdOrKey
     * @param \App\Data\Issue $issue
     * @return Issue|object
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function updateIssue($issueIdOrKey, \App\Data\Issue $issue)
    {
        $editParams = [
            'notifyUsers' => false
        ];

        $issueField = new IssueField();
        $issueField->setProjectKey($issue->project_key)
            ->setPriorityName(static::$slaveIssuePrioritiesMapping[$issue->priority])
            ->setSummary($issue->summary)
            ->setIssueType(static::$slaveIssueTypeMappings[$issue->type]);
        if (!is_null($issue->assignee)) {
            $issueField->setAssigneeName(static::$slaveUsersMapping[$issue->assignee]);
        }
        if ($issue->type=='Epic') {
            $issueField->addCustomField($this->getEpicNameCustomFieldId(),$issue->epic_name);
            $issueField->addCustomField($this->getEpicColorCustomFieldId(),$issue->epic_color);
        } else if (!is_null($issue->epic_link)) {
            $issueField->addCustomField($this->getEpicLinkCustomFieldId(),$issue->epic_link);
        }

        $this->issueService->update($issueIdOrKey, $issueField, $editParams);

        if (static::$slaveIssueTypeMappings[$issue->type]!='Bug') {
            $timeTracking = new TimeTracking();
            $timeTracking->setOriginalEstimate($issue->original_estimate/(60*60*8)."d");
            $timeTracking->setRemainingEstimate($issue->remaining_estimate/(60*60*8)."d");
            $this->issueService->timeTracking($issueIdOrKey,$timeTracking);
        }

        $transition = new Transition();
        $transition->setTransitionName(static::$slaveIssueStatusTransitionMapping[$issue->status]);
        $this->issueService->transition($issueIdOrKey,$transition);

        return $this->getIssue($issueIdOrKey);
    }
}