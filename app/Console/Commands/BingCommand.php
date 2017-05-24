<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Bing;
use Diamond;
use App\Background;

class BingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bing:download';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Download Bing's wallpaper for authentication page background";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $result = Bing::download();
        $message = ($result !== false) ?
            ($result > 0) ? 
                "$result photos downloaded" : 
                "Nothing" : 
                "Something went wrong!";
                
        $this->info($message);
    }
}
