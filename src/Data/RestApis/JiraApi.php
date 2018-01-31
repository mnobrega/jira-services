<?php
/**
 * Created by PhpStorm.
 * User: mnobrega
 * Date: 27/01/2018
 * Time: 23:59
 */

namespace App\Data\RestApis;

use JiraRestApi\Configuration\ArrayConfiguration;
use JiraRestApi\Field\FieldService;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\Issue;

class JiraApi
{
    private $issueService;
    private $fieldService;

    /**
     * JiraApi constructor.
     * @param $instance
     * @throws \Exception
     */
    public function __construct($instance)
    {
        $configuration = new ArrayConfiguration(Config::getCredentials($instance));
        $this->issueService = new IssueService($configuration);
        $this->fieldService = new FieldService($configuration);
    }

    /**
     * @param $query string
     * @return \JiraRestApi\Issue\Issue[]
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function getIssuesByJQL($query)
    {
        return $this->issueService
            ->search($query,0,1000)
            ->getIssues();
    }

    /**
     * @param $fieldName
     * @return \JiraRestApi\Field\Field|null
     * @throws \JiraRestApi\JiraException
     */
    public function getCustomFieldByName($fieldName)
    {
        $fields = $this->fieldService->getAllFields();
        /** @var \JiraRestApi\Field\Field $field */
        foreach ($fields as $field) {
            if ($field->name==$fieldName) {
                return $field;
            }
        }
        return null;
    }
}