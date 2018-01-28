<?php
/**
 * Created by PhpStorm.
 * User: mnobrega
 * Date: 27/01/2018
 * Time: 23:59
 */

namespace App\Data\RestApis;

use JiraAgileRestApi\Configuration\ArrayConfiguration;
use JiraAgileRestApi\Issue\IssueService;
use JiraAgileRestApi\Sprint\SprintService;

class JiraAgile
{
    private $sprintService;
    private $issueService;

    /**
     * JiraAgile constructor.
     * @param $instance
     * @throws \Exception
     */
    public function __construct($instance)
    {
        $configuration = new ArrayConfiguration(CredentialsFactory::getCredentials($instance));
        $this->issueService = new IssueService($configuration);
    }

    /**
     * @param $issueIdOrKey
     * @return object
     * @throws \JiraAgileRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function getSprintFromIssue($issueIdOrKey)
    {
        return $this->issueService->get($issueIdOrKey);
    }
}