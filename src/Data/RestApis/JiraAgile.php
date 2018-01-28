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

class JiraAgile implements JiraAgileInterface
{
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

    public function getBoard($boardName)
    {
        // TODO: Implement getBoard() method.
    }

    public function getBoardSprints($boardId)
    {
        // TODO: Implement getBoardSprints() method.
    }
}