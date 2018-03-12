<?php
/**
 * Created by PhpStorm.
 * User: mnobrega
 * Date: 14/01/2018
 * Time: 22:53
 */

namespace App\Data\Repositories;

use App\Data\WrapperSyncEvent;
use Illuminate\Support\Collection;


class WrapperSyncEventRepository extends Repository
{
    /**
     * @param $fromDateTime
     * @param $toDateTime
     * @return WrapperSyncEvent|\Illuminate\Database\Eloquent\Model
     */
    public function create($fromDateTime, $toDateTime)
    {
        $this->model = new WrapperSyncEvent();
        $attributes = [
            'from_datetime' => $fromDateTime,
            'to_datetime' => $toDateTime,
        ];

        // TODO- remove its a test
        $this->fill($attributes);
        return $this->model;
        // TODO- remove its a test

        return $this->fillAndSave($attributes);
    }

    /**
     * @return Collection
     */
    public function getLatestWrapperSyncEvent()
    {
        return $this->model
            ->orderBy('to_datetime','desc')
            ->limit(1)
            ->get();
    }
}