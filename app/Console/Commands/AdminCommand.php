<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Administrator;
use App\Role;
use History;

class AdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create {--super : Create super administrator}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create super administrator for tatcoop.com';

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
        if ($this->option('super')) {
            $exist = Administrator::where('email', 'admin@tatcoop.com')->first();

            if ($exist == null) {
                $password = $this->secret('password');
                $confirmed = $this->secret('comfirm password');

                if ($password == $confirmed) {
                    if (strlen($password) >= 6) {
                        $admin = ['name' => 'Secret', 'lastname' => 'Administrator', 'email' => 'admin@tatcoop.com', 'password' => $password, 'password_changed' => true];
                        $obj = new Administrator($admin);

                        $role = Role::find(1);
                        $role->admins()->save($obj);

                        History::addAdminHistory($obj->id, 'สร้างบัญชีผู้ดูแลระบบ');
                        $this->info('Super administrator was created.');
                    }
                    else {
                        return $this->error('ERROR: The password must be at least 6 characters.');
                    }
                }
                else {
                    return $this->error('ERROR: The password confirmation does not match.');
                }
            }
            else {
                return $this->error('ERROR: Super administrator already exist.');
            }
        }
        else {
            return $this->error('ERROR: Require option argument.');
        }
    }
}
