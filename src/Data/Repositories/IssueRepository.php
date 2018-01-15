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
     * @param \Jira_Issue $jiraIssue
     * @return Issue|\Illuminate\Database\Eloquent\Model
     */
    public function create(\Jira_Issue $jiraIssue)
    {
        $this->model = new Issue();
        $attributes = $this->getAttributesFromJiraIssue($jiraIssue);
        return $this->fillAndSave($attributes);
    }

    /**
     * @param Issue $issue
     * @param \Jira_Issue $jiraIssue
     * @return Issue|\Illuminate\Database\Eloquent\Model
     */
    public function update(Issue $issue, \Jira_Issue $jiraIssue)
    {
        $this->model = $issue;
        $attributes = array_merge($this->getAttributesFromJiraIssue($jiraIssue));
        return $this->fillAndSave($attributes);
    }

    /**
     * @param \Jira_Issue $jiraIssue
     * @return array
     */
    private function getAttributesFromJiraIssue(\Jira_Issue $jiraIssue)
    {
        $created = new \DateTime($jiraIssue->getCreated());
        $updated = new \DateTime($jiraIssue->getUpdated());
        $fixVersions = $jiraIssue->getFields()["Fix Version/s"];
        $version = count($fixVersions)>0?$fixVersions[0]["name"]:null;
        $attributesFromJiraIssue = array(
            'key' => $jiraIssue->getKey(),
            'project_key' => $jiraIssue->getProject()["key"],
            'rank' => $jiraIssue->getFields()['Rank'],
            'type' => $jiraIssue->getIssueType()["name"],
            'status' => $jiraIssue->getStatus()["name"],
            'summary' => $jiraIssue->getSummary(),
            'created' => $created->format("Y-m-d H:i:s"),
            'updated' => $updated->format("Y-m-d H:i:s"),
            'fix_version' => $version,
            'epic_link' => $jiraIssue->getFields()["Epic Link"],
            'remaining_estimate' => $jiraIssue->getFields()["Remaining Estimate"],
            'original_estimate' => $jiraIssue->getFields()["Original Estimate"],
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
            ->orderBy('created','asc')
            ->get();
    }
}