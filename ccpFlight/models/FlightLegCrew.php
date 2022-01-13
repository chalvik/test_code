<?php
namespace common\modules\ccpFlight\models;

use Yii;
use common\models\base\BaseActiveRecord;
use common\modules\ccpEmployee\models\Employee;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use common\behaviors\SaveChangedBehavior;

/**
 * Экипаж на рейсе
 * класс работающий с ActiveRecord объекта и связями с этим объектом
 *
 * Class FlightLegCrew
 * @package common\modules\ccpFlight\models
 * @property integer $id
 * @property integer $employee_id
 * @property integer $flight_leg_id
 * @property integer $roster_id
 * @property string $pos_code
 * @property string $id_dhd
 * @property string $pos_leg1
 * @property string $pos_flight
 * @property string $pos_pu
 * @property string $created_at
 * @property string $updated_at
 * @property string $last_updated_at
 * @property integer $flight_id
 * @property Employee employee
 */
class FlightLegCrew extends BaseActiveRecord
{


    const POSITION_DEFAULT  = 0;
    const POSITION_PU   = 1;
    const POSITION_PU_TRAINEE   = 2;
    const POSITION_FI   = 3;


    /**
     * @var array
     */
    public static $list_types = [
        self::POSITION_DEFAULT      => 'По умолчанию',
        self::POSITION_PU   => 'Старший БП',
        self::POSITION_PU_TRAINEE   => 'Стажер  ',
        self::POSITION_FI   => 'Инструктор '
    ];

    /**
     * @return array
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
        return '{{%ccp_flight_leg_crew}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['employee_id', 'flight_id', 'roster_id'], 'integer'],
            [['created_at', 'updated_at', 'last_updated_at'], 'safe'],
            [['pos_code', 'id_dhd', 'pos_leg1', 'pos_flight', 'pos_pu'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'            => Yii::t('flight_leg_crew', 'ID'),
            'employee_id'   => Yii::t('flight_leg_crew', 'Employee ID'),
            'flight_id'     => Yii::t('flight_leg_crew', 'Flight ID'),
            'roster_id'     => Yii::t('flight_leg_crew', 'Roster ID'),
            'pos_code'      => Yii::t('flight_leg_crew', 'Pos Code'),
            'id_dhd'        => Yii::t('flight_leg_crew', 'Id Dhd'),
            'pos_leg1'      => Yii::t('flight_leg_crew', 'Pos Leg1'),
            'pos_flight'    => Yii::t('flight_leg_crew', 'Pos Flight'),
            'created_at'    => Yii::t('flight_leg_crew', 'Created At'),
            'updated_at'    => Yii::t('flight_leg_crew', 'Updated At'),
            'last_updated_at'   => Yii::t('flight_leg_crew', 'Last Updated At'),
            'pos_pu'            => Yii::t('flight_leg_crew', 'Position By PU'),
        ];
    }

    /**
     * @return array
     */
    public function extraFields()
    {
        return ['employee', 'flight'];
    }

    /**
     * @return array
     */
    public function fields()
    {
        $fields = parent::fields();

        $fields['pos_flight'] = function ($model) {
            /** @var FlightLegCrew $model */
            return (($model->pos_flight) ?: $model->pos_leg1);
        };
        $fields['pos_pu'] = function ($model) {
            /** @var FlightLegCrew $model*/
            return (($model->pos_pu) ?: $model->pos_leg1);
        };
        $fields['pos_id'] = function ($model) {
            /** @var FlightLegCrew $model*/
            return $model->getPositionId();
        };

        $fields['refusal_food'] = function ($model) {
            /** @var FlightLegCrew $model  */
            return ($model->refusalFood)?1:0;
        };

        unset($fields['created_at'], $fields['updated_at'], $fields['roster_id']);
        return $fields;
    }

    /**
     * @return \yii\db\ActiveQuery
     */

    public function getFlight():ActiveQuery
    {
        return $this->hasOne(Flight::class, ['id' => 'flight_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployee():ActiveQuery
    {
        return $this->hasOne(Employee::class, ['roster_id' => 'roster_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRefusalFood():ActiveQuery
    {
        return $this->hasOne(FlightRefusalFood::class, ['roster_id' => 'roster_id', 'flight_id' => 'flight_id'])
            ->where([
                'status' => ['CREATED', 'IN_PROGRESS', 'PROCESSED']
            ]);
    }

    /**
     * Возвращает константу позиции БП на рейсе, с учетом стажировщиков
     * @return int
     */
    public function getPositionId():int
    {
        $position =  self::POSITION_DEFAULT;

        if ($this->pos_code=='PU') {
            $position = self::POSITION_PU;
        } elseif ($this->pos_code=='FI') {
            $position = self::POSITION_FI;
        } elseif ($this->employee->checkCrewCode(['FA'],[114,115]) || false) {
            $position = self::POSITION_PU_TRAINEE;
        }
        return $position;
    }
}
