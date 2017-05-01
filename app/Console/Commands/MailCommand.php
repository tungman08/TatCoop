<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Mail;

class MailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:send {address}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send e-mails for test smtp';

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
        $address = $this->argument('address');

        if (filter_var($address, FILTER_VALIDATE_EMAIL)) {
            Mail::raw('Success!!!', function ($message) use ($address) {
                $message->to($address)
                    ->subject('ทดสอบระบบ e-mail ของ www.tatcoop.com');
            });

            $this->line('Sent e-mail to ' . $address);
        }
        else {
            $this->error('Invalid email format');
        }
    }
}
