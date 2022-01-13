<?php

namespace common\modules\ccpFlight;

use common\components\BaseModule;

/**
 * Модуль для загрузки, обновления и хранения плеч рейсов и формирование с плеч рейсов с уникальным идентификатором
 * реализует контроллеры для работы
 * с тестовйо админкой
 * с апи
 * с консолью
 * не является самостоятельным модулем, и от него зависят другие модули проекта
 *
 * Class Module
 * @package common\modules\ccpFlight
 */
class Module extends BaseModule
{
    public $alias='ccpFlight';
}