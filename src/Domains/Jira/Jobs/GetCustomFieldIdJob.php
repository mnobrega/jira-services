<?php
namespace App\Domains\Jira\Jobs;

use App\Data\RestApis\JiraApi;
use Lucid\Foundation\Job;

class GetCustomFieldIdJob extends Job
{
    private $jiraApi;
    private $fieldName;

    /**
     * GetCustomFieldIdJob constructor.
     * @param $jiraInstance
     * @param $fieldName
     */
    public function __construct($jiraInstance, $fieldName)
    {
        $this->jiraApi = new JiraApi($jiraInstance);
        $this->fieldName = $fieldName;
    }

    /**
     * @throws \JiraRestApi\JiraException
     */
    public function handle()
    {
        $customField = $this->jiraApi->getCustomFieldByName($this->fieldName);
        return $customField->id;
    }
}
