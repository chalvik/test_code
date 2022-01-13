<?php
namespace common\modules\ccpFlight\controllers\api;

use api\rest\ActiveRestController;

/**
 * Класс реализует методы для обработки апи запросов
 * для справочника интервалов рейсов
 *
 * This is the api controller class for flight legs.
 * Class IntervalController
 * @package common\modules\ccpFlight\controllers\api
 * @property string $modelClass
 */
class IntervalController extends ActiveRestController
{
    public $modelClass = 'common\modules\ccpFlight\models\FlightInterval';
}

