<?php
namespace App\Domains\Sync\Jobs;

use App\Data\Repositories\WrapperSyncEventRepository;
use App\Data\WrapperSyncEvent;
use Lucid\Foundation\Job;

class CreateWrapperSyncEventJob extends Job
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
        $this->repository = new WrapperSyncEventRepository(new WrapperSyncEvent());
    }

    /**
     * @return WrapperSyncEvent|\Illuminate\Database\Eloquent\Model
     */
    public function handle()
    {
        return $this->repository->create($this->fromDateTime->format("Y-m-d H:i:s"),
            $this->toDateTime->format("Y-m-d H:i:s"));
    }
}
