<?php

namespace Database\Seeders;

use App\Models\Jilla;
use Illuminate\Database\Seeder;

class JillaSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jilla = [
            [
                'name' => 'વડોદરા',
                'vibhag_id' => 1,
                'status' => '1'
            ],
            [
                'name' => 'નવસારી',
                'vibhag_id' => 1,
                'status' => '1'
            ],
            [
                'name' => 'ભરૂચ',
                'vibhag_id' => 1,
                'status' => '1'
            ],
            [
                'name' => 'નર્મદા',
                'vibhag_id' => 1,
                'status' => '1'
            ],
            [
                'name' => 'આણંદ',
                'vibhag_id' => 2,
                'status' => '1'
            ],
            [
                'name' => 'ખેડા',
                'vibhag_id' => 2,
                'status' => '1'
            ]
        ];

        foreach ($jilla as $val) {
            Jilla::create($val);
        }
    }
}
