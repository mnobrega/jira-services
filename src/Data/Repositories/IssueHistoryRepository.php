<?php
/**
 * Created by PhpStorm.
 * User: mnobrega
 * Date: 08/01/2018
 * Time: 01:00
 */

namespace App\Data\Repositories;

use App\Data\Issue;
use App\Data\IssueHistory;

class IssueHistoryRepository extends Repository
{
    const FIELD_NAME_STATUS = 'status';

    public function create(Array $attributes, Issue $issue)
    {
        $this->model = new IssueHistory();
        $this->model->issue()->associate($issue);
        $this->fill($attributes);
        return $this->model->save();
    }

    public function update(IssueHistory $issueHistory, array $attributes)
    {
        $this->model = $issueHistory;
        return $this->fillAndSave($attributes);
    }

    /**
     * @param $issueId
     * @return \App\Data\IssueHistory []
     */
    public function getIssueHistoriesByIssueId($issueId)
    {
        return $this->model
            ->where('issue_id','=',$issueId)
            ->orderBy("created","asc")
            ->get();
    }

    static public function getFieldFromJiraIssueHistory($jiraIssueHistory)
    {
        return $jiraIssueHistory->items[0]->field;
    }

    /**
     * @param $jiraIssueHistory
     * @return array
     */
    static public function getAttributesFromJiraIssueHistory($jiraIssueHistory)
    {
        $created = new \DateTime($jiraIssueHistory->created);
        $attributes = array (
            'jira_id'=>$jiraIssueHistory->id,
            'created'=>$created->format("Y-m-d H:i:s"),
            'field'=>$jiraIssueHistory->items[0]->field,
            'field_type'=>$jiraIssueHistory->items[0]->fieldtype,
            'from_string'=>$jiraIssueHistory->items[0]->fromString,
            'to_string'=>$jiraIssueHistory->items[0]->toString,
            'author_name'=>property_exists($jiraIssueHistory,"author")?$jiraIssueHistory->author->name:"",
        );
        return $attributes;
    }
}