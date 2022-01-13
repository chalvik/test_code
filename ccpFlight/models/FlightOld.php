<?php

namespace common\modules\ccpFlight\models;

use common\behaviors\SaveChangedBehavior;
use common\models\base\BaseActiveRecord;
use common\modules\cargo\models\FlightLoadCompartment;
use common\modules\cargo\models\FlightLoadDetail;
use common\modules\cargo\models\CargoFfm;
use common\modules\cargo\models\FlightMail;
use common\modules\ccpAircraft\models\Aircraft;
use common\modules\ccpAircraft\models\Restriction;
use common\modules\ccpAirport\models\Airport;
use common\modules\ccpFlight\behaviors\IntervalBehavior;
use common\modules\ccpFlight\behaviors\MenuSearchBehavior;
use common\modules\ccpNotification\models\Notification;
use common\modules\feedback\models\Feedback;
use common\modules\food\models\Menu;
use common\modules\passenger\models\FlightPassenger;
use common\modules\passenger\models\FlightPassengerTransfer;
use common\modules\report\models\Report;
use common\modules\chat\models\Ticket;
use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * Рейсы (Flight)
 * класс работающий с ActiveRecord объекта и связями с этим объектом
 *
 * Class Flight
 * @package common\modules\ccpFlight\models
 * @property integer $id
 * @property integer $day Aims Day
 * @property integer $flt Flight number
 * @property string $fltDes Flight Description
 * @property string $os
 * @property integer $carrier
 * @property integer $aircraft_id
 * @property integer $dep_airport_id
 * @property integer $arr_airport_id
 * @property string $std Scheduled start time
 * @property string $sta Scheduled end time
 * @property string $etd Estimated time to start
 * @property string $eta Estimated time to end
 * @property string $arr_gate
 * @property string $dep_gate
 * @property string $arr_weather Weather airport of departure
 * @property string $dep_weather Weather airport of arrival
 * @property boolean $deleted Flag delete
 * @property string $deleted_at Date time delete
 * @property integer $deleted_user_id User id  Delete
 * @property integer $canceled
 * @property integer $updated_user_id User Updated
 * @property string $created_at Date time create
 * @property string $updated_at Date time update
 * @property integer $menu_id Menu ID
 * @property string $dep_stand
 * @property string $arr_stand
 * @property string $last_updated_at
 * @property integer $interval
 * @property string $tkof
 * @property string $tdown
 * @property string $blon
 * @property string $blof
 * @property string $estimated
 * @property Feedback[] $feedback
 * @property Airport $depAirport
 * @property Airport $arrAirport
 * @property FlightMail[] $mails
 * @property FlightLoadDetail[] $loadDetails
 * @property FlightLoadCompartment[] $loadCompartment
 * @property FeatureFlight $featuresFlight
 * @property Aircraft $aircraft
 * @property integer stdAirport
 * @property integer staAirport
 * @property string $legcd
 * @method Menu|null getAvailableMenu() see [[MenuSearchBehavior::getAvailableMenu()]] for more info
 */
