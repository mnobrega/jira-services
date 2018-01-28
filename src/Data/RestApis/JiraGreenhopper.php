<?php
/**
 * Created by PhpStorm.
 * User: mnobrega
 * Date: 28/01/2018
 * Time: 20:49
 */

namespace App\Data\RestApis;

use JiraGreenhopperRestApi\Configuration\ArrayConfiguration;
use JiraGreenhopperRestApi\ExperimentalApi\Board\BoardService;

class JiraGreenhopper implements JiraAgileInterface
{
    private $boardService;

    /**
     * JiraGreenhopper constructor.
     * @param $instance
     * @throws \Exception
     */
    public function __construct($instance)
    {
        $configuration = new ArrayConfiguration(CredentialsFactory::getCredentials($instance));
        $this->boardService = new BoardService($configuration);
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