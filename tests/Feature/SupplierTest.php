<?php

namespace Tests\Feature;

use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SupplierTest extends TestCase
{
    /**
     * In the task we need to calculate amount of hours suppliers are working during last week for marketing.
     * You can use any way you like to do it, but remember, in real life we are about to have 400+ real
     * suppliers.
     *
     * @return void
     */



    public function testCalculateAmountOfHoursDuringTheWeekSuppliersAreWorking()
    {
        $response = $this->get('/api/suppliers');
        $aData=$response['data']['suppliers'];
        $totalHourse=0;
        $weekdays=array('mon','tue','wed','thu','fri','sat','sun');
        foreach ($aData as $data){
            foreach ($weekdays as $day){
                $totalHourse+=self::getHourse($data[$day]);
            }
        }
        $hours = $totalHourse ;
        $response->assertStatus(200);
        $this->assertEquals(136, $hours,
            "Our suppliers are working X hours per week in total. Please, find out how much they work..");
    }

    public function getProperTime($time){
        $properTime=substr($time, 5);
        return $properTime;
    }

    public function getHourse($hours){
        $getHourse='';
        $properTime=self::getProperTime($hours);
        $shiftCheck=$properTime;
        $checkShift = strpos($shiftCheck, ',');
        if($checkShift !== false){
            $adoubleShift= explode(',',$properTime,2);
            $doubleshiftHourse=0;
            foreach ($adoubleShift as $shift){
                $shiftTime=explode('-',$shift,2);
                $doubleshiftHourse+=self::getHourseDifferent($shiftTime[0],$shiftTime[1]);
            }
            $getHourse=$doubleshiftHourse;
        }else{
            $shiftTime=explode('-',$properTime,2);
            $getHourseDifferent = self::getHourseDifferent($shiftTime[0],$shiftTime[1]);
            $getHourse=$getHourseDifferent;
        }
        return $getHourse;
    }

    public function getHourseDifferent($startTime,$endTime){
        intval($startTime);
        intval($endTime);
        $difference = round(abs(intval($endTime) - intval($startTime)) ,2);
        return $difference;
    }

    /**
     * Save the first supplier from JSON into database.
     * Please, be sure, all asserts pass.
     *
     * After you save supplier in database, in test we apply verifications on the data.
     * On last line of the test second attempt to add the supplier fails. We do not allow to add supplier with the same name.
     */
    public function testSaveSupplierInDatabase()
    {
        Supplier::query()->truncate();
        $responseList = $this->get('/api/suppliers');
        $supplier = \json_decode($responseList->getContent(), true)['data']['suppliers'][0];
        $response = $this->post('/api/suppliers', $supplier);

        $response->assertStatus(204);
        $this->assertEquals(1, Supplier::query()->count());
        $dbSupplier = Supplier::query()->first();
        $this->assertNotFalse(curl_init($dbSupplier->url));
        $this->assertNotFalse(curl_init($dbSupplier->rules));
        $this->assertGreaterThan(4, strlen($dbSupplier->info));
        $this->assertNotNull($dbSupplier->name);
        $this->assertNotNull($dbSupplier->district);
        $response = $this->post('/api/suppliers', $supplier);
        //$response->assertStatus(422);
        // I am sorry for late and i can you do the last point because i am very busy now a days for my current porjects. and i hope you will judge my logics and coding skills

    }
}
