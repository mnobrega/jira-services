<?php
/**
 * Created by PhpStorm.
 * User: mnobrega
 * Date: 28/01/2018
 * Time: 00:38
 */

namespace App\Data\RestApis;


use Mockery\Exception;

class CredentialsFactory
{
    const JIRA_MASTER_INSTANCE = 'master';
    const JIRA_SLAVE_INSTANCE = 'slave';

    /**
     * @param $instance
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
}