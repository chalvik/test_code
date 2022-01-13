<?php

namespace common\modules\ccpFlight\models;

use common\models\base\BaseActiveRecord;
use common\modules\cargo\models\CargoPreliminfo;
use common\modules\cargo\models\FlightLoadCompartment;
use common\modules\cargo\models\FlightLoadDetail;
use common\modules\cargo\models\CargoFfm;
use common\modules\cargo\models\FlightMail;
use common\modules\ccpAircraft\models\Aircraft;
use common\modules\ccpAirport\models\Airport;
use common\modules\ccpFlight\behaviors\IntervalBehavior;
use common\modules\ccpFlight\behaviors\NewMenuSearchBehavior;
use common\modules\ccpFlight\models\helpers\HelperFlightFeatures;
use common\modules\ccpNotification\models\Notification;
use common\modules\ccpUser\models\User;
use common\modules\ccpWeather\models\Weather;
use common\modules\compPackage\models\CompPackage;
use common\modules\EdbPassenger\models\EdbPassengerTransfer;
use common\modules\feedback\models\Feedback;
use common\modules\food\models\Menu;
use common\modules\EdbPassenger\models\EdbPassenger;
use common\modules\report\models\Report;
use common\modules\chat\models\Ticket;
use common\modules\scheduler\models\ExtendedLogger;
use console\models\Aims;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use common\models\helpers\TimeHelper;
use common\modules\ccpFlight\behaviors\PassengersListSearchBehavior;
use common\modules\ccpAircraft\models\AircraftTypeNames;

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
 * @property string $os letter
 * @property integer $carrier
 * @property integer $aircraft_id
 * @property integer $dep_airport_id
 * @property integer $arr_airport_id
 * @property string $origin_std_date Scheduled origin std time
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
 * @property Menu|null $menu
 * @property CompPackage[] compPackage
 * @property FlightLeg[] legs
 * @property FlightLegCrew $crew
 * @property EdbPassenger[] passengers
 * @property FlightLegCrew puCrew
 * @method Menu|null getAvailableMenu() see [[MenuSearchBehavior::getAvailableMenu()]] for more info
 * @method getCachingKey()
 *
 * @property string $departureDatePlanUTC
 * @property string $departureDatePlanLOC
 * @property string $departureDateFactUTC
 * @property string $departureDateFactLOC
 */
class Flight extends BaseActiveRecord
{
    const LETTER_A = 'A';
    const LETTER_A_CYR = 'А';
    const LETTER_B = 'B';
    const LETTER_G = 'G';
    const LETTER_R = 'R';
    const LETTER_S = 'S';
    const LETTER_T = 'T';
    const LETTER_U = 'U';
    const LETTER_V = 'V';
    const LETTER_W = 'W';
    
    const STATUS_CANCELED = 2;

