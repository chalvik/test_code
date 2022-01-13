<?php
namespace common\modules\ccpFlight\models;

use Yii;

/**
 * Справочник интервалов рейса (FlightInterval)
 * класс работающий с ActiveRecord объекта и связями с этим объектом
 *
 * Class FlightInterval
 * @package common\modules\ccpFlight\models
 * @property integer $id
 * @property string $title
 * @property double $min
 * @property double $max
 * @property string $created_at
 * @property string $updated_at
 */
class FlightInterval extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ccp_flight_interval';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'min', 'max'], 'required'],
            [['min', 'max'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('flight', 'ID'),
            'title' => Yii::t('flight', 'Title'),
            'min' => Yii::t('flight', 'Min'),
            'max' => Yii::t('flight', 'Max'),
            'created_at' => Yii::t('flight', 'Created At'),
            'updated_at' => Yii::t('flight', 'Updated At'),
        ];
    }
}
