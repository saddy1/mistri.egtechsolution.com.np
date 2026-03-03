<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::updateOrCreate(
            ['email' => 'sadanand@ioepc.edu.np'], // unique field
            [
                'name' => 'Sadanand Paneru',
                'password' => bcrypt('S@ddy9843521965@'), // Hash the password
                'contact' => '9843521965',
            ]
        );
        Admin::updateOrCreate(
            ['email' => 'exam@ioepc.edu.np'], // unique field
            [
                'name' => 'Exam Admin',
                'password' => bcrypt('Exam@ioepc@123'), // Hash the password
                'contact' => '9843521965',
            ]
        );
    }
}
