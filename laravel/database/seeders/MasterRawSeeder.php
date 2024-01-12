<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterRawSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sqlFilePath = resource_path('sql/MasterRawSeeder.sql');

        if (file_exists($sqlFilePath)) {
            $sqlContent = file_get_contents($sqlFilePath);

            DB::unprepared($sqlContent);

            $this->command->info('MasterRawSeeder.sql has been executed.');
        } else {
            $this->command->error('MasterRawSeeder.sql file not found.');
        }
    }
}
