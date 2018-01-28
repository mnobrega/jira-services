<?php
/**
 * Created by PhpStorm.
 * User: mnobrega
 * Date: 28/01/2018
 * Time: 00:38
 */

namespace App\Data\RestApis;

use JiraAgileRestApi\JiraClient as JiraAgileClient;
use JiraGreenhopperRestApi\JiraClient as JiraGreenhopperClient;

class CredentialsFactory
{
    const JIRA_MASTER_INSTANCE = 'master';
    const JIRA_SLAVE_INSTANCE = 'slave';

    /**
     * @param $instance
     * @return array
     * @throws \Exception
     */
    static public function getCredentials($instance)
    {
        switch ($instance) {
            case static::JIRA_MASTER_INSTANCE:
                return [
                    'jiraHost'=>env('JIRA_HOST'),
                    'jiraUser'=>env('JIRA_USERNAME'),
                    'jiraPassword'=>env('JIRA_PASSWORD'),
                    'jiraVersion'=>env('JIRA_VERSION'),
                ];
                break;
            case static::JIRA_SLAVE_INSTANCE:
                return [
                    'jiraHost'=>env('JIRA_SLAVE_HOST'),
                    'jiraUser'=>env('JIRA_SLAVE_USERNAME'),
                    'jiraPassword'=>env('JIRA_SLAVE_PASSWORD'),
                    'jiraVersion'=>env('JIRA_SLAVE_VERSION'),
                ];
                break;
            default:
                throw new \Exception("Unknown jira instance:".$instance);
        }
    }

    /**
     * @param $version
     * @param $instance
     * @return JiraAgile|JiraGreenhopper
     * @throws \Exception
     */
    static public function getJiraAgile($version, $instance)
    {
        if (in_array($version,JiraAgileClient::$compatibleJiraVersions)) {
            return new JiraAgile($instance);
        } elseif (in_array($version,JiraGreenhopperClient::$compatibleJiraVersions)) {
            return new JiraGreenhopper($instance);
        } else {
            throw new \Exception("Your JIRA version is not compatible with any available library");
        }
    }
}