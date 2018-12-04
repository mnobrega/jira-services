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

    static $acceptedFieldNames = array(self::FIELD_NAME_FLAGGED, self::FIELD_NAME_STATUS);

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

    /**
     * @param $jiraIssueHistory
     * @return array
     */
    static public function getAttributesFromJiraIssueHistory($jiraIssueHistory, $jiraIssueHistoryItem)
    {
        $created = new \DateTime($jiraIssueHistory->created);
        $attributes = array (
            'jira_id'=>$jiraIssueHistory->id,
            'created'=>$created->format("Y-m-d H:i:s"),
            'field'=>$jiraIssueHistoryItem->field,
            'field_type'=>$jiraIssueHistoryItem->fieldtype,
            'from_string'=>$jiraIssueHistoryItem->fromString,
            'to_string'=>$jiraIssueHistoryItem->toString,
            'author_name'=>property_exists($jiraIssueHistory,"author")?$jiraIssueHistory->author->name:"",
        );
        return $attributes;
    }
}