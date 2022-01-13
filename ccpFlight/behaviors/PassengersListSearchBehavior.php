<?php
namespace common\modules\ccpFlight\behaviors;

use yii\base\Behavior;
use common\modules\EdbPassenger\models\EdbPassenger;
use common\modules\ccpFlight\models\Flight;

/**
 * Поведение для поиска пассажиров на рейс с литерой A.
 * Т.е. для списков пассажиров рейс которых был перенесён в течение ближайших суток.
 * 
 * !Notes: Для более точного поиска возможно нужно учесть данные с AIMS Oracle, т.к. в таблице
 * `ccp_flight` не все данные обновляются вовремя.
 * Aims::DateToAimsDate($date); std -> date -1 day => преобразовать во время и должен получить STD_UTC_DATE
 * 
 * @package common\modules\ccpFlight\behaviors
 */
class PassengersListSearchBehavior extends Behavior
{
    public function searchPassengersList($passengers)
    {
        /** @var Flight $flight */
        $flight = $this->owner;
        
        $originalFlight = $this->findOriginalflight();
        
        if ($originalFlight !== null) {
            // make active record query with a new std param to find all passengers from the original flight
            $passengers = EdbPassenger::find()
                ->where(['FLT' => $flight->flt])
                ->andWhere(['STD_UTC' => $originalFlight->std])
                ->andWhere(['NOT IN', 'PASSENGER_STATUS', ['CANCEL', 'DELETED']]);
        }
        
        return $passengers;
    }
    
    /**
     * Find previous flight without letter A and with canceled status
     * @return \yii\db\ActiveRecord|array|NULL
     */
    protected function findOriginalflight() : ?Flight
    {
        $flight = $this->findFlight(Flight::STATUS_CANCELED);
        
        return $flight;
    }
    
    protected function findFlight($canceled = null) : ?Flight
    {
        return Flight::find()->where(['flt' => $this->owner->flt])
            ->andFilterWhere(['canceled' => $canceled])
            ->andWhere(['day' => $this->owner->day-1])->one();
    }
}
