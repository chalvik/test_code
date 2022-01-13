<?php

namespace common\modules\ccpFlight\models\helpers;

use common\models\helpers\TimeHelper;
use common\modules\ccpFlight\models\Flight;
use Yii;
use yii\db\ActiveRecord;
use yii\db\ArrayExpression;

/**
 * This is the model class for table "helper_flight_features".
 *
 * @property int $flight_id
 * @property ArrayExpression|array $codes
 * @property ArrayExpression|array $existed_codes
 * @property ArrayExpression|array $crew_pos
 * @property bool $has_fail_transfers
 * @property string $created_at
 *
 * @property Flight $flight
 */
class HelperFlightFeatures extends \yii\db\ActiveRecord
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

    public static function primaryKey()
    {
        return ['flight_id'];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'helper_flight_features';
    }

    public function afterFind()
    {

        if ($this->codes instanceof ArrayExpression) $this->codes = $this->codes->getValue();
        if ($this->existed_codes instanceof ArrayExpression) $this->existed_codes = $this->existed_codes->getValue();
        if ($this->crew_pos instanceof ArrayExpression) $this->crew_pos = $this->crew_pos->getValue();
        parent::afterFind();
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['flight_id'], 'default', 'value' => null],
            [['flight_id'], 'integer'],
            [['codes', 'existed_codes','crew_pos'], 'safe'],
            [['has_fail_transfers'], 'boolean'],
            [['flight_id'], 'exist', 'skipOnError' => true, 'targetClass' => Flight::className(), 'targetAttribute' => ['flight_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'flight_id' => 'Flight ID',
            'codes' => 'Codes',
            'existed_codes' => 'Existed Codes',
            'has_fail_transfers' => 'Has Fail Transfers',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFlight()
    {
        return $this->hasOne(Flight::className(), ['id' => 'flight_id']);
    }
}
