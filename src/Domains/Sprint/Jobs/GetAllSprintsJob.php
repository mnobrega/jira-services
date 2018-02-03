<?php
namespace App\Domains\Sprint\Jobs;

use App\Data\Repositories\SprintRepository;
use App\Data\Sprint;
use Lucid\Foundation\Job;

class GetAllSprintsJob extends Job
{
    private $repository;

    /**
     * GetAllSprintsJob constructor.
     */
    public function __construct()
    {
        $this->repository = new SprintRepository(new Sprint());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function handle()
    {
        return $this->repository->all();
    }
}
