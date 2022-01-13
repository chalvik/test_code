<?php

namespace common\modules\scheduler\models;

use common\modules\scheduler\components\AbstractTask;
use Yii;
use yii\db\ActiveRecord;

/**
 * Очередь выполнения задач (SchedulerTaskRun)
 * класс работающий с ActiveRecord объекта и связями с этим объектом
 *
 * Class SchedulerTaskRun
 * @package common\modules\scheduler\models
 * @property integer $id
 * @property integer $task_id
 * @property string $started_at
 * @property string $finished_at
 * @property integer $status
 * @property integer $priority
 * @property string $extended_log
 * @property SchedulerTask $task
 */
class SchedulerTaskRun extends ActiveRecord
{

    const USE_PRIORITY = true;
    /**
     * Ошибка выполнения задачи
     */
    const STATUS_ERROR = -1;

    /**
     * Ошибка выполнения задачи и перевод в ожидание
     */
    const STATUS_FORCE_STOPPED = -2;
    /**
     * Задача ожидает выполнения
     */
    const STATUS_WAITING = 0;
    /**
     * Задача в процессе выполнения
     */
    const STATUS_DURING = 1;
    /**
     * Задача выполнена
     */
    const STATUS_SUCCESS = 2;

    /**
     * Снятие задачи если последняя активность ее была больше данного времени
     */

    const TIME_OUT_LAST_ACTIVE = 60 * 15;
    /**
     * Снятие задачи если она выполняется более данного времени
     */

    const TIME_OUT_LAST_RUN = 60 * 40;


