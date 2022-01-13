<?php
/**
 * Created by PhpStorm.
 * User: lexa
 * Email: achernogor@iseck.com
 * Date: 16.12.19
 * Time: 0:32
 */

namespace common\modules\ccpFlight\models;

use yii\base\Model;
use yii\helpers\ArrayHelper;

class FlightTypes extends Model
{
    
    const TYPE_ALL = 0;
    
    /** рейс внутренний по России  */
    const TYPE_INNER = 1;
    
    /**  Рейс международный */
    const TYPE_INTERNATIONAL = 2;

    public static $type_list = [
        [
            'id' => self::TYPE_ALL,
            'title' => 'Все',
        ],
        [
            'id' => self::TYPE_INNER,
            'title' => 'по России',
        ],
        [
            'id' => self::TYPE_INTERNATIONAL,
            'title' => 'Международный',
        ]
    ];
    
    public static function list()
    {
        return ArrayHelper::map(self::$type_list, 'id', 'title');
    }
}