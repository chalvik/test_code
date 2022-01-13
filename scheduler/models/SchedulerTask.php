<?php

namespace common\modules\scheduler\models;

use common\models\base\BaseActiveRecord;
use common\modules\ccpUser\models\User;
use Yii;

/**
 * Задачи планировщика (SchedulerTask)
 * класс работающий с ActiveRecord объекта и связями с этим объектом
 *
 * This is the model class for table "scheduler_task".
 * Class SchedulerTask
 * @package common\modules\scheduler\models
 * @property integer $id
 * @property string $title
 * @property string $class
 * @property integer $period
 * @property integer $status
 * @property integer $user_created_id
 * @property integer $user_updated_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $last_activity_at
 * @property string $last_addline_at
 * @property string $last_run_at
 * @property integer $progress;
 * @property integer $priority;
 * @property boolean $enable_log
 * @property SchedulerTaskRun[] $schedulerTaskRuns
 */

class SchedulerTask extends BaseActiveRecord
{
    /**
     * Статус новый
     */
    const STATUS_NEW  = 0;
    /**
     * Статус в работе
     */

    const STATUS_RUN  = 1;

    /**
     * Статус остановлен
     */
    const STATUS_STOP = 2;

    /**
     * @var array
     */
    public static $statuses = [
        self::STATUS_NEW    =>  "New task",
        self::STATUS_RUN    =>  "Run task",
        self::STATUS_STOP   =>  "Stop task",
    ];
    
