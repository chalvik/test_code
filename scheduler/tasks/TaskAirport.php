<?php

namespace common\modules\scheduler\tasks;

use common\modules\scheduler\components\AbstractTask;
use common\modules\ccpAirport\models\console\AirportParse;

/**
 * Class TaskAirport
 * @package common\modules\scheduler\tasks
 */
class TaskAirport extends AbstractTask
{

    public function run()
    {
        $output = [
            'status' => false,
            'message' => ''
        ];
        
        $psize = 200;
        $count = AirportParse::getCountAirportFromOracle();
        $page = 0;
        $pages = ceil($count/$psize);
        
        for ($page = 0; $page<=$pages; $page++) {
            $data_employees = AirportParse::getAirportFromOracle($psize, $page*$psize);
            $transaction = AirportParse::getDb()->beginTransaction();
            
            try {
                foreach ($data_employees as $data) {
                    $airport = AirportParse::findOne(['iata'=>$data['IATA_CODE']]);

                    if (!$airport) {
                        $airport = new AirportParse();
                    }
                    
                    $airport->iata              =   $data['IATA_CODE'];
                    $airport->icao              =   $data['ICAO_CODE'];
                    $airport->city              =   $data['CITY_NAME'];
                    $airport->country           =   $data['COUNTRY_NAME'];
                    $airport->longitude         =   $data['LONGITUDE'];
                        $airport->latitude          =   $data['LATITUDE'];
                    $airport->utc_aims          =   $data['UTC'];
                    $airport->last_updated_at   =   gmdate("Y-m-d H:i:s");
                    
                    if (! $airport->save()) {
                        throw new \Exception(json_encode($airport->errors), 500);
                    }
                }
                
                $task->saveLastActivity();
                
                $transaction->commit();
            } catch (\Exception $e) {
                $error['message'] =  $e->getMessage();
                $error['status'] =  true;
                $transaction->rollBack();
            }
        }
        
        return $output;
    }
}
