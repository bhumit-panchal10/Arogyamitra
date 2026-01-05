<?php

namespace Tests\Feature;

use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Http\Controllers\CsvController;
use App\Models\User;
use App\Models\Vibhag;
use App\Models\Jilla;
use App\Models\Taluka;
use App\Models\Gramjuth;
use App\Models\Gram;
use Illuminate\Support\Facades\Validator;

class importCsvTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    // public function test_example(): void
    // {
    //     $this->assertTrue(false);
    //     $response = $this->get('/import');

    //     $response->assertStatus(200);
    // }

    public function test_vibhag()
    {

        $vibhag = Vibhag::make([
            'name' => 'બનાસકાંઠા',
            'status' => '1',
        ]);
        $vibhag1 = Vibhag::make([
            'name' => 'મહેસાણા',
            'status' => '1',
        ]);
        $this->assertTrue($vibhag->name != $vibhag1->name);
        // $response->assertRedirect(RouteServiceProvider::HOME);
    }
    public function test_jilla()
    {
        $jilla = Jilla::make([
            'name' => 'થરાદ',
            'vibhag_id' => 'બનાસકાંઠા',
            'status' => '1',
        ]);
        $jilla1 = Jilla::make([
            'name' => 'પાલનપુર',
            'vibhag_id' => 'બનાસકાંઠા',
            'status' => '1',
        ]);
        $this->assertTrue($jilla->name != $jilla1->name);
    }
    //     $jillaName1 = 'થરાદ';
    //     $vibhagId1 = 'બનાસકાંઠા';
    //     $jilla1 = Jilla::make([
    //         'name' => $jillaName1,
    //         'vibhag_id' => $vibhagId1,
    //         'status' => '1',
    //     ]);

    //     $jillaName2 = 'પાલનપુર';
    //     $vibhagId2 = 'બનાસકાંઠા';
    //     $jilla2 = Jilla::make([
    //         'name' => $jillaName2,
    //         'vibhag_id' => $vibhagId2,
    //         'status' => '1',
    //     ]);

    //     $jillaId1 = $this->test_jilla($jillaName1, $vibhagId1);
    //     $jillaId2 = $this->test_jilla($jillaName2, $vibhagId2);

    //     $this->assertTrue($jilla1->name != $jilla2->name);
    //     $this->assertEquals($jilla1->id, $jillaId1); // Assuming Jilla model has an 'id' field
    //     $this->assertEquals($jilla2->id, $jillaId2);
    // }
    public function test_taluka()
    {

        $taluka = Taluka::make([
            'name' => 'દેવગઢ બારિયા	',
            'jilla_id' => 'દાહોદ',
            'status' => '1',
        ]);
        $taluka1 = Taluka::make([
            'name' => 'વાવ',
            'jilla_id' => 'દાહોદ',
            'status' => '1',
        ]);
        $this->assertTrue($taluka->name != $taluka1->name);
    }
    public function test_gramjuth()
    {

        $gramjuth = Gramjuth::make([
            'name' => 'દાહોદ',
            'taluka_id' => 'પોશીના',
            'status' => '1',
        ]);
        $gramjuth1 = Gramjuth::make([
            'name' => 'મુનજીના મુવાડા',
            'taluka_id' => 'સાઠંબા',
            'status' => '1',
        ]);
        $this->assertTrue($gramjuth->name != $gramjuth1->name);
    }
    public function test_gram()
    {

        $gram = Gram::make([
            'name' => 'ગોલપ (નેસડા)',
            'gramjuth_id' => 'પાડણ',
            'status' => '1',
        ]);
        $gram1 = Taluka::make([
            'name' => 'તખતપુરા (જો)',
            'gramjuth_id' => 'માવસરી',
            'status' => '1',
        ]);
        $this->assertTrue($gram->name != $gram1->name);
    }
    public function test_user()
    {
        $user = User::make([
            'name' => 'ઈશ્વરભાઇ પ્રધાનજી વ્યાસ',
            'password' => '$2y$10$QgpU1BErV5.G.WoCXRNfau/GCxKjxKGRiKA5aEZQ0MeRHsdWPxUG2',
            'status' => 'Active',
            'role' => '1',
            'mobile_no' => '9787665546',
        ]);
        $user1 = User::make([
            'name' => '	ઇશ્વરગીરી મોહનગીરી ગોસ્વામી',
            'password' => '$2y$10$QgpU1BErV5.G.WoCXRNfau/GCxKjxKGRiKA5aEZQ0MeRHsdWPxUG2',
            'status' => 'Active',
            'role' => '1',
            'mobile_no' => '9787665566',
        ]);
        $rules = [
            'name' => 'required|string|max:255',
            'mobile_no' => 'required|numeric|digits:10',

        ];
        $validate = Validator::make($user->toArray(), $rules);
        $this->assertFalse($validate->fails(), $validate->errors());

        $validate1 =Validator::make($user1->toArray(), $rules);
        $this->assertFalse($validate1->fails(), $validate1->errors());

        $this->assertTrue($user->name != $user1->name && $user->mobile_no != $user1->mobile_no);
    }
}
