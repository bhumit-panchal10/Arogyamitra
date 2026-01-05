<?php

namespace Database\Seeders;

use App\Models\Gramjuth;
use Illuminate\Database\Seeder;

class GramjuthSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gramjuth = [
            [
                'name' => 'સેલવાસ',
                'taluka_id' => 5,
                'status' => '1'
            ],
            [
                'name' => 'રાંધા',
                'taluka_id' => 5,
                'status' => '1'
            ],
            [
                'name' => 'રાજપુરી જંગલ',
                'taluka_id' => 6,
                'status' => '1'
            ],
            [
                'name' => 'કાંગવી',
                'taluka_id' => 6,
                'status' => '1'
            ],
            [
                'name' => 'ગડી',
                'taluka_id' => 6,
                'status' => '1'
            ],
            [
                'name' => 'તામછડી',
                'taluka_id' => 6,
                'status' => '1'
            ]
        ];

        foreach ($gramjuth as $val) {
            Gramjuth::create($val);
        }
    }
}
