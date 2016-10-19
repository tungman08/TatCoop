<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PrefixsTableSeeder::class);
        $this->call(ThemesTableSeeder::class);
        $this->call(EmployeeTypesTableSeeder::class);
        $this->call(PostcodesTableSeeder::class);
        $this->call(ProvincesTableSeeder::class);
        $this->call(AdministratorsTableSeeder::class);
        $this->call(ProfilesTableSeeder::class);
        $this->call(MembersTableSeeder::class);
        $this->call(EmployeesTableSeeder::class);
        $this->call(ShareholdingTypesTableSeeder::class);
        $this->call(ShareholdingsTableSeeder::class);
    }
}
