<?php

namespace JDD\Api\Console\Commands;

use Illuminate\Console\Command;
use JDD\Api\Providers\PackageServiceProvider;

class UpdatePackage extends Command
{
    /**
     * The name and signature of the console command.
     *
     *
     * @var string
     */
    protected $signature = 'coredump-jdd-api:jdd-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the installed jdd package';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Build asset: ' . $this->signature);
        $dir = getcwd();
        chdir(__DIR__ . '/../../../');
        file_exists('node_modules') ?: exec('npm install');
        exec('npm run build');
        chdir($dir);
        $this->call('vendor:publish', ['--tag' => PackageServiceProvider::PluginName . '/assets', '--force' => true]);
    }
}
