<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\modules\scheduler\models\SchedulerTaskLog;
/* @var $this yii\web\View */
/* @var $model common\modules\scheduler\models\SchedulerTaskLog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="scheduler-task-log-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'task_run_id')->textInput() ?>

    <?= $form->field($model, 'status')->dropDownList(SchedulerTaskLog::$statuses,['prompt'=>'Select Status']) ?>

    <?= $form->field($model, 'message')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('scheduler', 'Create') : Yii::t('scheduler', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
