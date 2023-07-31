<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'run';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        dd('run');
    }
}
