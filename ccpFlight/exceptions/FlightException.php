<?php
namespace common\modules\ccpFlight\exceptions;

use yii\base\Exception;

/**
 * Класс для всех исключений для модуля рейсов
 *
 * Class FlightException
 * @package common\modules\ccpFlight\exceptions
 */
class FlightException extends Exception{

    public function __construct($message = null, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code);
    }

}
