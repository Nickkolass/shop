<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

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
    public function handle(): void
    {
        shell_exec('curl https://disk.yandex.ru/d/UXUnbmQv4Zndug/factory.zip -o storage/app/testing/factory.zip');
        shell_exec('unzip storage/app/testing/factory -d storage/app/testing');
        shell_exec('rm storage/app/testing/factory.zip');

        foreach (Storage::disk('testing')->directories() as $dir) {
            if ($dir != 'factory') {
                foreach (Storage::disk('testing')->files($dir) as $file) {
                    $path = explode('/', $file);
                    array_pop($path);
                    $path = implode('/', $path);
                    Storage::putFile($path, new File('storage/app/testing/' . $file), 'public');
                }
                Storage::disk('testing')->deleteDirectory($dir);
            }
        }
        $this->call('storage:link', ['--force' => true]);
        $this->call('key:generate', ['--force' => true]);
        $this->call('jwt:secret', ['--force' => true]);
        $this->call('migrate', ['--seed' => true, '--force' => true]);
        $this->call('optimize');
        if (config('filesystems.default') == 'public') shell_exec('chmod 777 -R ./storage/app/public');
        shell_exec('npm run dev');
    }
}
