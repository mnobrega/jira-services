<?php
/**
 * Created by PhpStorm.
 * User: mnobrega
 * Date: 27/01/2018
 * Time: 23:59
 */

namespace App\Data\RestApis;

use App\Data\SlaveJiraIssue;
use JiraRestApi\Configuration\ArrayConfiguration;
use JiraRestApi\Field\FieldService;
use JiraRestApi\Issue\IssueField;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\Issue;
use JiraRestApi\Issue\TimeTracking;
use JiraRestApi\Issue\Transition;
use JiraRestApi\IssueLink\IssueLink;
use JiraRestApi\IssueLink\IssueLinkService;
use JiraRestApi\Project\ProjectService;
use JiraRestApi\Version\Version;
use JiraRestApi\Version\VersionService;

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
        null=>"Low",
    ];
    //TODO: HARDCODED - Move this to a database table so it can by dynamic
    private static $slaveIssueAssigneesMapping = [
        "cribeiro"=>"android.dev",
        "ana.martins"=>"design.dev",
        "asoares"=>"backend.dev",
        "jsantos"=>"frontend.dev",
        "smartins"=>"android.dev",
    ];

    const SLAVE_JIRA_DEFAULT_USER = 'auto.sync.user';

    const FIELD_NAME_EPIC_LINK = 'Epic Link';
    const FIELD_NAME_EPIC_NAME = 'Epic Name';
    const FIELD_NAME_EPIC_COLOR = 'Epic Colour';

    const MAX_JIRA_RESULTS = 1000;

    private $issueService;
    private $issueLinkService;
    private $fieldService;
    private $versionService;
    private $projectService;

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
        $this->issueLinkService = new IssueLinkService($configuration);
        $this->fieldService = new FieldService($configuration);
        $this->versionService = new VersionService($configuration);
        $this->projectService = new ProjectService($configuration);
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
     * @param $issueIdOrKey
     * @param \DateTime $fromDateTime
     * @param \DateTime $toDateTime
     * @return array
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function getIssueHistoriesByDateInterval($issueIdOrKey, \DateTime $fromDateTime, \DateTime $toDateTime)
    {
        $issueHistoriesForDateInterval = array();
        $issueChangelog = $this->issueService->get($issueIdOrKey,array('expand'=>'changelog'));
        foreach ($issueChangelog->changelog->histories as $issueHistory) {
            $issueHistoryDateTime= new \DateTime($issueHistory->created);
            if ($issueHistoryDateTime->getTimestamp()>=$fromDateTime->getTimestamp() &&
                $issueHistoryDateTime->getTimestamp()<=$toDateTime->getTimestamp()) {
                $issueHistoriesForDateInterval[] = $issueHistory;
            }
        }
        return $issueHistoriesForDateInterval;
    }

    /**
     * @param $projectKey
     * @return \JiraRestApi\Project\Project|object
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function getProject($projectKey)
    {
        return $this->projectService->get($projectKey);
    }

    /**
     * @param $query string
     * @return \JiraRestApi\Issue\Issue[]
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function getIssuesByJQL($query)
    {
        $start = 0;
        $maxResults = static::MAX_JIRA_RESULTS;
        $issues = $this->issueService
            ->search($query,$start,$maxResults)
            ->getIssues();
        while (count($issues) == $start + static::MAX_JIRA_RESULTS) {
            $start += static::MAX_JIRA_RESULTS;
            array_merge($issues,$this->issueService
                ->search($query,$start,$maxResults)
                ->getIssues());
        }
        return $issues;
    }

    /**
     * @return array
     * @throws \JiraRestApi\JiraException
     */
    public function getFields()
    {
        return $this->fieldService->getAllFields();
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
     * @param $versionId
     * @return \JiraRestApi\Version\Version|object
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function getVersionById($versionId)
    {
        return $this->versionService->get($versionId);
    }

    /**
     * @param Version $version
     * @return Version|object
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function createVersion(Version $version)
    {
        return $this->versionService->create($version);
    }

    /**
     * @param $versionId
     * @param Version $version
     * @return Version|object
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function updateVersion($versionId, Version $version)
    {
        return $this->versionService->update($versionId, $version);
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

        if (!is_null($issue->assignee) && key_exists($issue->assignee,static::$slaveIssueAssigneesMapping)) {
            $issueField->setAssigneeName(static::$slaveIssueAssigneesMapping[$issue->assignee]);
        } else {
            $issueField->setAssigneeName(static::SLAVE_JIRA_DEFAULT_USER);
        }

        if ($issue->type=='Epic') {
            $issueField->addCustomField($this->getEpicNameCustomFieldId(),$issue->epic_name);
            $issueField->addCustomField($this->getEpicColorCustomFieldId(),$issue->epic_color);
        }

        if (!is_null($issue->epic_link)) {
            $issueField->addCustomField($this->getEpicLinkCustomFieldId(),$issue->epic_link);
        }
        if (!is_null($issue->fix_version_id)) {
            $issueField->fixVersions = array(array("id"=>$issue->fix_version_id));
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

    public function deleteIssue($issueIdOrKey)
    {
        return $this->issueService->deleteIssue($issueIdOrKey);
    }

    /**
     * @param \App\Data\IssueLink $issueLink
     * @param SlaveJiraIssue $slaveJiraIssue
     * @param $inwardSlaveJiraIssue
     * @param $outwardSlaveJiraIssue
     * @throws \JiraRestApi\JiraException
     */
    public function createIssueLink(\App\Data\IssueLink $issueLink, SlaveJiraIssue $slaveJiraIssue,
                                    $inwardSlaveJiraIssue, $outwardSlaveJiraIssue)
    {
        $jiraIssueLink = new IssueLink();
        $jiraIssueLink->setLinkTypeName($issueLink->type);
        if (!is_null($inwardSlaveJiraIssue)) {
            $jiraIssueLink->setInwardIssue($inwardSlaveJiraIssue->slave_issue_key);
        } else {
            $jiraIssueLink->setInwardIssue($slaveJiraIssue->slave_issue_key);
        }
        if (!is_null($outwardSlaveJiraIssue)) {
            $jiraIssueLink->setOutwardIssue($outwardSlaveJiraIssue->slave_issue_key);
        } else {
            $jiraIssueLink->setInwardIssue($slaveJiraIssue->slave_issue_key);
        }
        return $this->issueLinkService->addIssueLink($jiraIssueLink);
    }

    public function deleteIssueLink(\App\Data\SlaveJiraIssueLink $slaveJiraIssueLink) {
        return $this->issueLinkService->deleteIssueLink($slaveJiraIssueLink->slave_issue_link_jira_id);
    }
}