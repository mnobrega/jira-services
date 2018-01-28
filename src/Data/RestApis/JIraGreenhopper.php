<?php
/**
 * Created by PhpStorm.
 * User: mnobrega
 * Date: 28/01/2018
 * Time: 17:38
 */

namespace App\Data\RestApis;


use JiraGreenhopperRestApi\ExperimentalApi\Sprint\SprintService;
use JiraRestApi\Configuration\ArrayConfiguration;

class JiraGreenhopper
{
    private $sprintService;

    /**
     * JiraGreenhopper constructor.
     * @param $instance
     * @throws \Exception
     */
    public function __construct($instance)
    {
        $configuration = new ArrayConfiguration(CredentialsFactory::getCredentials($instance));
        $this->sprintService = new SprintService($configuration);
    }
}