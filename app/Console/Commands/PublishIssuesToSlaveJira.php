<?php

namespace Framework\Console\Commands;

use App\Services\JiraSync\Features\PublishIssuesToSlaveJiraInstanceFeature;
use Illuminate\Console\Command;
use Lucid\Foundation\ServesFeaturesTrait;

class PublishIssuesToSlaveJira extends Command
{
    use ServesFeaturesTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jira-sync:issues:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish all the missing issues to a JIRA slave instance';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $this->serve(PublishIssuesToSlaveJiraInstanceFeature::class);
        } catch (\Exception $e) {
            dd($e);
            $this->output->writeln('<error>An error has ocurred.</error>');
            $this->output->writeln('<info>Error: '.$e->getMessage().'</info>');
        }
    }
}