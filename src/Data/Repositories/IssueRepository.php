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
        'epic_name'=>'customfield_10007',
        'epic_color'=>'customfield_10009',
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
     * @param $key
     * @return mixed
     * @throws \Exception
     */
    public function getByKey($key)
    {
        $issues = $this->getByAttributes(['key'=>$key]);
        if (count($issues)==1) {
            return $issues[0];
        } else {
            throw new \Exception("Not found or wrong number of issues with key:".$key);
        }
    }

    /**
     * @param $from
     * @param $to
     * @return \App\Data\Issue[]
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

    public function getIssuesSortedByRank($sortOrder)
    {
        return $this->model
            ->whereNotNull('ranking')
            ->orderBy('ranking',$sortOrder)
            ->get();
    }

    public function getIssuesDistinctVersions()
    {
        return $this->model
            ->select('project_key','fix_version_id')
            ->whereNotNull('fix_version_id')
            ->groupBy('fix_version_id')
            ->groupBy('project_key')
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
            'priority' => (is_object($jiraIssue->fields->priority))?$jiraIssue->fields->priority->name:null,
            'ranking' => null,//not available from JIRA directly
            'type' => $jiraIssue->fields->issuetype->name,
            'status' => $jiraIssue->fields->status->name,
            'summary' => $jiraIssue->fields->summary,
            'created' => $jiraIssue->fields->created->format("Y-m-d H:i:s"),
            'updated' => $jiraIssue->fields->updated->format("Y-m-d H:i:s"),
            'fix_version_id' => count($fixVersions)>0?$fixVersions[0]->id:null,
            'epic_link' => key_exists(static::$jiraCustomFieldsMapping['epic_link'],$jiraIssue->fields->customFields)?
                $jiraIssue->fields->customFields[static::$jiraCustomFieldsMapping['epic_link']]:null,
            'epic_name'=>key_exists(static::$jiraCustomFieldsMapping['epic_name'],$jiraIssue->fields->customFields)?
                $jiraIssue->fields->customFields[static::$jiraCustomFieldsMapping['epic_name']]:null,
            'epic_color'=>key_exists(static::$jiraCustomFieldsMapping['epic_color'],$jiraIssue->fields->customFields)?
                $jiraIssue->fields->customFields[static::$jiraCustomFieldsMapping['epic_color']]:null,
            'assignee' => is_object($jiraIssue->fields->assignee)?$jiraIssue->fields->assignee->name:null,
            'remaining_estimate' => $jiraIssue->fields->timeestimate==0?null:$jiraIssue->fields->timeestimate,
            'original_estimate' => is_object($jiraIssue->fields->timeoriginalestimate)?
                $jiraIssue->fields->timeoriginalestimate->scalar:null,
        );
        return $attributesFromJiraIssue;
    }
}