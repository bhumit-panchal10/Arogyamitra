<?php

namespace Database\Seeders;

use App\Models\Vibhag;
use Illuminate\Database\Seeder;

class VibhagSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vibhags = [
            [
                'name' => 'વડોદરા',
                'status' => '1'
            ],
            [
                'name' => 'નડિયાદ',
                'status' => '1'
            ],
            [
                'name' => 'ગાંધીનગર',
                'status' => '1'
            ],
            [
                'name' => 'મહેસાણા',
                'status' => '1'
            ],
            [
                'name' => 'બનાસકાંઠા',
                'status' => '1'
            ],
            [
                'name' => 'નવસારી',
                'status' => '1'
            ]
        ];

        foreach ($vibhags as $vibhag) {
            Vibhag::create($vibhag);
        }
    }
}