    /**
     * Массив  статусов
     * List statuses
     * @var array
     */
    public static $statuses = [
        self::STATUS_ERROR => "Error",
        self::STATUS_WAITING => "Waiting",
        self::STATUS_DURING => "IN Process",
        self::STATUS_SUCCESS => "Success",
        self::STATUS_FORCE_STOPPED => "Остановленная",
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'scheduler_task_run';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

            [['task_id', 'status', 'priority'], 'integer'],
            [['task_id'], 'required'],
            ['task_id', 'exist', 'targetClass' => '\common\modules\scheduler\models\SchedulerTask', 'targetAttribute' => 'id'],
            [['started_at', 'finished_at'], 'safe'],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => SchedulerTask::className(), 'targetAttribute' => ['task_id' => 'id']],
            [['status'], 'default', 'value' => self::STATUS_WAITING],
            [['status'], 'in', 'range' => [self::STATUS_DURING, self::STATUS_ERROR, self::STATUS_SUCCESS, self::STATUS_WAITING]],
            [['task_id'], 'validateTaskInLine'],

        ];
    }

    /**
     * Валидация добавления задачи в очередь
     * @param $attribute
     * @param $params
     */

    public function validateTaskInLine($attribute, $params)
    {
        $count = 0;
        if ($this->status == self::STATUS_WAITING) {
            $count = self::find()
                ->where([
                    'task_id' => $this->task_id,
                    'status' => [self::STATUS_WAITING, self::STATUS_DURING]
                ])
                ->count();
        }
        if ($count) {
            $this->addError($attribute, " Task does not add in line. Task repeat in line. ");
        }
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('scheduler', 'ID'),
            'task_id' => Yii::t('scheduler', 'Task ID'),
            'started_at' => Yii::t('scheduler', 'Started At'),
            'finished_at' => Yii::t('scheduler', 'Finished At'),
            'status' => Yii::t('scheduler', 'Status'),
        ];
    }


    /**
     * Связь на задачу
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(SchedulerTask::className(), ['id' => 'task_id']);
    }

    /**
     * Обновляет статус записи в очереди выполнения
     * Update status Run Task
     * @param integer $status
     * @return boolean
     */
    public function updateStatus($status)
    {
        $output = false;
        if (isset(self::$statuses[$status])) {
            $this->status = $status;
            if (in_array($status, [self::STATUS_ERROR, self::STATUS_SUCCESS])) {
                $this->finished_at = gmdate("Y-m-d H:i:s");
                $output = $this->save(['finished_at', 'status']);
            }
            if (in_array($status, [self::STATUS_DURING])) {
                $this->started_at = gmdate("Y-m-d H:i:s");
                $output = $this->save(['started_at', 'status']);
            }
        }
        return $output;
    }


    /**
     * Добавляет задачу в очередь выполнения
     * return SchedulerTaskRun first in line schedule
     * @param \common\modules\scheduler\models\SchedulerTask $task Task
     * @return boolean errors
     */
    public static function addInLine(SchedulerTask $task)
    {
        $model = new self();
        $model->task_id = $task->id;
        // inherit priority
        $model->priority = $task->priority;
        $model->save();
        $task->SaveLastAddLine();
        return (count($model->errors)) ?: false;
    }

    /**
     * Возвращает крайную запись в режиме ожидания из очереди
     * return SchedulerTaskRun first in line schedule
     * @return SchedulerTaskRun
     */

    public static function firstInLine()
    {
        /** @var SchedulerTaskRun $model */
        $query = self::find()
            ->where([
                'status' => self::STATUS_WAITING
            ]);

        if (self::USE_PRIORITY) {
            $query->orderBy([
                'priority' => SORT_DESC,
                'id' => SORT_ASC
            ]);
        } else {
            $query->orderBy(['id' => 'ASC']);
        }
        $model = $query->one();

        return $model;
    }

    /**
     * Метод ставит задачу в очередь и выполняет крайнюю задачу из очереди
     * @param SchedulerTaskRun $run
     */
    public static function run(SchedulerTaskRun $run)
    {
        if (self::CheckTask($run)) {
            SchedulerTaskLog::add(
                $run->id,
                $run->task_id,
                SchedulerTaskLog::STATUS_ERROR,
                "Dublicate run task $run->task_id"
            );
        } else {
            self::process($run);
        }
    }

    /**
     * Проверяет задачу , на  то что выполняется она сейчас или нет
     * перед запуском
     * Check Task before Run
     * @return integer
     */
    private static function checkTask(SchedulerTaskRun $run)
    {
        return self::find()
            ->where([
                'status' => self::STATUS_DURING,
                'task_id' => $run->task_id
            ])
            ->count();
    }

    /**
     * выполняет задачу, записывает логи о выполнении
     * @param SchedulerTaskRun $run
     */
    private static function process(SchedulerTaskRun $run)
    {
        $run->updateStatus(self::STATUS_DURING);

        try {
            $task_class = $run->task->class;

            /** @var  AbstractTask $task */
            $task = Yii::createObject(
                $task_class,
                [
                    'logging' => true
                ]
            );
            $task->setTask($run->task);
            $run->task->saveLastRun();
            $task->run();

            // write extended log if enabled
            if ($run->task->enable_log) {
                $extended_log = implode("<br>",ExtendedLogger::$logStorage);
            } else {
                $extended_log = '';
            }

            if (!$task->getErrors()) {
                $message = "Success: ok";
                $run->updateStatus(self::STATUS_SUCCESS);
                SchedulerTaskLog::add($run->id, $run->task_id, SchedulerTaskLog::STATUS_SUCCESS, $message, $extended_log);
            } else {
                $message = json_encode($task->getErrors());
                $run->updateStatus(self::STATUS_ERROR);
                SchedulerTaskLog::add($run->id, $run->task_id, SchedulerTaskLog::STATUS_ERROR, $message, $extended_log);
            }
        } catch (\Exception $e) {

            // write extended log if enabled
            if ($run->task->enable_log) {
                $extended_log = implode("<br>",ExtendedLogger::$logStorage);
            } else {
                $extended_log = '';
            }
            $run->updateStatus(self::STATUS_ERROR);
            SchedulerTaskLog::add($run->id, $run->task_id, SchedulerTaskLog::STATUS_ERROR, $e->getMessage(), $extended_log);
        }
    }

    /**
     * Устанавливает статус задачи в просроченные через self::TIME_OUT_LAST_ACTIVE  минут после подвисания
     *  или после self::TIME_OUT_LAST_RUN + 2 минуты после запуска
     * Это если задача перестала выполнятся "подвисла"
     * Set time Expired and task status to error
     */

    public static function setErrorTimeExpired()
    {
        $current_date = gmdate("Y-m-d H:i:s");
        $timeExpired = self::TIME_OUT_LAST_ACTIVE;
        /**
         * Добавляем время чтоб удостоверится что задача уже не выполняется
         */
        $timeExpiredRun = self::TIME_OUT_LAST_RUN + 2 * 60;

        $TaskExpired = SchedulerTask::find()
            ->from(['t' => 'scheduler_task'])
            ->where('t.id  = r.task_id')
            ->andWhere("extract('epoch' from ( '$current_date'::timestamp-t.last_activity_at::timestamp)) >= $timeExpired");

        $error_runs = self::find()
            ->from(['r' => 'scheduler_task_run'])
            ->where(['exists', $TaskExpired])
            ->andWhere(['r.status' => self::STATUS_DURING])
            ->all();

        foreach ($error_runs as $run) {
            SchedulerTaskLog::add($run->id, $run->task_id, SchedulerTaskLog::STATUS_ERROR, json_encode(['error' => 'Time Expired']));
            $run->finished_at = gmdate("Y-m-d H:i:s");
            $run->status = self::STATUS_ERROR;
            $run->save(false);
        }
    }
}
