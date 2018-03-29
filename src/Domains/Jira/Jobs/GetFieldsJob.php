<?php
namespace App\Domains\Jira\Jobs;

use App\Data\RestApis\JiraApi;
use Lucid\Foundation\Job;

class GetFieldsJob extends Job
{
    private $jiraApi;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($jiraInstance)
    {
        $this->jiraApi = new JiraApi($jiraInstance);
    }

    /**
     * @return \JiraRestApi\Field\Field []
     * @throws \JiraRestApi\JiraException
     */
    public function handle()
    {
        return $this->jiraApi->getFields();
    }
}
