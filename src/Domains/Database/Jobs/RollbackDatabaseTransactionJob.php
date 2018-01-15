<?php
namespace App\Domains\Database\Jobs;

use Lucid\Foundation\Job;

class RollbackDatabaseTransactionJob extends Job
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function handle()
    {
        \DB::rollBack();
    }
}
