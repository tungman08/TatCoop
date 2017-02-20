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
        $this->call(HistoryTypesTableSeeder::class);
        $this->call(AdministratorsTableSeeder::class);
        $this->call(EmployeeTypesTableSeeder::class);
        $this->call(PostcodesTableSeeder::class);
        $this->call(ProvincesTableSeeder::class);
        $this->call(ProfilesTableSeeder::class);
        $this->call(MembersTableSeeder::class);
        $this->call(EmployeesTableSeeder::class);
        $this->call(ShareholdingTypesTableSeeder::class);
        $this->call(ShareholdingsTableSeeder::class);
        $this->call(DocumentTypesTableSeeder::class);
        $this->call(DocumentsTableSeeder::class);
        $this->call(AttachmentsTableSeeder::class);
        $this->call(CarouselsTableSeeder::class);
    }
}
