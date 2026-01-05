<?php

namespace Database\Seeders;

use App\Models\Taluka;
use Illuminate\Database\Seeder;

class TalukaSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $taluka = [
            [
                'name' => 'સેલવાસ',
                'jilla_id' => 1,
                'status' => '1'
            ],
            [
                'name' => 'ધરમપુર',
                'jilla_id' => 1,
                'status' => '1'
            ],
            [
                'name' => 'કપરાડા',
                'jilla_id' => 1,
                'status' => '1'
            ],
            [
                'name' => 'વલસાડ',
                'jilla_id' => 1,
                'status' => '1'
            ],
            [
                'name' => 'વાંસદા',
                'jilla_id' => 1,
                'status' => '1'
            ],
            [
                'name' => 'ખેડા',
                'jilla_id' => 2,
                'status' => '1'
            ]
        ];

        foreach ($taluka as $val) {
            Taluka::create($val);
        }
    }
}
