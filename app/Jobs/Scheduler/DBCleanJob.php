<?php

namespace App\Jobs\Scheduler;

use App\Models\PropertyValue;
use Database\Seeders\Components\SeederStorageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

class DBCleanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        Artisan::call('telescope:prune', ['--env' => 'local']);
        PropertyValue::doesntHave('products')->delete();
        (new SeederStorageService)->caching();
    }
}

