<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use PaymentCalculator;

class PaymentApproveCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:approve {date? : The date that uses for approve loan payment}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Approve loan payment of members';

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
        $result = PaymentCalculator::approve($this->argument('date'));

        $this->info(($result));
    }
}
