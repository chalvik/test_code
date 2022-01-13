<?php
namespace common\modules\ccpFlight\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Компании (Carrier)
 * класс работающий с массивов данных прописанном в объекте
 *
 * Class Carrier
 * @package common\modules\ccpFlight\models
 * @property integer $id
 * @property string $title
 */
class Carrier extends \yii\base\Model
{

    public $id;
    public $title;

    /**
     * carrier Globus
     */
    const TYPE_VIEW_ONLY_CARRIER = 1;

    /**
     * carrier S7
     */
    const TYPE_VIEW_ALL = 2;

    /**
     * carrier S7
     */
    const CARRIER_S7 = 1;

    /**
     * carrier Globus
     */
    const CARRIER_GH = 2;

    /**
     * carrier All company
     */
    const CARRIER_ALL = 99;

    /**
     * Массив возможных компаний
     * @var array
     */
    public static $carrier_list_aims =
        [
            self::CARRIER_S7 => 'S7',
            self::CARRIER_GH => 'GH',
            self::CARRIER_ALL => 'ALL',
        ];

    /**
     * Возвращает массив объектов компаний
     * Get array objects of carrier
     * @param int $type_view
     * @return array
     */
    public static function findAll($type_view = self::TYPE_VIEW_ALL)
    {
        $result = [];
        if ($type_view == self::TYPE_VIEW_ALL) {
            $item = new self;
            $item->id = self::CARRIER_ALL;
            $item->title = Yii::t('carrier', 'CARRIER_ALL');
            $result[] = $item;
        }

        $item = new self;
        $item->id = self::CARRIER_S7;
        $item->title = Yii::t('carrier', 'CARRIER_S7');
        $result[] = $item;

        $item = new self;
        $item->id = self::CARRIER_GH;
        $item->title = Yii::t('carrier', 'CARRIER_GH');
        $result[] = $item;

        return $result;
    }

    /**
     * Возвращает объект компании по ключу
     * Get one record of carrier
     * @param int $id
     * @return self
     */
    public static function findOne($id)
    {
        $carriers = ArrayHelper::index(static::findAll(), 'id');
        return key_exists($id, $carriers) ? $carriers[$id] : null;
    }

    /**
     * Возвращает id компании по коду
     * Get id record for code
     * @param string $code
     * @return integer
     */
    public static function getIdFromCode($code)
    {
        $id = array_search($code, static::$carrier_list_aims);
        return $id ?: null;
    }

    /**
     * Возвращает код компании по ай ди
     * @param integer $id
     * @return integer
     */
    public static function getCodeFromId($id)
    {
        return isset(self::$carrier_list_aims[$id])?self::$carrier_list_aims[$id]:null;
    }


}
