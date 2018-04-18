<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BackupMongo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:mongo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make backup of all data in the mongo db storage';

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
            if ($this->isBackupDirAvailable()) {
                $this->error('Check .env file or make sure dir of logs exists');
                $this->log('Check .env file or make sure dir of logs exists');
                exit;
            }

            $targetDir = env('MONGO_BACKUP_DIR'). time();
            $this->info($targetDir);
            if ( !mkdir($targetDir, 0777, true) ) {
                $this->error('Cannot create backup dir');
                $this->log('Cannot create backup dir');
                exit;
            }

            $output = shell_exec("mongodump -d vk_bot -o $targetDir");
            $this->log($output);
        } catch (\Exception $exception) {
            $this->log($exception->getMessage());
        }
    }

    private function log($message)
    {
        Log::useFiles(storage_path().'/logs/cron.log');
        Log::info($message);
    }

    private function isBackupDirAvailable()
    {
        return !empty(env('MONGO_BACKUP_DIR')) && !is_dir(env('MONGO_BACKUP_DIR'));
    }
}
