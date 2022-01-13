<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\modules\scheduler\models\SchedulerTask;

/* @var $this yii\web\View */
/* @var $model common\modules\scheduler\models\SchedulerTask */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="scheduler-task-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'class')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList(SchedulerTask::$statuses,['prompt'=>'Select Status']) ?>
    
    <?= $form->field($model, 'period')->textInput(["type"=>'number']) ?>
    <?= $form->field($model, 'priority')->textInput() ?>
    <?= $form->field($model, 'enable_log')->checkbox() ?>

    <?php // = $form->field($model, 'user_created_id')->textInput() ?>

    <?php // = $form->field($model, 'user_updated_id')->textInput() ?>

    <?php // = $form->field($model, 'created_at')->textInput() ?>

    <?php // = $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('scheduler', 'Create') : Yii::t('scheduler', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
