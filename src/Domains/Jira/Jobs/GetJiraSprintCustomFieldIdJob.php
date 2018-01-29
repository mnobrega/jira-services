<?php
namespace App\Domains\Jira\Jobs;

use App\Data\RestApis\JiraApi;
use Lucid\Foundation\Job;

class GetJiraSprintCustomFieldIdJob extends Job
{
    const FIELD_NAME_SPRINT = 'Sprint';

    private $jiraApi;

    public function __construct($jiraInstance)
    {
        $this->jiraApi = new JiraApi($jiraInstance);
    }

    public function handle()
    {
        $fields = $this->jiraApi->getAllFields();
        foreach ($fields as $field) {
            if ($field->name==static::FIELD_NAME_SPRINT) {
                return $field->id;
            }
        }
        throw new \Exception("There is no Sprint field in your JIRA instance.");
    }
}
