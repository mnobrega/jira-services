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

    public function update($sprint, array $attributes)
    {
        $this->model = $sprint;
        return $this->fillAndSave($attributes);
    }

    static public function getAttributesFromJiraSprint(\JiraAgileRestApi\Sprint\Sprint $jiraSprint)
    {
        $startDate = new \DateTime($jiraSprint->startDate);
        $endDate = new \DateTime($jiraSprint->endDate);
        $attributesFromJiraSprint = [
            'sprint_id' => $jiraSprint->id,
            'state' => $jiraSprint->state,
            'start_date' => $startDate->format("Y-m-d H:i:s"),
            'end_date' => $endDate->format("Y-m-d H:i:s")
        ];
        return $attributesFromJiraSprint;
    }
}