<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use ShareholdingCalculator;

class ShareholdingStoreCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shareholding:store';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store shareholding of members';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $result = ShareholdingCalculator::store();

        $this->info(($result));
    }
}
