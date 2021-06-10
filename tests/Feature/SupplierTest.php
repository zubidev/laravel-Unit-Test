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
        $hours = 0;

        $supplierList = json_decode($response->getOriginalContent());
        $suppliers = $supplierList->data->suppliers;
        $daysArray = array('mon','tue','wed','thu','fri','sat','sun');
        if(!empty($suppliers)){
            foreach ($suppliers as $supplier){
                $arrayKeys = array_keys((array)$supplier);
                foreach ($daysArray as $day){
                    if(in_array($day, $arrayKeys)){
                        $splitByDash = explode("-",$supplier->$day);
                        if(count($splitByDash) >= 3){
                            $splitByComma = explode(",", $splitByDash[1]);
                            $time1 = new \DateTime((explode(": ",$splitByDash[0]))[1]);
                            $time2 = new \DateTime($splitByComma[0]);
                            $interval = $time1->diff($time2);
                            $hours = $hours  + $interval->h;

                            $time1 = new \DateTime($splitByComma[1]);
                            $time2 = new \DateTime($splitByDash[2]);
                            $interval = $time1->diff($time2);
                            $hours = $hours  + $interval->h;
                        }else{
                            $time1 = new \DateTime((explode(": ",$splitByDash[0]))[1]);
                            $time2 = new \DateTime($splitByDash[1]);
                            $interval = $time1->diff($time2);
                            $hours = $hours  + $interval->h;
                        }
                    }
                }

            }
        }
        $response->assertStatus(200);
        $this->assertEquals(136, $hours,
            "Our suppliers are working X hours per week in total. Please, find out how much they work..");
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
        $response->assertStatus(422);
    }
}
