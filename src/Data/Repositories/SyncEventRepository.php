<?php
/**
 * Created by PhpStorm.
 * User: mnobrega
 * Date: 14/01/2018
 * Time: 22:53
 */

namespace App\Data\Repositories;

use App\Data\Issue;
use App\Data\SyncEvent;
use Illuminate\Support\Collection;


class SyncEventRepository extends Repository
{
    /**
     * @param $from
     * @param $to
     * @return SyncEvent|\Illuminate\Database\Eloquent\Model
     */
    public function create($fromDateTime, $toDateTime)
    {
        $this->model = new SyncEvent();
        $attributes = [
            'from_datetime' => $fromDateTime,
            'to_datetime' => $toDateTime,
        ];
        return $this->fillAndSave($attributes);
    }

    /**
     * @param SyncEvent $syncEvent
     * @param $tuplesCreated
     * @param $tuplesUpdated
     * @param $tuplesDeleted
     * @return SyncEvent|\Illuminate\Database\Eloquent\Model
     */
    public function update(SyncEvent $syncEvent, $tuplesCreated, $tuplesUpdated, $tuplesDeleted)
    {
        $this->model = $syncEvent;
        $attributes = [
            'tuples_created' => $tuplesCreated,
            'tuples_updated' => $tuplesUpdated,
            'tuples_deleted' => $tuplesDeleted,
        ];
        return $this->fillAndSave($attributes);
    }

    /**
     * @return Collection
     */
    public function getLatestSyncEvent()
    {
        return $this->model
            ->orderBy('to_datetime','desc')
            ->limit(1)
            ->get();
    }
}