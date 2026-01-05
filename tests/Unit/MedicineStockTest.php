<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\MedicineStock;


class MedicineStockTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_filter_jilla(): void
    {
        $qty = 2;
        $medicineStock = MedicineStock::get();

        foreach ($medicineStock as $key => $value) {
            $this->assertEquals($qty, 2);
        }
    }
}

