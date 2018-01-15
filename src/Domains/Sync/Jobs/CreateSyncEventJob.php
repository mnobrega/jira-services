<?php
namespace App\Domains\Sync\Jobs;

use App\Data\Repositories\SyncEventRepository;
use App\Data\SyncEvent;
use Lucid\Foundation\Job;

class CreateSyncEventJob extends Job
{
    private $fromDateTime;
    private $toDateTime;
    private $repository;

    /**
     * CreateSyncEventJob constructor.
     * @param \DateTime $fromDateTime
     * @param \DateTime $toDateTime
     */
    public function __construct(\DateTime $fromDateTime, \DateTime $toDateTime)
    {
        $this->fromDateTime = $fromDateTime;
        $this->toDateTime = $toDateTime;
        $this->repository = new SyncEventRepository(new SyncEvent());
    }

    /**
     * @return SyncEvent|\Illuminate\Database\Eloquent\Model
     */
    public function handle()
    {
        return $this->repository->create($this->fromDateTime->format("Y-m-d H:i:s"),
            $this->toDateTime->format("Y-m-d H:i:s"));
    }
}
