<?php
/**
 * Created by PhpStorm.
 * User: mnobrega
 * Date: 27/01/2018
 * Time: 23:59
 */

namespace App\Data\RestApis;

use JiraRestApi\Configuration\ArrayConfiguration;
use JiraRestApi\Issue\IssueService;

class JiraApi
{
    private $issueService;

    /**
     * JiraApi constructor.
     * @param $instance
     * @throws \Exception
     */
    public function __construct($instance)
    {
        $configuration = new ArrayConfiguration(CredentialsFactory::getCredentials($instance));
        $this->issueService = new IssueService($configuration);
    }

    /**
     * @param $query
     * @return \JiraRestApi\Issue\Issue[]
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function getJiraIssuesByJQL($query)
    {
        return $this->issueService
            ->search($query,0,1000)
            ->getIssues();
    }
}