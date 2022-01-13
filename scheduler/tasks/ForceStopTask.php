<?php

namespace common\modules\scheduler\tasks;

use common\modules\report\models\ReportExport;
use common\modules\scheduler\components\AbstractTask;
use common\modules\scheduler\models\SchedulerTask;
use common\modules\scheduler\models\SchedulerTaskRun;

/**
 * Class TaskAirport
 * @package common\modules\scheduler\tasks
 */
class ForceStopTask extends AbstractTask
{
    const DELAY_TO_REMOVE = 1; // hours


    private $tasks_id = [];
    public static $tasksToForceStop = [
        '\common\modules\report\tasks\TaskCreateReportExcel',
    ];

    public function run()
    {

        try {
            $this->getTasks();

            // перевод силой остановленные, повисших задач более, чем на self::DELAY_TO_REMOVE
            $this->removeTasks();

        } catch (\Exception $e) {
            $this->status = false;
            $this->addErrors('exception', $e->getMessage());
        }
    }

    public function getTasks()
    {
        foreach (self::$tasksToForceStop as $task) {
            if ($foundTask = SchedulerTask::findOne(['class' => $task])) {
                $this->tasks_id[] = $foundTask->id;
            }

        }
    }

    /**
     * change status to SchedulerTaskRun::STATUS_FORCE_STOPPED
     */
    public function removeTasks()
    {
        $count = SchedulerTaskRun::updateAll([
            'status' => SchedulerTaskRun::STATUS_FORCE_STOPPED
        ],
            [
                'AND',
                ['status' => SchedulerTaskRun::STATUS_DURING],
                ['<', 'started_at', gmdate("Y-m-d H:i:s", time() - 3600 * self::DELAY_TO_REMOVE)],
                ['task_id' => $this->tasks_id],
            ]
        );

        if ($count && ($report = ReportExport::findOne(['status' => 0]))) {
            $report->status = -1;
            $report->save();
        }
    }
}
