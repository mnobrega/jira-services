<?php
namespace App\Domains\Database\Jobs;

use Lucid\Foundation\Job;

class BeginDatabaseTransactionJob extends Job
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

    /**
     * @throws \Exception
     */
    public function handle()
    {
        \DB::beginTransaction();
    }
}
