<?php

namespace Framework\Console\Commands;

use App\Services\JiraWrapper\Features\CopyJiraIssuesToDatabaseFeature;
use Illuminate\Console\Command;
use Lucid\Foundation\ServesFeaturesTrait;

class CopyJiraIssuesToDatabase extends Command
{

    use ServesFeaturesTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jira-wrapper:issues:copy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy all JQL referred issues to the local database';

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
            $featureResult = $this->serve(CopyJiraIssuesToDatabaseFeature::class);
            $this->output->writeln('<info>'.$featureResult['createdIssues'].' Issues Created.</info>');
            $this->output->writeln('<info>'.$featureResult['updatedIssues'].' Issues Updated.</info>');
            $this->output->writeln('<info>'.$featureResult['createdSprints'].' Sprints Created.</info>');
            $this->output->writeln('<info>'.$featureResult['updatedSprints'].' Sprints Updated.</info>');
        } catch (\Exception $e) {
            $this->output->writeln('<error>An error has ocurred.</error>');
            $this->output->writeln('<info>Error: '.$e->getMessage().'</info>');
            dump($e);
        }
    }
}
