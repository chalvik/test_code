<?php

namespace common\modules\ccpFlight\models;

use Yii;

/**
 * Рейсы совместной эксплуатации
 * класс работающий с ActiveRecord объекта и связями с этим объектом
 *
 * Class FeatureFlight
 * @package common\modules\ccpFlight\models
 * @property integer $id
 * @property integer $flight_flt
 * @property integer $type
 * @property string $note
 * @property string $start_date
 * @property string $end_date
 */
class FeatureFlight extends \yii\db\ActiveRecord
{
    const TYPE_EXTERNAL = 2;
    const TYPE_MANUAL = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%feature_flight}}';
    }

    public static function mapTypes()
    {
        return [
            self::TYPE_MANUAL => "MANUAL",
            self::TYPE_EXTERNAL => "EXTERNAL",
        ];
    }

    /**
     * @inheritdoc
     */

    public function rules()
    {
        return [
            [['flight_flt', 'type'], 'integer'],
            [['type'], 'default','value' =>  self::TYPE_MANUAL],
            [['note'], 'string'],
            [['start_date', 'end_date'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }


    public function beforeSave($insert)
    {
        $this->start_date = $this->start_date.' 00:00:00';
        $this->end_date = $this->end_date.' 23:59:59';

        return parent::beforeSave($insert);
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('flight', 'ID'),
            'flight_flt' => Yii::t('flight', 'Flight Flt'),
            'type' => Yii::t('flight', 'Type'),
            'note' => Yii::t('flight', 'Note'),
            'start_date' => Yii::t('flight', 'Start Date'),
            'end_date' => Yii::t('flight', 'End Date'),
        ];
    }
}
