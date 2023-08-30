<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class InitProjectInsideDockerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'init project inside docker container';

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $this->call('storage:link');
        $this->call('key:generate');
        $this->call('jwt:secret');
        $this->call('migrate:fresh', ['--seed' => true]);
        $this->call('optimize');
    }
}
