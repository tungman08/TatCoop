<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use PaymentCalculator;

class PaymentCalculateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:calculate {date? : The date that uses for calculate loan payment}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate loan payment of members';

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
        $result = PaymentCalculator::calculate($this->argument('date'));

        $this->info(($result));
    }
}
