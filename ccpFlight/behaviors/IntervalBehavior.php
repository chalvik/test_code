<?php
namespace common\modules\ccpFlight\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Поведение для расчета периода рейса, по данным сущности  /common/modules/cppFlight/model/FlightInterval
 * и записи идентификатора периода в поле interval
 * при сохранении . обновлении рейса
 *
 * Behavior for identify avaialble flight menu
 * DON'T EXTEND THIS CLASS, PLEASE!
 * Class IntervalBehavior
 * @package common\modules\ccpFlight\behaviors
 */
final class IntervalBehavior extends Behavior
{
    /**
     * @inheritDoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => "setInterval",
            ActiveRecord::EVENT_BEFORE_UPDATE => "setInterval",
        ];
    }
    
    /**
     * Расчитываем период рейса и подбираем соответствующую связь с объектом
     * и записи идентификатора периода в поле interval
     */
    public function setInterval()
    {
        $std = $this->owner->std;
        $sta = $this->owner->sta;
        $diff =  (strtotime($sta) - strtotime($std));
        if ($diff < 0) {
            $interval = 0;
        } else {
            $interval = floor($diff/60);
        }
        
        $fl = \common\modules\ccpFlight\models\FlightInterval::find()
                ->where(['<=','min',$interval])
                ->andWhere(['>=','max',$interval])
                ->one();
        
        if ($fl) {
            $this->owner->interval = $fl->id;
        }
    }
}
