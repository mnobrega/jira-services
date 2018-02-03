<?php
namespace App\Domains\Sprint\Jobs;

use App\Data\Repositories\SlaveJiraSprintRepository;
use App\Data\SlaveJiraSprint;
use App\Data\Sprint;
use Lucid\Foundation\Job;

class SearchSlaveJiraSprintByMasterJiraSprintJob extends Job
{
    private $repository;
    private $masterJiraSprint;

    /**
     * SearchSlaveJiraSprintByMasterJiraSprintJob constructor.
     * @param Sprint $masterJiraSprint
     */
    public function __construct(Sprint $masterJiraSprint)
    {
        $this->repository = new SlaveJiraSprintRepository(new SlaveJiraSprint());
        $this->masterJiraSprint = $masterJiraSprint;
    }

    /**
     * @return mixed|null
     * @throws \Exception
     */
    public function handle()
    {
        return $this->repository->searchByMasterSprintId($this->masterJiraSprint->id);
    }
}
