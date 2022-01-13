<?php
namespace common\modules\ccpFlight\models\console;

use common\modules\ccpFlight\models\FlightLeg;

/**
 * Class FlightUpdate для получения плеч рейсов, которые нужно обновить (крон)
 * @package common\components
 */
class FlightLegUpdate extends FlightLeg
{

    /*
     * period time before for update flight
     */
    const PERIOD = 120 * 60;

    /**
     * @param string $lastUpdatedField
     * @return FlightLeg[]
     */
    public static function getFlightsForUpdating($lastUpdatedField = 'last_updated_at')
    {
        //              5min                    15 min
//        *-----------------------*---------------------- *
//        CURRENT                before20                    END
//              
//              
//              
//        *---------------------- * -------------------------*
//        STD                    TKOF                       TDOWN

        $current_second = time();
        /*
         * curent time  for update flight
         */
        $current = gmdate("Y-m-d H:i:s", $current_second);

        $current_before5 = date("Y-m-d H:i:s", $current_second - (0 * 60));
        $current_before15 = date("Y-m-d H:i:s", $current_second - (15 * 60));
        $current_before120 = gmdate("Y-m-d H:i:s", $current_second - (60 * 2 * 60));
        $current_before_day = gmdate("Y-m-d H:i:s", $current_second - (24 * 60 * 60)); // day 24 hours
        $current_after20 = gmdate("Y-m-d H:i:s", $current_second + (20 * 60));

        /*
         * end period time  for update flight
         */
        $end_second = $current_second + static::PERIOD;
        $end = gmdate("Y-m-d H:i:s", $end_second);

//        $where = ['between','etd',$start,$end];

        $query = self::find();
        /**
         * beetween 120 and 20 minute before  departure flight once at 15 minute
         */

        $query->where(['and', "etd >='$current_after20'", "etd <='$end'", "$lastUpdatedField <= '$current_before15'"]);
        /**
         * beetween 20 minut beetwen tkof from departure flight once at 5 minute
         */
        $query->orWhere(['and', "etd between '$current' AND '$current_after20'", "$lastUpdatedField <= '$current_before5'"]);
        /**
         * Aircraft do not defarture
         */
        $query->orWhere(['and', "tkof is  null", "etd between  '$current_before120' AND '$current'", "$lastUpdatedField <= '$current_before5'"]);

        /**
         * Aircraft do not arrive
         */
        $query->orWhere(['and', "tdown is  null", "etd between  '$current_before_day' AND '$current'", "$lastUpdatedField <= '$current_before5'"]);

//        $query->andWhere(['!=','adate',0]);
//        $query->andWhere(['!=','aroute',0]);

        //$query->andWhere(['!=', 'canceled', 1]);

        $query->with('flight');

        return $query->all();
    }

    /**
     * Запрос на актуализацию данных будет предварительно осуществляться за 3 часа, за 1,5 часа и за 40 минут до планового времени вылета
     * и за 10 минут до расчетного времени вылета
     */
    public static function getFlightLegsForUpdateAircraftRestrictions()
    {
        $currentSeconds = time();
        /*
         * curent time  for update flight
         */
        $current = gmdate("Y-m-d H:i:s", $currentSeconds);
        $currentBefore10 = gmdate("Y-m-d H:i:s", $currentSeconds - (10 * 60));
        $currentAfter3Hours = gmdate("Y-m-d H:i:s", $currentSeconds + (3 * 60 * 60));
        $currentAfter90M = gmdate("Y-m-d H:i:s", $currentSeconds + (90 * 60));
        $currentAfter40M = gmdate("Y-m-d H:i:s", $currentSeconds + (40 * 60));
        $currentAfter10M = gmdate("Y-m-d H:i:s", $currentSeconds + (10 * 60));

        $query = self::find()
            ->where(['or', "std <='$currentAfter3Hours'", "std <='$currentAfter90M'", "std <='$currentAfter40M'"])
            ->andWhere(['>=', "std", $current])
            ->orWhere(['and', "etd <='$currentAfter10M'", "etd >='$current'"])
            ->andWhere(["<=", "last_restriction_updated_at", $currentBefore10]);

        $query->with('flight');

        return $query->all();
    }

}
