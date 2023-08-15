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
     */
    public function handle()
    {
       dump(cache()->get('first_page_product_aggregate_data_without_filter_by_category_id:1'));
    }
}
