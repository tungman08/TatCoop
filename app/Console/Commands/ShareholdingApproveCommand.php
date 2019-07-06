<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use ShareholdingCalculator;

class ShareholdingApproveCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shareholding:approve {date? : The date that uses for approve shareholding}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Approve shareholding of members';

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
        $result = ShareholdingCalculator::approve($this->argument('date'));

        $this->info(($result));
    }
}
