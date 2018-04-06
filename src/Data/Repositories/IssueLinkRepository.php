<?php
/**
 * Created by PhpStorm.
 * User: mnobrega
 * Date: 08/01/2018
 * Time: 01:00
 */

namespace App\Data\Repositories;

use App\Data\Issue;
use App\Data\IssueLink;

class IssueLinkRepository extends Repository
{
    public function create(Array $attributes, Issue $issue)
    {
        $this->model = new IssueLink();
        $this->model->issue()->associate($issue);
        $this->fill($attributes);
        return $this->model->save();
    }

    public function update(IssueLink $issueLink, array $attributes)
    {
        $this->model = $issueLink;
        return $this->fillAndSave($attributes);
    }

    /**
     * @param IssueLink $issueLink
     * @return bool|null
     * @throws \Exception
     */
    public function delete(IssueLink $issueLink)
    {
        return $issueLink->delete();
    }

    public function getUpdatedIssuesLinksByDateTimeInterval($from, $to)
    {
        return \App\Data\IssueLink::withTrashed()
            ->where('updated_at','>=',$from)
            ->where('updated_at','<=',$to)
            ->where('type','<>','Epic')
            ->orderBy('created_at','asc')
            ->get();
    }

    /**
     * @param $jiraIssueLink
     * @return array
     */
    static public function getAttributesFromJiraIssueLink($jiraIssueLink)
    {
        $attributes = array (
            'jira_id'=>$jiraIssueLink->id,
            'type'=>$jiraIssueLink->type->name,
            'inward'=>$jiraIssueLink->type->inward,
            'outward'=>$jiraIssueLink->type->outward,
            'inward_issue_key'=>@$jiraIssueLink->inwardIssue->key,
            'outward_issue_key'=>@$jiraIssueLink->outwardIssue->key
        );
        return $attributes;
    }
}