<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [[
            'email' => 'admin@admin.sk',
            'password' => bcrypt('admin123'),
            'is_admin' => true,
            'first_name' => 'Jozef',
            'last_name' => 'Bugal',
            'username' => 'admin'
        ],[
            'email' => 'jozko@bugal.sk',
            'password' => bcrypt('helloworld'),
            'first_name' => 'Nicolas',
            'last_name' => 'Parker',
            'username' => 'helloworld'
        ]
        ];

        User::create($users[0]);
        User::create($users[1]);
    }
}