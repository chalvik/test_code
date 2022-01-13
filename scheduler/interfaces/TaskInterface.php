<?php

namespace common\modules\scheduler\interfaces;

/**
 * Interface TaskInterface
 * @package common\modules\scheduler\interfaces
 */
interface TaskInterface
{
    /**
     *  Method run for work tof task
     * @return array  error ['status'=> true|false, message ='']
     */
    public function run();

    /**
     * @return array
     */
    public function getErrors();

    /**
     * @return integer
     */
    public function getStatus();
}
