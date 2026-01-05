<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'name' => 'Admin',
                'email' => 'hello@hardyinfotech.com',
                'username' => '',
                'jilla_id' => null,
                'gram_id' => null,
                'mobile_no' => '947736739',
                'address' => 'Ahmedabad',
                'status' => 'Active',
                'password' => '$2y$10$QgpU1BErV5.G.WoCXRNfau/GCxKjxKGRiKA5aEZQ0MeRHsdWPxUG2',//Admin@123
                'role' => '1',
                'remember_token' => '',
            ]
        ];

        foreach ($items as $item) {
            User::create($item);
        }
    }
}
