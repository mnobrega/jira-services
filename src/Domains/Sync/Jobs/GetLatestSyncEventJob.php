<?php
namespace App\Domains\Sync\Jobs;

use App\Data\Repositories\SyncEventRepository;
use App\Data\SyncEvent;
use Lucid\Foundation\Job;

class GetLatestSyncEventJob extends Job
{
    private $repository;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->repository = new SyncEventRepository(new SyncEvent());
    }

    /**
     * @return SyncEvent
     * @throws \Exception
     */
    public function handle()
    {
        $syncEvents = $this->repository->getLatestSyncEvent();
        switch (count($syncEvents)) {
            case 0:
                $syncEvent = new SyncEvent();
                $syncEvent->from_datetime = "2000-01-01 00:00:00";
                $syncEvent->to_datetime = "2000-01-01 00:00:00";
                break;
            case 1:
                $syncEvent = $syncEvents[0];
                break;
            default:
                throw new \Exception("More than 1 sync event was found. Total:".count($syncEvents));
        }

        return $syncEvent;
    }
}