class FlightOld extends BaseActiveRecord
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class' => MenuSearchBehavior::className(),
            ],
            [
                'class' => SaveChangedBehavior::className(),
            ],
            [
                'class' => IntervalBehavior::className(),
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%ccp_flight}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'day', 'flt', 'carrier', 'aircraft_id', 'dep_airport_id',
                    'arr_airport_id', 'deleted_user_id', 'canceled',
                    'updated_user_id', 'menu_id', 'interval',
                ],
                'integer'
            ],
            [['std', 'sta', 'etd', 'eta', 'deleted_at', 'created_at', 'updated_at',
                'last_updated_at', 'blon', 'blof', 'estimated'], 'safe'],
            [['arr_weather', 'dep_weather'], 'string'],
            [['legcd'], 'string'],
            [['deleted'], 'boolean'],
            [['fltDes', 'arr_gate', 'dep_gate', 'dep_stand', 'arr_stand'], 'string', 'max' => 255],
            [['os'], 'string', 'max' => 2],
            [['deleted'], 'default', 'value' => false],
            [['estimated'], 'default', 'value' => $this->blof]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('flight', 'ID'),
            'day' => Yii::t('flight', 'Day'),
            'flt' => Yii::t('flight', 'Flt'),
            'legcd' => Yii::t('flight', 'Legcd'),
            'fltDes' => Yii::t('flight', 'Flt Des'),
            'os' => Yii::t('flight', 'Os'),
            'carrier' => Yii::t('flight', 'Carrier'),
            'aircraft_id' => Yii::t('flight', 'Aircraft ID'),
            'dep_airport_id' => Yii::t('flight', 'Dep Airport ID'),
            'arr_airport_id' => Yii::t('flight', 'Arr Airport ID'),
            'std' => Yii::t('flight', 'Std'),
            'sta' => Yii::t('flight', 'Sta'),
            'etd' => Yii::t('flight', 'Etd'),
            'eta' => Yii::t('flight', 'Eta'),
            'blon' => Yii::t('flight', 'Blon'),
            'blof' => Yii::t('flight', 'Blof'),
            'tkof' => Yii::t('flight', 'Tkof'),
            'tdown' => Yii::t('flight', 'Tdown'),
            'estimated' => Yii::t('flight', 'Estimated'),
            'arr_gate' => Yii::t('flight', 'Arr Gate'),
            'dep_gate' => Yii::t('flight', 'Dep Gate'),
            'dep_stand' => Yii::t('flight_leg', 'Dep Stand'),
            'arr_stand' => Yii::t('flight_leg', 'Arr Stand'),
            'interval' => Yii::t('flight_leg', 'Interval'),
            'arr_weather' => Yii::t('flight', 'Arr Weather'),
            'dep_weather' => Yii::t('flight', 'Dep Weather'),
            'deleted' => Yii::t('flight', 'Deleted'),
            'deleted_at' => Yii::t('flight', 'Deleted At'),
            'deleted_user_id' => Yii::t('flight', 'Deleted User ID'),
            'canceled' => Yii::t('flight', 'Canceled'),
            'updated_user_id' => Yii::t('flight', 'Updated User ID'),
            'created_at' => Yii::t('flight', 'Created At'),
            'updated_at' => Yii::t('flight', 'Updated At'),
            'last_updated_at' => Yii::t('aircraft', 'Last Updated At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function extraFields()
    {
        return ['legs', 'firstLeg', 'depAirport', 'arrAirport', 'aircraft', 'crew', 'menu', 'feedback', 'mails',
            'loadDetails', 'loadCompartment', 'featuresFlight', 'restrictions', 'ffm'];
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        $fields = parent::fields();
        $unwanted = [
            'created_at', 'updated_at', 'deleted', 'deleted_at',
            'deleted_user_id', 'updated_user_id'
        ];

        foreach ($unwanted as $fieldName) {
            unset($fields[$fieldName]);
        }
        return $fields;
    }

    /**
     * Связь с плечами рейса
     * Relation all leg for flight
     * @return \yii\db\ActiveQuery
     */
    public function getLegs()
    {
        return $this->hasMany(FlightLeg::className(), ['flight_id' => 'id']);
    }

    /**
     * Связь с пассажирами
     * Relation all leg for flight
     * @return \yii\db\ActiveQuery
     */
    public function getPassengers()
    {
        return $this->hasMany(FlightPassenger::className(), ['flight_id' => 'id']);
    }

    /**
     * Связь с транферными пассажирами
     * Relation transfer passenger
     * @return \yii\db\ActiveQuery
     */
    public function getTransfer()
    {
        return $this->hasMany(FlightPassengerTransfer::className(), ['flight_id' => 'id']);
    }

    /**
     * Связь с первым плечем рейса
     * Relation first leg for flight
     * @return \yii\db\ActiveQuery
     */
    public function getFirstLeg()
    {
        return $this->hasOne(FlightLeg::className(), ['flight_id' => 'id'])
            ->orderBy(['std' => 'ASC']);
    }

    /**
     * Возвращает меню
     * Returns available menu
     * @return Menu|\yii\db\ActiveQuery
     */
    public function getMenu()
    {
        if ($this->menu_id) {
            return $this->hasOne(Menu::className(), ['id' => 'menu_id']);
        } else {
            return $this->getAvailableMenu();
        }
    }

    /**
     * Связь с воздушным судном
     * Relation Aircraft for flight
     * @return \yii\db\ActiveQuery
     */
    public function getAircraft()
    {
        return $this->hasOne(Aircraft::className(), ['id' => 'aircraft_id']);
    }

    /**
     * Связь с аэропортом прилета
     * Relation Arrival Airport for flight
     * @return \yii\db\ActiveQuery
     */
    public function getArrAirport()
    {
        return $this->hasOne(Airport::className(), ['id' => 'arr_airport_id']);
    }

    /**
     * Связь с аэропортом вылета
     * Relation Departure Airport for flight
     * @return \yii\db\ActiveQuery
     */
    public function getDepAirport()
    {
        return $this->hasOne(Airport::className(), ['id' => 'dep_airport_id']);
    }

    /**
     * Связь на экипаж который на рейсе
     * Relation Crew(first leg)  for flight
     * @return \yii\db\ActiveQuery
     */
    public function getCrew()
    {
        return $this->hasMany(FlightLegCrew::className(), ['flight_id' => 'id']);
    }

    /**
     * Связь на обратную связь
     * Relation Feedback messages
     * @return \yii\db\ActiveQuery
     */
    public function getFeedback()
    {

        return $this->hasMany(Feedback::className(), ['flight_id' => 'id']);

    }

    /**
     * Связь на почту перевозимую рейсом
     * Relation
     * @return \yii\db\ActiveQuery
     */
    public function getMails()
    {
        return $this->hasMany(FlightMail::className(), ['flight_id' => 'id']);
    }

    /**
     * Связь на загрузку воздушного судна на рейсе (грузы)
     * Relation
     * @return \yii\db\ActiveQuery
     */
    public function getLoadDetails()
    {
        return $this->hasMany(FlightLoadDetail::className(), ['flight_id' => 'id']);
    }

    /**
     * Связь на загрузку отсеков воздушного судна
     * Relation
     * @return \yii\db\ActiveQuery
     */
    public function getLoadCompartment()
    {
        return $this->hasMany(FlightLoadCompartment::className(), ['flight_id' => 'id']);
    }

    /**
     * Рейсы совмесной эксплуатации
     * Relation
     * @return \yii\db\ActiveQuery
     */
    public function getFeaturesFlight()
    {
        return $this->hasMany(FeatureFlight::className(), ['flight_flt' => 'flt'])
            ->andWhere(['<=', 'start_date', new Expression('CURRENT_TIMESTAMP')])
            ->andWhere(['>=', 'end_date', new Expression('CURRENT_TIMESTAMP')]);
    }

    /**
     * Связь на эксплутационные ограничения
     * Relation
     * @return \yii\db\ActiveQuery
     */
    public function getRestrictions()
    {
        return $this->hasMany(Restriction::className(), ['id' => 'restriction_id'])
            ->viaTable('{{%aircraft_restriction}}', ['aircraft_id' => 'aircraft_id']);
    }

    /**
     * Связь на грузы Ffm
     * Relation Ffm
     * @return \yii\db\ActiveQuery
     */
    public function getFfm()
    {
        return $this->hasMany(CargoFfm::className(), ['flight_id' => 'id']);
    }

    /**
     * Связь на отчеты по рейсу
     * Relation Reports
     * @return \yii\db\ActiveQuery
     */
    public function getReports()
    {
        return $this->hasMany(Report::className(), ['flight_id' => 'id']);
    }

    /**
     * Связь на опопвещения по рейсу
     * Relation Notifications
     * @return \yii\db\ActiveQuery
     */
    public function getNotifications()
    {
        return $this->hasMany(Notification::className(), ['flight_id' => 'id']);
    }

    /**
     * Связь на тикеты чата
     * Relation chat tickets
     * @return \yii\db\ActiveQuery
     */
    public function getChatTickets()
    {
        return $this->hasMany(Ticket::className(), ['flight_id' => 'id']);
    }

    /**
     * Возвращает локальное время (учитываю часовой пояс) аэропорта прилета
     *
     * Get local datetime airport arrival
     * format Unix Timestamp
     * @return integer
     */
    public function getStaAirport()
    {
        $diff = $this->arrAirport->utc_aims;
        return strtotime($this->sta . ' UTC') + $diff * 60;
    }

    /**
     * Возвращает локальное время (учитываю часовой пояс) аэропорта вылета
     *
     * Get local datetime airport departure
     * format Unix Timestamp
     * @return \yii\db\ActiveQuery
     */
    public function getStdAirport()
    {
        $diff = $this->depAirport->utc_aims;
        return strtotime($this->std . ' UTC') + $diff * 60;
    }
}