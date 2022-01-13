<?php

namespace common\modules\scheduler\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Логи выполнения задач планировщика (SchedulerTaskLog)
 * класс работающий с ActiveRecord объекта и связями с этим объектом
 *
 * This is the model class for table "scheduler_task_log".
 * Class SchedulerTaskLog
 * @package common\modules\scheduler\models
 * @property integer $id
 * @property integer $task_run_id
 * @property integer $status
 * @property string $message
 * @property string $extended_log
 * @property string $created_at
 */
class SchedulerTaskLog extends ActiveRecord
{
    /**
     * Ошибка добавления задачи в очередь
     */
    const STATUS_ERROR_ADD_IN_LINE = -2;
    /**
     * Ошибка выполнения задачи
     */
    const STATUS_ERROR = -1;
    /**
     * По умолчанию
     */
    const STATUS_DEFAULT = 0;
    /**
     * Задача выполнена успешно
     */
    const STATUS_SUCCESS = 1;
    /**
     * Успешно добавлена в очередь задач
     */
    const STATUS_SUCCESS_IN_LINE = 2;

    /**
     * МАссив статусов записей логов
     * @var array
     */
    public static $statuses = [
        self::STATUS_ERROR_ADD_IN_LINE => "Error Add in Line",
        self::STATUS_ERROR => "Error",
        self::STATUS_SUCCESS => "Success",
        self::STATUS_DEFAULT => "Default",
        self::STATUS_SUCCESS_IN_LINE => "Success add in line",
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'scheduler_task_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['task_run_id', 'status', 'task_id'], 'integer'],
//            ['task_run_id', 'exist', 'targetClass' => '\common\modules\scheduler\models\SchedulerTasRun','targetAttribute' => 'id'],            
            [['message', 'extended_log'], 'string'],
            [['created_at'], 'safe'],
            [['status'], 'default', 'value' => self::STATUS_DEFAULT],
            [['status'], 'in', 'range' => [self::STATUS_DEFAULT, self::STATUS_ERROR, self::STATUS_SUCCESS]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('scheduler', 'ID'),
            'task_run_id' => Yii::t('scheduler', 'Task Run ID'),
            'task_id' => Yii::t('scheduler', 'Task ID'),
            'status' => Yii::t('scheduler', 'Status'),
            'message' => Yii::t('scheduler', 'Message'),
            'created_at' => Yii::t('scheduler', 'Created At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_at = gmdate("Y-m-d H:i:s");
        }
        return parent::beforeSave($insert);
    }

    /**
     * Добавляет запись лога в базу данных
     * @param $task_run_id
     * @param $task_id
     * @param $status
     * @param $message
     * @param string $extended_log
     * @return bool|array
     */
    public static function add($task_run_id, $task_id, $status, $message, $extended_log = '')
    {
        $model = new self();
        $model->task_run_id = $task_run_id;
        $model->task_id = $task_id;
        $model->status = $status;
        $model->message = $message;
        // store extended log
        $model->extended_log = $extended_log;
        $model->save();
        return ($model->errors) ?: false;
    }

    /**
     * связь на очередь выполнения задач
     * relation  with SchedulerTaskRun
     * @return \yii\db\ActiveQuery
     */
    public function getRun()
    {
        return $this->hasOne(SchedulerTaskRun::className(), ['id' => 'task_run_id']);
    }
}
