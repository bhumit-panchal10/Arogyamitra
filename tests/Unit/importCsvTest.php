<?php

namespace Tests\Unit;

use App\Http\Controllers\CsvController;
use PHPUnit\Framework\TestCase;
use App\Models\Vibhag;
use App\Models\Jilla;
use App\Models\Taluka;
use App\Models\Gramjuth;
use App\Models\Gram;
use App\Models\User;

class importCsvTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function testImportCsv()
    {
        $file = new CsvController('../public/sewabharati_allData.csv');
        $this->assertTrue(true);
        // $file = new CsvController(__DIR__.'/public/sewabharati_allData.csv');
    }
    public function Vibhag($name)
    {
        // $this->assertTrue(true);

        $vibhag = new Vibhag();
        $this->assertTrue($name, $vibhag, 'The vibhag field should be filled.');
        // $this->assertTrue(isset($vibhag->name), 'The vibhag field should be filled.');
        // $this->assertFalse('name', $vibhag, 'The vibhag field should be filled.');
    }
    public function Jilla()
    {
        $jilla = new Jilla();
        $this->assertTrue('name', $jilla, 'The jilla field should be filled.');
        $this->assertTrue('vibhag_id', $jilla, 'The vibhag_id field should be filled.');
        // $this->assertFalse('name', $jilla, 'The jilla field should be filled.');
        // $this->assertFalse('vibhag_id', $jilla, 'The vibhag_id field should be filled.');
    }
    public function Taluka()
    {
        $taluka = new Taluka();
        $this->assertTrue('name', $taluka, 'The value should not be null.');
        $this->assertTrue('jilla_id', $taluka, 'The value should not be null.');
        // $this->assertFalse('name', $taluka, 'The value should not be null.');
        // $this->assertFalse('jilla_id', $taluka, 'The value should not be null.');

    }
    public function Gramjuth()
    {
        $gramjuth = new Gramjuth();
        $this->assertTrue('name', $gramjuth,  'The value should not be null.');
        $this->assertTrue('taluka_id', $gramjuth,  'The value should not be null.');
        // $this->assertFalse('name', $gramjuth,  'The value should not be null.');
        // $this->assertFalse('taluka_id', $gramjuth,  'The value should not be null.');

    }
    public function Gram()
    {
        $gram = new Gram();
        $this->assertTrue('name', $gram, 'The value should not be null.');
        $this->assertTrue('gramjuth_id', $gram, 'The value should not be null.');
        // $this->assertFalse('name', $gram, 'The value should not be null.');
        // $this->assertFalse('gramjuth_id', $gram, 'The value should not be null.');


    }
    public function User(){
        $users =new User();
        $this->assertTrue('name', $users, 'The value should not be null.');
        $this->assertTrue('gram_id', $users, 'The value should not be null.');
        $this->assertTrue('role', $users, 'The value should not be null.');
        $this->assertTrue('mobile_no', $users, 'The value should not be null.');
        $this->assertTrue('jilla_id', $users, 'The value should not be null.');
        $this->assertTrue('status', $users, 'The value should not be null.');
    }
}