<?php

namespace Database\Seeders;

use App\Models\Gram;
use Illuminate\Database\Seeder;

class GramSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gram = [
            [
                'name' => 'ખુટલી',
                'gramjuth_id' => 5,
                'status' => '1'
            ],
            [
                'name' => 'રખોલી',
                'gramjuth_id' => 5,
                'status' => '1'
            ],
            [
                'name' => 'વલસાડ',
                'gramjuth_id' => 6,
                'status' => '1'
            ],
            [
                'name' => 'રાબડા',
                'gramjuth_id' => 6,
                'status' => '1'
            ]
        ];

        foreach ($gram as $val) {
            Gram::create($val);
        }
    }
}
