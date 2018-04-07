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
     * @return bool|null
     * @throws \Exception
     */
    public function delete(Issue $issue)
    {
        $this->model = $issue;
        return $this->model->delete();
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
     * @param $issueKey
     * @return \Illuminate\Database\Eloquent\Model|\App\Data\Issue
     */
    public function getByKey($issueKey)
    {
        return $this->findBy('issue_key',$issueKey);
    }

    /**
     * @param $issueKey
     * @return mixed|null
     * @throws \Exception
     */
    public function searchByKey($issueKey)
    {
        $issues = $this->getByAttributes(['issue_key'=>$issueKey]);
        switch (count($issues)) {
            case 0:
                return null;
            case 1:
                return $issues[0];
            default:
                throw new \Exception("There cannot be more than 1 issue with the same key");
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

    /**
     * @param $from
     * @param $to
     * @return \App\Data\Issue[]
     */
    public function getUpdatedIssuesWithTrashedByDateTimeInterval($from, $to)
    {
        return Issue::withTrashed()
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

    public function getLatestUpdatedIssue()
    {
        return $this->model
            ->orderBy('updated','desc')
            ->limit(1)
            ->get();
    }

    public function getCount()
    {
        return $this->model->count('*');
    }

    public function syncSprints(Issue $issue, $sprintIds)
    {
        $this->model = $issue;
        return $this->model->sprints()->sync($sprintIds);
    }

    /**
     * @param \JiraRestApi\Issue\Issue $jiraIssue
     * @param \JiraRestApi\Field\Field[] $jiraFields
     * @return array
     */
    static public function getAttributesFromJiraIssue(\JiraRestApi\Issue\Issue $jiraIssue, $jiraFields)
    {
        $fixVersions = $jiraIssue->fields->fixVersions;
        $attributesFromJiraIssue = array(
            'issue_key' => $jiraIssue->key,
            'project_key' => $jiraIssue->fields->project->key,
            'priority' => (is_object($jiraIssue->fields->priority))?$jiraIssue->fields->priority->name:null,
            'ranking' => null,//not available from JIRA directly
            'type' => $jiraIssue->fields->issuetype->name,
            'status' => $jiraIssue->fields->status->name,
            'summary' => $jiraIssue->fields->summary,
            'created' => $jiraIssue->fields->created->format("Y-m-d H:i:s"),
            'updated' => $jiraIssue->fields->updated->format("Y-m-d H:i:s"),
            'fix_version_id' => count($fixVersions)>0?$fixVersions[0]->id:null,
            'epic_link' => key_exists(static::getCustomFieldIdByName('Epic Link',$jiraFields),
                $jiraIssue->fields->customFields)? $jiraIssue->fields->customFields[
                    static::getCustomFieldIdByName('Epic Link',$jiraFields)]:null,
            'epic_name'=>key_exists(static::getCustomFieldIdByName('Epic Name',$jiraFields),
                $jiraIssue->fields->customFields)? $jiraIssue->fields->customFields[
                    static::getCustomFieldIdByName('Epic Name',$jiraFields)]:null,
            'epic_color'=>key_exists(static::getCustomFieldIdByName('Epic Colour',$jiraFields),
                $jiraIssue->fields->customFields)? $jiraIssue->fields->customFields[
                    static::getCustomFieldIdByName('Epic Colour',$jiraFields)]:null,
            'assignee' => is_object($jiraIssue->fields->assignee)?$jiraIssue->fields->assignee->name:null,
            'remaining_estimate' => $jiraIssue->fields->timeestimate==0?null:$jiraIssue->fields->timeestimate,
            'original_estimate' => is_object($jiraIssue->fields->timeoriginalestimate)?
                $jiraIssue->fields->timeoriginalestimate->scalar:null,
        );
        return $attributesFromJiraIssue;
    }

    static public function getCustomFieldIdByName($customFieldName, $fields)
    {
        foreach ($fields as $field) {
            if ($field->custom && $field->name==$customFieldName) {
                return $field->id;
            }
        }
        return null;
    }

}