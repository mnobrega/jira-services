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
     * @param \JiraRestApi\Issue\Issue $jiraIssue
     * @return Issue|\Illuminate\Database\Eloquent\Model
     */
    public function create(\JiraRestApi\Issue\Issue $jiraIssue)
    {
        $this->model = new Issue();
        $attributes = $this->getAttributesFromJiraIssue($jiraIssue);
        return $this->fillAndSave($attributes);
    }

    /**
     * @param Issue $issue
     * @param \JiraRestApi\Issue\Issue $jiraIssue
     * @return Issue|\Illuminate\Database\Eloquent\Model
     */
    public function update(Issue $issue, \JiraRestApi\Issue\Issue $jiraIssue)
    {
        $this->model = $issue;
        $attributes = array_merge($this->getAttributesFromJiraIssue($jiraIssue));
        return $this->fillAndSave($attributes);
    }

    /**
     * @param \JiraRestApi\Issue\Issue $jiraIssue
     * @return array
     */
    private function getAttributesFromJiraIssue(\JiraRestApi\Issue\Issue $jiraIssue)
    {
        $fixVersions = $jiraIssue->fields->fixVersions;
        $version = count($fixVersions)>0?$fixVersions[0]->name:null;
        $attributesFromJiraIssue = array(
            'key' => $jiraIssue->key,
            'project_key' => $jiraIssue->fields->project->key,
            'priority' => $jiraIssue->fields->priority->name,
            'type' => $jiraIssue->fields->issuetype->name,
            'status' => $jiraIssue->fields->status->name,
            'summary' => $jiraIssue->fields->summary,
            'created' => $jiraIssue->fields->created->format("Y-m-d H:i:s"),
            'updated' => $jiraIssue->fields->updated->format("Y-m-d H:i:s"),
            'fix_version' => $version,
            'epic_link' => key_exists('customfield_10006',$jiraIssue->fields->customFields)?
                $jiraIssue->fields->customFields["customfield_10006"]:null,
            'assignee' => is_object($jiraIssue->fields->assignee)?$jiraIssue->fields->assignee->name:null,
            'remaining_estimate' => $jiraIssue->fields->timeestimate==0?null:$jiraIssue->fields->timeestimate,
            'original_estimate' => is_object($jiraIssue->fields->timeoriginalestimate)?
                $jiraIssue->fields->timeoriginalestimate->scalar:null,
        );
        return $attributesFromJiraIssue;
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
}