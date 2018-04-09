<?php

namespace Framework\Console\Commands;

use App\Services\JiraWrapper\Features\CopyJiraIssuesHistoryToDatabaseFeature;
use Illuminate\Console\Command;
use Lucid\Foundation\ServesFeaturesTrait;

class CopyJiraIssuesHistoryToDatabase extends Command
{

    use ServesFeaturesTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jira-wrapper:issues:copy-history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy latest issues history to the local database';

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
            $featureResult = $this->serve(CopyJiraIssuesHistoryToDatabaseFeature::class);
            $this->output->writeln('<info>'.count($featureResult['created']).' Histories Created.</info>');
            $this->output->writeln('<info>'.count($featureResult['updated']).' Histories Updated.</info>');

        } catch (\Exception $e) {
            $this->output->writeln('<error>An error has ocurred.</error>');
            $this->output->writeln('<info>Error: '.$e->getMessage().'</info>');
        }
    }
}
