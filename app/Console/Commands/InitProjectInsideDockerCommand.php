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
        shell_exec('curl https://disk.yandex.ru/d/qzbLR1NnyshCBg/photo.zip -o storage/app/public/photo.zip');
        shell_exec('unzip storage/app/public/photo -d storage/app/public');
        shell_exec('rm storage/app/public/photo.zip');
        $this->call('storage:link', ['--force' => true]);
        $this->call('key:generate', ['--force' => true]);
        $this->call('jwt:secret', ['--force' => true]);
        $this->call('migrate:fresh', ['--seed' => true, '--force' => true]);
        $this->call('optimize');
        shell_exec('chmod 777 -R ./storage/app/public');
        shell_exec('npm run dev');
    }
}
