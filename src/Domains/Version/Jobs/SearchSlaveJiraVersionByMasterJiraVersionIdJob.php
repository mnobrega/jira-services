<?php
namespace App\Domains\Version\Jobs;

use App\Data\Repositories\SlaveJiraVersionRepository;
use App\Data\SlaveJiraVersion;
use Lucid\Foundation\Job;

class SearchSlaveJiraVersionByMasterJiraVersionIdJob extends Job
{
    private $repository;
    private $masterVersionId;

    /**
     * GetSlaveJiraVersionByMasterJiraVersionIdJob constructor.
     * @param $masterVersionId
     */
    public function __construct($masterVersionId)
    {
        $this->repository = new SlaveJiraVersionRepository(new SlaveJiraVersion());
        $this->masterVersionId = $masterVersionId;
    }

    /**
     * @return SlaveJiraVersion
     * @throws \Exception
     */
    public function handle()
    {
        $slaveJiraVersions = $this->repository->getByAttributes(['master_version_id'=>$this->masterVersionId]);
        switch(count($slaveJiraVersions)) {
            case 0:
                return null;
                break;
            case 1:
                return $slaveJiraVersions[0];
                break;
            default:
                throw new \Exception("More than 1 slave jira version found.");
        }
    }
}
