<?php
/**
 * Created by PhpStorm.
 * User: mnobrega
 * Date: 08/01/2018
 * Time: 01:00
 */

namespace App\Data\Repositories;

use App\Data\Issue;

class IssueRepository extends Repository
{
    static private $jiraCustomFieldsMapping = [
        'epic_link'=>'customfield_10006',
    ];

    /**
     * @param array $attributes
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $attributes)
    {
        $this->model = new Issue();
        return $this->fillAndSave($attributes);
    }

    /**
     * @param Issue $issue
     * @param array $attributes
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update(Issue $issue, array $attributes)
    {
        $this->model = $issue;
        return $this->fillAndSave($attributes);
    }

    /**
     * @param $from
     * @param $to
     * @return Issue[]
     */
    public function getUpdatedIssuesByDateTimeInterval($from, $to)
    {
        return $this->model
            ->where('updated','>=',$from)
            ->where('updated','<=',$to)
            ->where('type','<>','Epic')
            ->orderBy('created','asc')
            ->get();
    }

    public function syncSprints(Issue $issue, $sprintIds)
    {
        $this->model = $issue;
        return $this->model->sprints()->sync($sprintIds);
    }

    /**
     * @param \JiraRestApi\Issue\Issue $jiraIssue
     * @return array
     */
    static public function getAttributesFromJiraIssue(\JiraRestApi\Issue\Issue $jiraIssue)
    {
        $fixVersions = $jiraIssue->fields->fixVersions;
        $attributesFromJiraIssue = array(
            'key' => $jiraIssue->key,
            'project_key' => $jiraIssue->fields->project->key,
            'priority' => $jiraIssue->fields->priority->name,
            'ranking' => null,//not available from JIRA directly
            'type' => $jiraIssue->fields->issuetype->name,
            'status' => $jiraIssue->fields->status->name,
            'summary' => $jiraIssue->fields->summary,
            'created' => $jiraIssue->fields->created->format("Y-m-d H:i:s"),
            'updated' => $jiraIssue->fields->updated->format("Y-m-d H:i:s"),
            'fix_version' => count($fixVersions)>0?$fixVersions[0]->name:null,
            'epic_link' => key_exists(static::$jiraCustomFieldsMapping['epic_link'],$jiraIssue->fields->customFields)?
                $jiraIssue->fields->customFields[static::$jiraCustomFieldsMapping['epic_link']]:null,
            'assignee' => is_object($jiraIssue->fields->assignee)?$jiraIssue->fields->assignee->name:null,
            'remaining_estimate' => $jiraIssue->fields->timeestimate==0?null:$jiraIssue->fields->timeestimate,
            'original_estimate' => is_object($jiraIssue->fields->timeoriginalestimate)?
                $jiraIssue->fields->timeoriginalestimate->scalar:null,
        );
        return $attributesFromJiraIssue;
    }
}