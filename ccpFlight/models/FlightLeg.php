<?php
namespace common\modules\ccpFlight\models;

use common\behaviors\SaveChangedBehavior;
use common\models\base\BaseActiveRecord;
use common\modules\ccpFlight\models\console\FlightParse;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * Плечи рейса
 * класс работающий с ActiveRecord объекта и связями с этим объектом
 *
 * Class FlightLeg
 * @package common\modules\ccpFlight\models
 * @property integer $id
 * @property integer $flight_id
 * @property integer $day
 * @property integer $flt
 * @property string $dep
 * @property integer $dep_airport_id
 * @property integer $carrier
 * @property string $legcd
 * @property string $arr
 * @property integer $arr_airport_id
 * @property string $ac
 * @property string $reg
 * @property integer $aircraft_id
 * @property boolean $canceled
 * @property string $adate
 * @property string $aroute
 * @property string $std
 * @property string $sta
 * @property string $etd
 * @property string $eta
 * @property string $blof
 * @property string $tkof
 * @property string $tdown
 * @property string $blon
 * @property string $dep_gate
 * @property string $arr_gate
 * @property string $created_at
 * @property string $updated_at
 * @property string $last_updated_at
 * @property string $last_load_updated_at
 * @property string $last_restriction_updated_at
 * @property string $arr_stand
 * @property string $dep_stand
 * @property FlightParse $flight
 */
class FlightLeg extends BaseActiveRecord
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class' => SaveChangedBehavior::className(),
            ]
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%ccp_flight_leg}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['flight_id', 'day', 'flt', 'dep_airport_id', 'carrier', 'arr_airport_id', 'aircraft_id'], 'integer'],
            [['canceled'], 'boolean'],
            [
                ['std', 'sta', 'etd', 'eta', 'blof', 'tkof', 'tdown','blon',
                    'created_at', 'updated_at', 'last_updated_at',
                    'last_load_updated_at', 'last_restriction_updated_at'
                ], 'safe'
            ],
            [
                ['dep', 'legcd', 'arr', 'ac', 'reg',
                    'adate', 'aroute', 'dep_gate',
                    'arr_gate', 'dep_stand', 'arr_stand'
                ], 'string', 'max' => 255
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('flight_leg', 'ID'),
            'flight_id' => Yii::t('flight_leg', 'Flight ID'),
            'day' => Yii::t('flight_leg', 'Day'),
            'flt' => Yii::t('flight_leg', 'Flt'),
            'dep' => Yii::t('flight_leg', 'Dep'),
            'dep_airport_id' => Yii::t('flight_leg', 'Dep Airport ID'),
            'carrier' => Yii::t('flight_leg', 'Carrier'),
            'legcd' => Yii::t('flight_leg', 'Legcd'),
            'arr' => Yii::t('flight_leg', 'Arr'),
            'arr_airport_id' => Yii::t('flight_leg', 'Arr Airport ID'),
            'ac' => Yii::t('flight_leg', 'Ac'),
            'reg' => Yii::t('flight_leg', 'Reg'),
            'aircraft_id' => Yii::t('flight_leg', 'Aircraft ID'),
            'canceled' => Yii::t('flight_leg', 'Canceled'),
            'adate' => Yii::t('flight_leg', 'Adate'),
            'aroute' => Yii::t('flight_leg', 'Aroute'),
            'std' => Yii::t('flight_leg', 'Std'),
            'sta' => Yii::t('flight_leg', 'Sta'),
            'etd' => Yii::t('flight_leg', 'Etd'),
            'eta' => Yii::t('flight_leg', 'Eta'),
            'blof' => Yii::t('flight_leg', 'Blof'),
            'tkof' => Yii::t('flight_leg', 'Tkof'),
            'tdown' => Yii::t('flight_leg', 'Tdown'),
            'blon' => Yii::t('flight_leg', 'Blon'),
            'dep_gate' => Yii::t('flight_leg', 'Dep Gate'),
            'arr_gate' => Yii::t('flight_leg', 'Arr Gate'),
            'dep_stand' => Yii::t('flight_leg', 'Dep Stand'),
            'arr_stand' => Yii::t('flight_leg', 'Arr Stand'),
            'created_at' => Yii::t('flight_leg', 'Created At'),
            'updated_at' => Yii::t('flight_leg', 'Updated At'),
            'last_updated_at' => Yii::t('aircraft', 'Last Updated At'),
            'last_load_updated_at' => Yii::t('aircraft', 'Last Load Updated At'),
            'last_restriction_updated_at' => Yii::t('aircraft', 'Last Restriction Updated At'),
        ];
    }

    /**
     * Связь на рейс
     * Relation  Flight
     * @return ActiveQuery
     */
    public function getFlight()
    {
        return $this->hasOne(FlightParse::className(), ['id' => 'flight_id']);
    }

    /**
     * Связь на экипаж
     * Relation Crew  for leg of flight
     * @return ActiveQuery
     */

    public function getCrew()
    {
        return $this->hasMany(FlightLegCrew::className(), ['flight_leg__id' => 'id']);
    }

}
