<?php

namespace App\Console\Commands;

use App\Components\Transport\Protokol\Amqp\AmqpClientInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\MountManager;

class InitProjectInsideDockerCommand extends Command
{

    protected $signature = 'init';

    protected $description = 'init project inside docker container';

    public function __construct(private readonly AmqpClientInterface $amqp)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->amqp->init(
            $this->amqp::QUEUE,
            $this->amqp::ROUTING_KEY,
            $this->amqp::EXCHANGE,
        );
        $this->amqp->initConsumers();

        $this->keys()
            ->images()
            ->migrate()
            ->completion();
    }

    private function completion(): self
    {
        shell_exec('chmod 777 -R ./storage/app/public');
        shell_exec('chmod 772 -R ./storage/logs');
        $this->call('optimize');
        shell_exec('npm run dev');
        return $this;
    }

    private function keys(): self
    {
        $this->call('storage:link', ['--force' => true]);
        $this->call('key:generate', ['--force' => true]);
        $this->call('jwt:secret', ['--force' => true]);
        return $this;
    }

    private function migrate(): self
    {
        $this->call('migrate', ['--seed' => true, '--force' => true]);
        config()->set('database.connections.mysql.database', 'shop_testing');
        $this->call('migrate', ['--force' => true]);
        config()->set('database.connections.mysql.database', 'shop');
        return $this;
    }

    private function images(): self
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
        return $this;
    }
}
