<?php
namespace common\modules\ccpFlight\controllers;

use common\components\BaseController;

/**
 * Контроллер для админки разработчика (backend)
 * реализует методы для работы, с экипажем на рейсе
 *
 * Class LegCrewController
 * @package common\modules\ccpFlight\controllers
 * @property string $modelName
 */
class LegCrewController extends BaseController
{
    public $modelName = 'FlightLegCrew';
}
