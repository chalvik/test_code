<?php

namespace common\modules\ccpFlight\models;

use common\models\helpers\TimeHelper;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "ccp_flight_force_update".
 *
 * @property int $flight_id
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $is_updated
 */

class CcpFlightForceUpdate extends \yii\db\ActiveRecord
{

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'value' => TimeHelper::getGreenwichCurrentTime(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ccp_flight_force_update';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['flight_id'], 'required'],
            [['flight_id'], 'integer'],
            [['flight_id'], 'unique'],
            [['created_at', 'updated_at'], 'safe'],
            [['is_updated'], 'boolean'],
            [['is_updated'], 'default','value' => false],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'flight_id' => 'Flight ID',
            'is_updated' => 'is_updated',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
