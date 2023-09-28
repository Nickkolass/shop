<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PhpStanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stan {directories?*} {--L|level=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'php stan analyze';

    /**
     * Execute the console command.
     *
     */
    public function handle(): void
    {
        $directories = empty($this->argument('directories')) ? '' : implode(' ', $this->argument('directories'));
        $level = $this->option('level') ?? '';
        if (!empty($level)) $level = '-l ' . $level;
        echo(shell_exec("vendor/bin/phpstan analyse $directories $level"));
    }
}
