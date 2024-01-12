<?php

namespace Database\Seeders;

use App\Models\UserModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = UserModel::create([
            'user_roles_id' => '',
            'name' => 'Nadya Auradiva',
            'email' => 'areraraas@gmail.com',
            'password' => Hash::make('tartaglia'),
            'updated_security' => date('Y-m-d H:i:s')
        ]);
    }
}
