<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FilesystemException;
use League\Flysystem\MountManager;

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
     * @expectedException FilesystemException
     * @return void
     */
    public function handle(): void
    {
        shell_exec('curl https://disk.yandex.ru/d/UXUnbmQv4Zndug/factory.zip -o storage/app/testing/factory.zip
        && unzip storage/app/testing/factory -d storage/app/testing
        && rm storage/app/testing/factory.zip');

        $mountManager = new MountManager([
            'testing' => Storage::disk('testing')->getDriver(),
            'default' => Storage::getDriver(),
        ]);
        foreach (Storage::disk('testing')->directories() as $dir) {
            if ($dir != 'factory') {
                foreach (Storage::disk('testing')->files($dir) as $file) {
                    $mountManager->move('testing://' . $file, 'default://' . $file);
                }
            }
        }
        $this->call('storage:link', ['--force' => true]);
        $this->call('key:generate', ['--force' => true]);
        $this->call('jwt:secret', ['--force' => true]);
        $this->call('migrate', ['--seed' => true, '--force' => true]);
        config()->set('database.connections.mysql.database', 'shop_testing');
        $this->call('migrate', ['--force' => true]);
        config()->set('database.connections.mysql.database', 'shop');

        if (Storage::getDefaultDriver() == 'public') shell_exec('chmod 777 -R ./storage/app/public');
        $this->call('optimize:clear');
        shell_exec('npm run dev');
    }
}
