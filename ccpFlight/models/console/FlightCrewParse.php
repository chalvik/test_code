<?php
namespace common\modules\ccpFlight\models\console;

use common\modules\ccpFlight\models\Flight;
use common\modules\ccpFlight\models\FlightLegCrew;
use yii\db\Query;

/**
 * Class FlightCrewParse
 * @package common\modules\ccpFlight\models\console
 */
class FlightCrewParse extends FlightLegCrew
{

    /**
     * @param Flight $flight
     * @param array $data
     * @return bool
     */
    public static function saveCrew($flight, $data, &$is_new)
    {
        $is_new = 0;
        $roster_id = trim($data['ROSTER_ID']);

        $model = self::find()
            ->where([
                'flight_id' => $flight->id,
                'roster_id' => $roster_id
            ])
            ->one();

        if (!$model) {
            $model = new self();
            $is_new = 1;
        }

        $model->flight_id = $flight->id;
        $model->roster_id = $roster_id;
        $model->pos_code = $data['POS_CODE'];
        $model->id_dhd = $data['ID_DHD'];
        $model->pos_leg1 = $data['POS_LEG_1'];
        $model->last_updated_at = gmdate("Y-m-d H:i:s");
        
        return $model->save();
    }
    
    /**
     * Get  Crew  array data records for $where  from Oracle (Aims)
     *
     * @param array $where
     * @param integer $limit
     * @param integer $offset
     * @return array
     */

    public static function getCrewFromOracle($where, $limit = 25, $offset = 0)
    {
        $std = null;

        if (isset($where['std'])) {
            $std = $where['std'];
            unset($where['std']);
        }


        $query =  (new Query())
            ->from(['t' => 'S7_IT.CCP#V_ROSTER'])
            ->select([
                'DAY',
                'FLT',
                'DEP',
                'CARRIER',
                'LEGCD',
                'STD_DATE' => "to_char(STD_DATE, 'yyyy-mm-dd hh24:mi:ss')",
                'ROSTER_ID',
                'ROSTER_NAME',
                'POS_CODE',
                'ID_DHD',
                'POS_LEG_1',
                'ADD_ROSTER' => "to_char(ADD_ROSTER, 'yyyy-mm-dd hh24:mi:ss')",
            ])
            ->where($where);

        if ($std) {
            $query = $query->andWhere("TO_CHAR(STD_DATE,'yyyy-mm-dd hh24:mi:ss') = '".$std."'");
        }

        $query->limit($limit)
            ->offset($offset);
        return $query->all(\Yii::$app->dbAmis);
    }
}
