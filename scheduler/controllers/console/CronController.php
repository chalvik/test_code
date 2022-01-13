<?php

namespace common\modules\scheduler\controllers\console;

use backend\models\D;
use common\modules\scheduler\components\AbstractTask;
use common\modules\scheduler\models\ExtendedLogger;
use common\modules\scheduler\models\SchedulerTaskLog;
use console\controllers\BaseController;
use common\modules\scheduler\models\SchedulerTaskRun;
use common\modules\scheduler\models\SchedulerTask;
use Hoa\Console\Console;
use yii\db\Exception;

/**
 * Class CronController
 * @package common\modules\scheduler\controllers\console
 */
class CronController extends BaseController
{
    /**
     * @param int $display_log
     */
    public function actionIndex($display_log = BaseController::LOG_NONE)
    {
        // time where we start
        $started_at = time();
        $this->log("Scheduler start", "white", null, $display_log);

        // add tasks to line schedule
        SchedulerTask::addTasksinLine();

        // get new task if time from start less then 60 seconds
        while (((time() - $started_at) < 50
            &&
            ($run = SchedulerTaskRun::firstInLine()))) {
            echo $this->ansiFormat(time() - $started_at, \yii\helpers\Console::FG_RED) . PHP_EOL;
            // run task fist inline
            if ($run) {
                ExtendedLogger::$logStorage = [];
                SchedulerTaskRun::run($run);
            }
        }


    }

    /**
     * @param $task_id
     * @param int $display_log
     */

    public function actionRun($task_id, $display_log = BaseController::LOG_NONE)
    {
        $this->log("Scheduler start", "white", null, $display_log);
        $task = SchedulerTask::findOne($task_id);
        if ($task) {
            try {
                $task_class = $task->class;
                /** @var  AbstractTask $task_a */
                $task_a = \Yii::createObject(
                    $task_class,
                    [
                        'logging' => true
                    ]
                );

                $task_a->setTask($task);
                $task_a->saveLastActivity(0);
                $result = $task_a->run();

                if ($task_a->getErrors()) {
                    throw  new \Exception(json_encode($task_a->getErrors()));
                }

                $this->log(json_encode($result), "yellow", null, $display_log);
            } catch (\Exception $e) {

                SchedulerTaskLog::add(1, $task->id, SchedulerTaskLog::STATUS_ERROR, $e);
                $this->log($e->getMessage(), "yellow", null, $display_log);
            }

        } else {
            $this->log("Task id " . $task_id . " NOT FOUND", "red", null, $display_log);
        }
    }
}