    private $namespace_task = "\\common\\modules\\scheduler\\tasks\\";
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'scheduler_task';
    }

    /**
     * @inheritdoc
     */

    public function rules()
    {
        return [
            
            [['period', 'user_created_id', 'user_updated_id','status', 'progress','priority'], 'integer'],
            [['period'], 'required'],
            [['period'], 'number' ,'min' => 5],
            [['created_at', 'updated_at','last_activity_at','last_run_at','last_addline_at'], 'safe'],
            [['title', 'class'], 'string', 'max' => 255],
            [['class'], 'validateClass'],
            [['class','title'], 'unique'],
            [['status'],'default','value' => self::STATUS_NEW],
            [['last_activity_at','last_run_at','last_addline_at'],'default','value'=>'1980-01-01 00:00:00'],
            [['progress','priority'],'default','value' => 0],
            [['enable_log'],'boolean'],
            [['enable_log'],'default','value' => false],

        ];
    }

    /**
     * Валидация ссылки на класс задачи
     * @param $attribute
     * @param $params
     */
    public function validateClass($attribute, $params)
    {
        $class_loc = $this->namespace_task.$this->class;
        try {
            if (! class_exists($this->class)) {
                if (class_exists($class_loc)) {
                    $this->class = $class_loc;
                } else {
                    $this->addError($attribute, " Class $this->class OR $class_loc  not found ");
                }
            }

            if (! is_subclass_of($this->class, 'common\modules\scheduler\interfaces\TaskInterface')) {
                $this->addError($attribute, " Class $this->class  does not   common\modules\scheduler\interfaces\TaskInterface ");
            }
        } catch (\Exception $e) {
            $this->addError($attribute, $e->getMessage());
        }
    }
          

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'        => Yii::t('scheduler', 'ID'),
            'title'     => Yii::t('scheduler', 'Title'),
            'class'     => Yii::t('scheduler', 'Class'),
            'period'    => Yii::t('scheduler', 'Period (minute)'),
            'progress'  => Yii::t('scheduler', 'Progress'),
            'user_created_id' => Yii::t('scheduler', 'User Created ID'),
            'user_updated_id' => Yii::t('scheduler', 'User Updated ID'),
            'created_at'    => Yii::t('scheduler', 'Created At'),
            'updated_at'    => Yii::t('scheduler', 'Updated At'),
            'last_run_at'   => Yii::t('scheduler', 'Last Run At'),
            'last_activity_at'  => Yii::t('scheduler', 'Last Activity At'),
            'last_addline_at'   => Yii::t('scheduler', 'Last Addline At'),
        ];
    }

    /**
     * Сохраняет время добавления задачи в очередь
     * relation  with SchedulerTaskRun
     * @return bool|\yii\db\ActiveQuery
     */
    public function saveLastAddLine()
    {
        $this->progress = 0;
        $this->last_activity_at = gmdate("Y-m-d H:i:s");
        $this->last_addline_at =  gmdate("Y-m-d H:i:s");
        return $this->save(false, ['last_activity_at','last_addline_at', 'progress']);
    }

    /**
     * Сохраняет время последней активности задачи
     * relation  with SchedulerTaskRun
     * @param integer $progress
     * @return boolean
     */

    public function saveLastActivity($progress = 0)
    {
        $this->progress = ($progress)?$progress:0;
        $this->last_activity_at = gmdate("Y-m-d H:i:s");
        return $this->save(false, ['last_activity_at','progress']);
    }

    /**
     * Сохраняет время последнего запуска задачи
     * @return bool
     */

    public function saveLastRun()
    {
        $this->progress = 0;
        $this->last_activity_at = gmdate("Y-m-d H:i:s");
        $this->last_run_at =  gmdate("Y-m-d H:i:s");
        return $this->save(false, ['last_activity_at','last_run_at']);
    }
    

    /**
     * Связь на очередь задач
     * relation  with SchedulerTaskRun
     * @return \yii\db\ActiveQuery
     */
    public function getRuns()
    {
        return $this->hasMany(SchedulerTaskRun::className(), ['task_id' => 'id']);
    }

    /**
     * Связь на очередь в процессе выполнения
     * relation  with SchedulerTaskRun
     * @return \yii\db\ActiveQuery
     */
    public function getInLine()
    {
        return $this->hasOne(SchedulerTaskRun::className(), ['task_id' => 'id'])
        ->where(['status'=>[SchedulerTaskRun::STATUS_WAITING,SchedulerTaskRun::STATUS_DURING ]]);
    }

    /**
     * СВязь на последнюю запись с очереди задач, выполненную, или выполненную с ошибкой
     * relation  with SchedulerTaskRun  one last record
     * @return \yii\db\ActiveQuery
     */
    public function getLastRun()
    {
        return $this->hasOne(SchedulerTaskRun::className(), ['task_id' => 'id'])
                ->where(['status'=>[SchedulerTaskRun::STATUS_SUCCESS,SchedulerTaskRun::STATUS_ERROR ]])
                ->orderBy(['id'=>'ASC']);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            if (isset(Yii::$app->user->identity->id)) {
                /** @var User $user */
                $user = Yii::$app->user->identity;
                $this->user_created_id = isset($user->id) ? $user->id : 0;
            }
        }
        return  parent::beforeSave($insert);
    }

    /**
     * Добавлет задачу в очередь
     * Add in line current task
     * @return bool
     */

    public function addInLine()
    {
        $result = SchedulerTaskRun::AddinLine($this);
        if ($result) {
            SchedulerTaskLog::add(
                0,
                $this->id,
                SchedulerTaskLog::STATUS_ERROR_ADD_IN_LINE,
                "Error add task $this->id -> ".json_encode($result)
            );
            return false;
        } else {
            SchedulerTaskLog::add(
                0,
                $this->id,
                SchedulerTaskLog::STATUS_SUCCESS_IN_LINE,
                "add task $this->id"
            );
            return true;
        }
    }

    /**
     * Удаляет задачу из очереди
     * Remove current task from line
     * @return bool
     */

    public function removeFromLine()
    {
         $line = SchedulerTaskRun::find()
            ->where([
                'task_id' => $this->id
            ])
            ->one();
          return ($line)?$line->delete():false;
    }


    /**
     * Добавляет зщадачи в очередь выполнения с учетом периода выполнения
     * Add inline tasks for period time  task
     * @return bool
     */
    public static function addTasksinLine()
    {
        SchedulerTaskRun::setErrorTimeExpired();
        $current_date = gmdate("Y-m-d H:i:s");
                
        $TaskRunQueryWT = SchedulerTaskRun::find()
                ->from(['r' => 'scheduler_task_run'])
                ->where('t.id  = r.task_id')
                ->andWhere(['<>','r.status',SchedulerTaskRun::STATUS_WAITING])
                ->andWhere(['<>','r.status',SchedulerTaskRun::STATUS_DURING]);

        
        $tasks = self::find()
                ->from(['t' => 'scheduler_task'])
//                ->where(['exists',$TaskRunQuery])
                ->where("extract ('epoch' from ( '$current_date'::timestamp-t.last_run_at::timestamp )) >= t.period")
                ->orWhere(['not exists',$TaskRunQueryWT])
                ->andWhere(['t.status'=> SchedulerTask::STATUS_RUN])
                ->all();

        /** @var SchedulerTask $task */
        foreach ($tasks as $task) {
            $task->addInLine();
        }
        return true;
    }
}
