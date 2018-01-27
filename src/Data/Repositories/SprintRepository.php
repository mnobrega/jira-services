<?php
/**
 * Created by PhpStorm.
 * User: mnobrega
 * Date: 27/01/2018
 * Time: 17:15
 */

namespace App\Data\Repositories;
use App\Data\Sprint;

class SprintRepository extends Repository
{
    public function create(array $attributes)
    {
        $this->model = new Sprint();
        return $this->fillAndSave($attributes);
    }

    static public function getAttributesFromJiraSprint(\JiraAgileRestApi\Sprint\Sprint $jiraSprint)
    {

    }
}