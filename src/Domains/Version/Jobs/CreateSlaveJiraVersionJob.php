<?php
namespace App\Domains\Version\Jobs;

use App\Data\Repositories\SlaveJiraVersionRepository;
use App\Data\SlaveJiraVersion;
use Lucid\Foundation\Job;

class CreateSlaveJiraVersionJob extends Job
{
    private $repository;
    private $masterVersionId;
    private $slaveVersionId;

    /**
     * CreateSlaveJiraVersionJob constructor.
     * @param $masterVersionId
     * @param $slaveVersionId
     */
    public function __construct($masterVersionId, $slaveVersionId)
    {
        $this->repository = new SlaveJiraVersionRepository(new SlaveJiraVersion());
        $this->masterVersionId = $masterVersionId;
        $this->slaveVersionId = $slaveVersionId;
    }

    public function handle()
    {
        return $this->repository->create($this->masterVersionId, $this->slaveVersionId);
    }
}
