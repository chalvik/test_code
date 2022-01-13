<?php
namespace common\modules\ccpEmployee\exceptions;

use yii\base\Exception;

/**
 * Класс для всех исключений для модуля сотрудников
 *
 * Class EmployeeException
 * @package common\modules\ccpEmployee\exceptions
 */
class EmployeeException extends Exception
{
    public function __construct($message = null, $code = 0, \Exception $previous = null)
    {
        parent::__construct(500, $message, $code);
    }
}
