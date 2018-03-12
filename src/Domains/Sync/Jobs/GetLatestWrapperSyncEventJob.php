<?php
namespace App\Domains\Sync\Jobs;

use App\Data\Repositories\WrapperSyncEventRepository;
use App\Data\WrapperSyncEvent;
use Lucid\Foundation\Job;

class GetLatestWrapperSyncEventJob extends Job
{
    private $repository;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->repository = new WrapperSyncEventRepository(new WrapperSyncEvent());
    }

    /**
     * @return WrapperSyncEvent
     * @throws \Exception
     */
    public function handle()
    {
        $wrapperSyncEvents = $this->repository->getLatestWrapperSyncEvent();
        switch (count($wrapperSyncEvents)) {
            case 0:
                $wrapperSyncEvent = new WrapperSyncEvent();
                $wrapperSyncEvent->from_datetime = "2000-01-01 00:00:00";
                $wrapperSyncEvent->to_datetime = "2000-01-01 00:00:00";
                break;
            case 1:
                $wrapperSyncEvent = $wrapperSyncEvents[0];
                break;
            default:
                throw new \Exception("More than 1 sync event was found. Total:".count($wrapperSyncEvents));
        }

        return $wrapperSyncEvent;
    }
}
