<?php

namespace App\Components\Transport\Protokol\Amqp;

use Illuminate\Console\Command;

class AmqpConsumeCommand extends Command
{
    protected $signature = 'amqp:consume {queue}';

    protected $description = 'amqp consume';

    public function __construct(private readonly AmqpClientInterface $amqp)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        echo " [*] Waiting for messages. To exit press CTRL+C\n";
        $this->amqp->consume($this->argument('queue'));
    }
}
