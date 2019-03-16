<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DividendCalculator;

class DividendCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dividend:calculate {year? : The year that uses for calculate dividend} {--force : Force the operation to run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate dividend of members';

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
        $result = DividendCalculator::calculate($this->argument('year'), $this->option('force'));

        $this->info($result);
    }
}
