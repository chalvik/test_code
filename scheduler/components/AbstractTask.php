<?php

namespace common\modules\scheduler\components;

use common\modules\scheduler\interfaces\TaskInterface;
use common\modules\scheduler\models\SchedulerTask;
use yii\base\Component;
use yii\log\Logger;
use common\modules\scheduler\interfaces\ResponseValidator;

/**
 * Abstract class for Scheduler Task
 * Class AbstractTask
 * @package common\modules\scheduler\components
 * @property integer $status
 * @property array $errors
 * @property SchedulerTask $task
 * @property boolean $logging
 */
abstract class AbstractTask extends Component implements TaskInterface
{

    /**
     * @var SchedulerTask $task  Task in scheduler
     */
    private $_task;

    /**
     * @var integer $_progress Progress work task
     */
    private $_progress = 0;

    /**
     * @var integer $_progress_count All count iteration for work of task
     */
    private $_progress_count;

    /**
     * Status success method
     * @var integer $status
     */
    private $status = false;

    /**
     * Errors after run method
     * @var array $errors
     */
    private $errors = [];

    /**
     * System logging
     * @var array $errors
     */
    public $logging = false;


    /**
     * Get Array errors after run task
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get Array errors after run task
     */
    public function addErrors($name, $message)
    {
        $error[$name] = $message;
        $this->errors[] = $error;
    }
    
    public function hasNoResponseError(ResponseValidator $validator)
    {
        return $validator->validate();
    }

    /**
     * Return status run task
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Return status run task
     * @param boolean $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Set task
     * @param SchedulerTask $task
     */
    public function setTask(SchedulerTask $task)
    {
        $this->_task = $task;
    }

    /**
     * Get task
     * @return SchedulerTask
     */
    public function getTask()
    {
        return $this->_task;
    }

    /**
     * Write log message in system Logger
     * @param string $message
     */
    protected function log($message)
    {
        $category = 'scheduler';
        \Yii::getLogger()->log($message, Logger::LEVEL_ERROR, $category);
    }

    /**
     * save Last Activity task
     * @param integer $iteration
     * @return bool
     */
    public function saveLastActivity($process = 0)
    {
        if ($this->task) return $this->task->saveLastActivity($process);
    }

    /**
     * Run Task
     * @return mixed
     */
    abstract public function run();

}
