<?php

namespace App\Console\Commands;

use App\Jobs\Scheduler\DBCleanUpdateJob;
use Database\Seeders\Services\SeederStorageService;
use Illuminate\Console\Command;

class DBCleanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'db clean, cache update';

    /**
     * Execute the console command.
     *
     */
    public function handle(): void
    {
        if (app()->environment('local')) $this->call('telescope:prune', ['--env' => 'local']);
        dispatch(new DBCleanUpdateJob());
        app(SeederStorageService::class)->caching();
        echo('db cleaned, cache updated' . PHP_EOL);
    }
}
