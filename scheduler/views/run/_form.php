<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\modules\scheduler\models\SchedulerTaskRun;


/* @var $this yii\web\View */
/* @var $model common\modules\scheduler\models\SchedulerTaskRun */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="scheduler-task-run-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'task_id')->textInput() ?>

    <?= $form->field($model, 'started_at')->textInput() ?>

    <?= $form->field($model, 'finished_at')->textInput() ?>

    <?= $form->field($model, 'status')->dropDownList(SchedulerTaskRun::$statuses,["prompt"=>"Select status"]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('scheduler', 'Create') : Yii::t('scheduler', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
