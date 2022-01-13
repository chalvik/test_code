<?php


namespace common\modules\scheduler\models;


class ExtendedLogger
{

    public static $logStorage = [];

    /**
     * @param array|string|object $message
     */
    public static function storeLog($message)
    {
        if (is_array($message) || is_object($message)) $message = "<pre>" . print_r($message,true) . "</pre>";
        self::$logStorage[] = $message;
    }


}