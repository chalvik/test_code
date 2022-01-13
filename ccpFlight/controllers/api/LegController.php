<?php
namespace common\modules\ccpFlight\controllers\api;

use api\rest\ActiveRestController;

/**
 * Класс реализует методы для обработки апи запросов
 * для справочника плеч рейсов
 *
 * This is the api controller class for flight legs.
 * Class LegController
 * @package common\modules\ccpFlight\controllers\api
 * @property string $modelClass
 */
class LegController extends ActiveRestController
{
    public $modelClass = 'common\modules\ccpFlight\models\FlightLeg';
}