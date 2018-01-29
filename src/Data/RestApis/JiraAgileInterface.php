<?php
/**
 * Created by PhpStorm.
 * User: mnobrega
 * Date: 28/01/2018
 * Time: 21:24
 */

namespace App\Data\RestApis;


interface JiraAgileInterface
{
    public function getBoardByName($boardName);
    public function getBoardOpenSprints($boardId);
}