<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Logs\DeleteLogFiles;

class DeleteLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quote:deleteLogs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command delete all log-files everyday';

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
     * @param DeleteLogs $deleteLogs
     * @return int
     */
    public function handle(DeleteLogFiles $deleteLogFiles)
    {
        $deleteLogFiles->removeLogs();
    }
}
