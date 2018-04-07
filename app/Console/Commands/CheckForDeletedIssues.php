<?php

namespace Framework\Console\Commands;

use App\Services\JiraWrapper\Features\CheckForDeletedIssuesFeature;
use Illuminate\Console\Command;
use Lucid\Foundation\ServesFeaturesTrait;

class CheckForDeletedIssues extends Command
{
    use ServesFeaturesTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jira-wrapper:issues:check-deleted';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks for deleted master JIRA issues.';

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
            $featureResult = $this->serve(CheckForDeletedIssuesFeature::class);
            $this->output->writeln('<info>'.$featureResult['deleted'].' Issues Deleted.</info>');
            $this->output->writeln('<info>'.$featureResult['keeped'].' Issues Keeped.</info>');
            $this->output->writeln('<info>Issues deleted:'.implode(",",$featureResult['deletedIssueKeys']).'</info>');
        } catch (\Exception $e) {
            $this->output->writeln('<error>An error has ocurred.</error>');
            $this->output->writeln('<info>Error: '.$e->getMessage().'</info>');
        }
    }
}
