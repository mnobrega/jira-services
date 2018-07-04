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
    const FIELD_NAME_FLAGGED = 'Flagged';

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

    static public function getFieldFromJiraIssueHistoryItem($item)
    {
        return $item->field;
    }

    /**
     * @param $jiraIssueHistory
     * @return array
     */
    static public function getAttributesFromJiraIssueHistory($jiraIssueHistory, $item)
    {
        $created = new \DateTime($jiraIssueHistory->created);
        $attributes = array (
            'jira_id'=>$jiraIssueHistory->id,
            'created'=>$created->format("Y-m-d H:i:s"),
            'field'=>$item->field,
            'field_type'=>$item->fieldtype,
            'from_string'=>$item->fromString,
            'to_string'=>$item->toString,
            'author_name'=>property_exists($jiraIssueHistory,"author")?$jiraIssueHistory->author->name:"",
        );
        return $attributes;
    }
}