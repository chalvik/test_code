<?php

namespace common\modules\scheduler\tasks\mock;

use common\modules\report\models\ReportExport;
use common\modules\scheduler\components\AbstractTask;
use common\modules\scheduler\models\ExtendedLogger;
use common\modules\scheduler\models\SchedulerTask;
use common\modules\scheduler\models\SchedulerTaskRun;

/**
 * Class TaskAirport
 * @package common\modules\scheduler\tasks
 */
class ShortMockTask extends AbstractTask
{
    const ITERATIONS_COUNT = 5;
    const TIME_TO_SLEEP_FOR_ITERATION = 1;

    public function run()
    {

        try {
            foreach (range(1, self::ITERATIONS_COUNT) as $item) {
                sleep(static::TIME_TO_SLEEP_FOR_ITERATION);
                ExtendedLogger::storeLog('MESSAGE = ' . date('Y-m-d h-s').' FROM '.get_class());
                ExtendedLogger::storeLog(['array' => ['jkjkj','dscsd'],'jkjkjkj']);
            }
            $this->status = true;


        } catch (\Exception $e) {
            $this->status = false;
            $this->addErrors('exception', $e->getMessage());
        }
    }

}
