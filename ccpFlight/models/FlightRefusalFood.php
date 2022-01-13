<?php

namespace common\modules\ccpFlight\models;

use common\models\base\BaseActiveRecord;
use Yii;

/**
 * This is the model class for table "ccp_flight_refusal_food".
 *
 * @property int $id
 * @property int $flight_id Идентификатор рейса
 * @property string $roster_id crewMemberId или табельный номер
 * @property string $crewMemberFullName ФИО сотрудника
 * @property string $role Роль на рейсе
 * @property string $status Статус
 * @property string $created_at
 * @property string $updated_at
 */
class FlightRefusalFood extends BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ccp_flight_refusal_food';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['flight_id'], 'default', 'value' => null],
            [['flight_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['roster_id', 'crewMemberFullName', 'role', 'status'], 'string', 'max' => 256],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('refusal_food', 'ID'),
            'flight_id' => Yii::t('refusal_food', 'Flight ID'),
            'roster_id' => Yii::t('refusal_food', 'Roster ID'),
            'crewMemberFullName' => Yii::t('refusal_food', 'Crew Member Full Name'),
            'role' => Yii::t('refusal_food', 'Role'),
            'status' => Yii::t('refusal_food', 'Status'),
            'created_at' => Yii::t('refusal_food', 'Created At'),
            'updated_at' => Yii::t('refusal_food', 'Updated At'),
        ];
    }
}
