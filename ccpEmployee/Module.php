<?php

namespace common\modules\ccpEmployee;

use common\components\BaseModule;

/**
 * Модуль для загрузки, обновления и хранения сотрудников
 * реализует контроллеры для работы
 * с тестовйо админкой
 * с апи
 * с консолью
 * является самостоятельным модулем, но от него зависят другие модули проекта
 *
 * Class Module
 * @package common\modules\ccpEmployee
 */
class Module extends BaseModule
{
    public $alias = 'ccpEmployee';
}