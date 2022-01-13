<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\modules\scheduler\models\SchedulerTaskLog;
use common\modules\scheduler\models\SchedulerTask;


/* @var $this yii\web\View */
/* @var $model common\modules\ccpFlight\models\search\FlightSearch */
/* @var $form yii\widgets\ActiveForm */
?>


<div>
    <a href="#" id="search-filter" class="btn btn-primary"> >>> Open Filter </a>
</div>

<div class="flight-search" style="display:none">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-md-3">

            <?= $form->field($model, 'id') ?>
            <?= $form->field($model, 'task_id')->dropDownList(ArrayHelper::map(
                    SchedulerTask::find()->all(), 'id', 'title'),
                    [
                        'prompt' => ' Выберите значение '
                    ]
                ) ?>
        </div>

        <div class="col-md-3">
            <?= $form->field($model, 'status')->dropDownList(
                    SchedulerTaskLog::$statuses,
                    [
                        'prompt' => ' Выберите значение '
                    ]
                ) ?>
            <?= $form->field($model, 'message') ?>
        </div>

        <div class="col-md-3">
            <?= $form->field($model, 'created_at') ?>
        </div>

        <div class="col-md-3">

        </div>
    </div>



    <div class="form-group">
        <?= Html::submitButton(Yii::t('flight', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('flight', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<div>
    <ul>
        <?php foreach ($model->getAttributes() as $key => $value ): ?>
            <?php if ($value): ?>
                <li>
                    <?=Html::activeLabel($model, $key)?> : <?=$value?>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</div>