    /**
     * @inheritdoc
     */

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class' => NewMenuSearchBehavior::className(),
            ],
            [
                'class' => IntervalBehavior::className(),
            ],
            [
                'class' => PassengersListSearchBehavior::className(),
            ]
        ]);
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%ccp_flight}}';
    }

    public function rules()
    {
        return [
            [
                [
                    'day', 'flt', 'carrier', 'aircraft_id', 'dep_airport_id',
                    'arr_airport_id', 'deleted_user_id', 'canceled',
                    'updated_user_id', 'menu_id', 'interval'
                ],
                'integer'
            ],
            [['origin_std_date', 'std', 'sta', 'etd', 'eta', 'deleted_at', 'created_at', 'updated_at',
                'last_updated_at', 'blon', 'blof', 'estimated'], 'safe'],
            [['arr_weather', 'dep_weather'], 'string'],
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
            'fltDes' => Yii::t('flight', 'Flt Des'),
            'os' => Yii::t('flight', 'Os'),
            'carrier' => Yii::t('flight', 'Carrier'),
            'aircraft_id' => Yii::t('flight', 'Aircraft ID'),
            'dep_airport_id' => Yii::t('flight', 'Dep Airport ID'),
            'arr_airport_id' => Yii::t('flight', 'Arr Airport ID'),
            'origin_std_date' =>  Yii::t('flight', 'Origin Std Data'),
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

    public function extraFields() : array
    {
        return ['legs', 'firstLeg', 'depAirport', 'arrAirport', 'aircraft', 'crew', 'menu', 'feedback', 'mails',
            'loadDetails', 'loadCompartment', 'featuresFlight', 'restrictions', 'ffm', 'preliminfo', 'compPackage',
            'depWeather', 'arrWeather'];
    }

    /**
     * @return array
     */
    public static function availableExtraRelations() : array
    {
        return ['depAirport', 'arrAirport', 'legs', 'firstLeg', 'aircraft', 'crew', 'feedback', 'mails',
            'loadDetails', 'loadCompartment', 'featuresFlight', 'ffm', 'preliminfo', 'compPackage',
            'depWeather', 'arrWeather'];

    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        $fields = parent::fields();
        unset($fields['menu_id']);

        $fields['menu_id'] = function (self $model) {
            if ($menu = $model->getAvailableMenu()) {
                return $menu->id;
            }
            return null;
        };

        $unwanted = [
            'created_at', 'updated_at', 'deleted', 'deleted_at',
            'deleted_user_id', 'updated_user_id', 'dep_weather', 'arr_weather'
        ];

        foreach ($unwanted as $fieldName) {
            unset($fields[$fieldName]);
        }

        return $fields;
    }

    /**
     * @param integer $to
     * @param int $from
     * @return ActiveQuery
     */
    public static function getCurrentFlights($to, $from = 0) : ActiveQuery
    {
        $current_time = time();
        $start = gmdate("Y-m-d H:i", $current_time + $from * 3600);
        $end = gmdate("Y-m-d H:i", $current_time + 3600 * $to);
        ExtendedLogger::storeLog(" TIME FROM $start TO $end");
        return Flight::find()
            ->where(['between', 'std', $start, $end]);
    }

    /**
     * Связь с плечами рейса
     * Relation all leg for flight
     * @return \yii\db\ActiveQuery
     */
    public function getLegs()
    {
        return $this->hasMany(FlightLeg::class, ['flight_id' => 'id']);
    }

    /**
     * Связь с плечами рейса
     * Relation all leg for flight
     * @return ActiveQuery
     */
    public function getHelper() : ActiveQuery
    {
        return $this->hasOne(HelperFlightFeatures::class, ['flight_id' => 'id']);
    }

    /**
     * Связь с пассажирами
     * Relation all leg for flight
     * @return \yii\db\ActiveQuery
     */
    public function getPassengers()
    {
        $std = $this->std;
        if (strtotime($this->origin_std_date) > strtotime("01.01.2017 00:00")) { // start ccp project year
            $std = $this->origin_std_date;
        }

        $passengers = $this->hasMany(EdbPassenger::class, ['FLT' => 'flt'])
            ->andWhere(['STD_UTC' => $std,])
            ->andWhere(['NOT IN', 'PASSENGER_STATUS', ['CANCEL', 'DELETED']])
            ->andWhere(['NOT',
                ['AND',
                  ['OR', ['SURNAME' => ''], ['SURNAME' => null]],
                  ['OR', ['TICKET' => ''], ['TICKET' => null]],
                  ['PASSENGER_STATUS' => 'RESERVED']
                ]
              ]
            )
            ->with(['flight', 'depAirport', 'arrAirport', 'vip'])
            ->orderBy(['SEAT'=> SORT_ASC,'PAX_TYPE' => SORT_ASC]);
      
        return $passengers;
    }

    /**
     * Связь с транферными пассажирами
     * Relation transfer passenger
     * @return \yii\db\ActiveQuery
     */
    public function getTransfer()
    {
        return $this->hasMany(EdbPassengerTransfer::class, ['flight_id' => 'id']);
    }

    /**
     * Связь с первым плечем рейса
     * Relation first leg for flight
     * @return ActiveQuery
     */
    public function getFirstLeg() : ActiveQuery
    {
        return $this->hasOne(FlightLeg::class, ['flight_id' => 'id'])
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
            return $this->hasOne(Menu::class, ['id' => 'menu_id']);
        } else {
            return $this->getAvailableMenu();
        }
    }

    /**
     * Связь с воздушным судном
     * Relation Aircraft for flight
     * @return ActiveQuery
     */
    public function getAircraft() : ActiveQuery
    {
        return $this->hasOne(Aircraft::className(), ['id' => 'aircraft_id'])
            ->cache(Aircraft::CACHING_TIME, Aircraft::tag($this->aircraft_id));
    }

    /**
     * Связь с аэропортом прилета
     * Relation Arrival Airport for flight
     * @return ActiveQuery
     */
    public function getArrAirport() : ActiveQuery
    {
        return $this->hasOne(Airport::class, ['id' => 'arr_airport_id'])
            ->cache(Airport::CACHING_TIME, Airport::tag($this->arr_airport_id));
    }

    /**
     * Связь с аэропортом вылета
     * Relation Departure Airport for flight
     * @return ActiveQuery
     */
    public function getDepAirport() : ActiveQuery
    {
        return $this->hasOne(Airport::class, ['id' => 'dep_airport_id'])
            ->cache(Airport::CACHING_TIME, Airport::tag($this->dep_airport_id));
    }

    /**
     * Link to Weather
     * @return ActiveQuery
     */
    public function getArrWeather() : ActiveQuery
    {
        return $this->hasOne(Weather::class, ['airport_id' => 'arr_airport_id'])
            ->where(['<=', 'from', $this->sta])
            ->andWhere(['>', 'to', $this->sta]);
    }

    /**
     * Link to Weather
     * @return ActiveQuery
     */
    public function getDepWeather() : ActiveQuery
    {
        return $this->hasOne(Weather::class, ['airport_id' => 'dep_airport_id'])
            ->where(['<=', 'from', $this->sta])
            ->andWhere(['>', 'to', $this->sta]);
    }

    /**
     * Связь на экипаж который на рейсе
     * Relation Crew(first leg)  for flight
     * @return ActiveQuery
     */

    public function getCrew() : ActiveQuery
    {
        return $this->hasMany(FlightLegCrew::class, ['flight_id' => 'id']);
    }

    /**
     * Get Crew with qual PU for flight
     * @return ActiveQuery
     */
    public function getPuCrew() : ActiveQuery
    {
        return $this->hasOne(FlightLegCrew::class, ['flight_id' => 'id'])
            ->where(['pos_code' => 'PU']);
    }

    /**
     * Связь на обратную связь
     * Relation Feedback messages
     * @return ActiveQuery
     */
    public function getFeedback() : ActiveQuery
    {
        return $this->hasMany(Feedback::class, ['flight_id' => 'id']);
    }

    /**
     * Связь на почту перевозимую рейсом
     * Relation
     * @return ActiveQuery
     */
    public function getMails() : ActiveQuery
    {
        return $this->hasMany(FlightMail::class, ['flight_id' => 'id']);
    }

    /**
     * Связь на загрузку воздушного судна на рейсе (грузы)
     * Relation
     * @return ActiveQuery
     */
    public function getLoadDetails() : ActiveQuery
    {
        return $this->hasMany(FlightLoadDetail::class, ['flight_id' => 'id']);
    }

    /**
     * Связь на загрузку отсеков воздушного судна
     * Relation
     * @return ActiveQuery
     */
    public function getLoadCompartment() : ActiveQuery
    {
        return $this->hasMany(FlightLoadCompartment::class, ['flight_id' => 'id']);
    }

    /**
     * Предварительная загрузка воздушного судна
     * Relation
     * @return ActiveQuery
     */
    public function getPreliminfo() : ActiveQuery
    {
        return $this->hasMany(CargoPreliminfo::class, ['flight_id' => 'id']);
    }

    /**
     * Рейсы совмесной эксплуатации
     * Relation
     * @return ActiveQuery
     */
    public function getFeaturesFlight() : ActiveQuery
    {
        return $this->hasMany(FeatureFlight::class, ['flight_flt' => 'flt'])
            ->andWhere(['<=', 'start_date', $this->std])
            ->andWhere(['>=', 'end_date', $this->std]);
    }

    /**
     * @deprecated
     * Связь на эксплутационные ограничения
     * Relation
     * @return \yii\db\ActiveQuery | array
     */
    public function getRestrictions()
    {
        return [];
    }

    /**
     * Связь на грузы Ffm
     * Relation Ffm
     * @return ActiveQuery
     */
    public function getFfm() : ActiveQuery
    {
        return $this->hasMany(CargoFfm::class, ['flight_id' => 'id']);
    }

    /**
     * Связь на отчеты по рейсу
     * Relation Reports
     * @return ActiveQuery
     */
    public function getReports() : ActiveQuery
    {
        return $this->hasMany(Report::class, ['flight_id' => 'id']);
    }

    /**
     * Связь на опопвещения по рейсу
     * Relation Notifications
     * @return ActiveQuery
     */
    public function getNotifications() : ActiveQuery
    {
        return $this->hasMany(Notification::class, ['flight_id' => 'id']);
    }

    /**
     * Связь на тикеты чата
     * Relation chat tickets
     * @return ActiveQuery
     */
    public function getChatTickets() : ActiveQuery
    {
        return $this->hasMany(Ticket::class, ['flight_id' => 'id']);
    }

    /**
     * Связь на привязанные компенсационные пакеты
     * Relation chat tickets
     * @return ActiveQuery
     */
    public function getCompPackage() : ActiveQuery
    {
        return $this->hasMany(CompPackage::class, ['flight_id' => 'id']);
    }

    /**
     * Возвращает локальное время (учитываю часовой пояс) аэропорта прилета
     *
     * Get local datetime airport arrival
     * format Unix Timestamp
     * @return int
     */
    public function getStaAirport() : int
    {
        $diff = $this->arrAirport->utc_aims || 0;
        return strtotime($this->sta . ' UTC') + $diff * 60;
    }

    /**
     * Возвращает локальное время (учитываю часовой пояс) аэропорта вылета
     * Get local datetime airport departure
     * format Unix Timestamp
     * @return  int
     */
    public function getStdAirport() : int
    {
        $diff = $this->depAirport->utc_aims  || 0;
        return strtotime($this->std . ' UTC') + $diff * 60;
    }

    /**
     * Проверяет, является ли пользователь
     * в экипаже и с указанной должностью (категорией)
     * @param User $user
     * @param array $quals
     * @return bool
     */
    public function getIsCrew(User $user, $quals) :bool
    {
        $quals = (is_array($quals)) ? $quals : [];
        $output = false;
        $crews = FlightLegCrew::find()
            ->where([
                'flight_id' => $this->id
            ])
            ->all();

        foreach ($crews as $crew) {
            /** @var FlightLegCrew $crew */
            if ($user->roster_id == $crew->roster_id) {
                if (count($quals)) {
                    if (in_array($crew->pos_code, $quals)) {
                        $output = true;
                    }
                } else {
                    $output = true;
                }
                break;
            }
        }
        return $output;
    }

    /**
     * Плановое время начала движения
     * @return string
     */
    public function getDepartureDatePlanUTC(): string
    {
        return (string)$this->std;
    }

    /**
     * Плановое время начала движения с коррекцией на местное время
     */
    public function getDepartureDatePlanLOC(): string
    {
        return TimeHelper::getDateTime($this->std, $this->depAirport->utc_aims * 60);
    }

    /**
     * Время отрыва от земли
     * @return string
     */
    public function getDepartureDateFactUTC(): string
    {
        return (string)empty($this->tkof) ? "" : $this->tkof;
    }

    /**
     * Время отрыва от земли с коррекцией локального времени
     * @return string
     */
    public function getDepartureDateFactLOC() : string
    {
        return empty($this->tkof) ? "" :
            TimeHelper::getDateTime($this->tkof, $this->depAirport->utc_aims * 60);
    }
    
    /**
     * Returns some of FlightTypes const like TYPE_INTERNATIONAL or TYPE_INNER.
     * @param string $country
     * @return int
     */
    public function getFlightInterType($country = 'RUSSIAN FEDERATION') : int
    {
        $inter_flight = FlightTypes::TYPE_INTERNATIONAL;
        
        if ($this->isInnerCountryFlight($country)) {
            $inter_flight = FlightTypes::TYPE_INNER;
        }
        
        return $inter_flight;
    }
    
    /**
     * Returns true if the flight is inner and false if it is outer.
     * @param string $country
     * @return bool
     */
    public function isInnerCountryFlight($country) : bool
    {
        $flight_arr_airport = Airport::findOne($this->arr_airport_id);
        $flight_dep_airport = Airport::findOne($this->dep_airport_id);
        
        return $flight_arr_airport->country == $country && $flight_dep_airport->country == $country;
    }
    
    public function getAircraftType() : int
    {
        $atypes = AircraftTypeNames::list();
        
        return $atypes[$this->aircraft->name] ?? 0;
    }
    
    /**
     * Return array flt number for flights for admin developer
     * @return  array
     */
    public static function mapFlt() : array
    {
        return Flight::find()
            ->select('flt')
            ->groupBy('flt')
            ->indexBy('flt')
            ->cache(3600)
            ->column();
    }
}
