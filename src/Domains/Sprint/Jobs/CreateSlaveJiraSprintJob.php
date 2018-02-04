<?php
namespace App\Domains\Sprint\Jobs;

use App\Data\Repositories\SlaveJiraSprintRepository;
use App\Data\SlaveJiraSprint;
use Lucid\Foundation\Job;

class CreateSlaveJiraSprintJob extends Job
{
    private $repository;
    private $masterJiraSprint;
    private $slaveJiraSprint;

    /**
     * CreateSlaveJiraSprintJob constructor.
     * @param $masterJiraSprint
     * @param $slaveJiraSprint
     */
    public function __construct($masterJiraSprint, $slaveJiraSprint)
    {
        $this->repository = new SlaveJiraSprintRepository(new SlaveJiraSprint());
        $this->masterJiraSprint = $masterJiraSprint;
        $this->slaveJiraSprint = $slaveJiraSprint;
    }

    public function handle()
    {
        return $this->repository->create($this->masterJiraSprint,$this->slaveJiraSprint->id);
    }
}
