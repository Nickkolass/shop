<?php

namespace App\Components\Transport;

use App\Components\Transport\Protokol\Amqp\AmqpClientInterface;
use App\Components\Transport\Protokol\Amqp\AmqpConsumeCommand;
use App\Components\Transport\Protokol\Http\HttpClientInterface;
use Illuminate\Support\ServiceProvider;

class TransportServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->registerTransport();
        $this->registerConsumers();
    }

    public function boot(): void
    {
        $this->bootTransport();
        $this->bootConsumers();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [
            AmqpClientInterface::class,
            HttpClientInterface::class,
        ];
    }

    private function registerTransport(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/Protokol/config.php', 'transport');

        $this->app->bind(AmqpClientInterface::class, function () {
            $default_amqp = config('transport.protocols.amqp.default');
            return $this->app->make(config("transport.protocols.amqp.clients.$default_amqp.bind"));
        });
        $this->app->bind(HttpClientInterface::class, function () {
            $default_http = config('transport.protocols.http.default');
            return $this->app->make(config("transport.protocols.http.clients.$default_http.bind"));
        });
    }

    private function registerConsumers(): void
    {
        foreach ((array)glob(__DIR__ . '/Consumer/*/config.php') as $config) {
            $dir_key = explode('/', (string)$config);
            array_pop($dir_key);
            $dir_key = array_pop($dir_key);
            $config_key = 'consumers.' . strtolower($dir_key);
            $this->mergeConfigFrom((string)$config, $config_key);

            $interface = "App\Components\Transport\Consumer\\$dir_key\\$dir_key" . 'TransportInterface';
            $this->app->bind($interface, function () use ($config_key) {
                $default_service_transport = config("$config_key.transport");
                return $this->app->make(config("$config_key.options.$default_service_transport.bind"));
            });
        }
    }

    private function bootTransport(): void
    {
        $this->commands(AmqpConsumeCommand::class);
    }

    private function bootConsumers(): void
    {
    }
}
