<?php

use common\modules\ccpFlight\models\FeatureFlight;
use common\modules\ccpFlight\models\Flight;
use kartik\date\DatePicker;
use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\ccpFlight\models\FeatureFlight */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="feature-flight-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'flight_flt')->dropDownList(Flight::mapFlt(),['class' => 'form-control select2']) ?>
    <?= $form->field($model, 'type')->dropDownList(FeatureFlight::mapTypes()) ?>

    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'start_date')->widget(DateTimePicker::classname(), [
        'options' => ['placeholder' => 'Enter  date  ...'],
        'pluginOptions' => [
            'todayHighlight' => true,
            'todayBtn' => true,
            'format' => 'yyyy-mm-dd hh:ii:ss',
            'autoclose' => true,
        ]
    ])->label("Действует с"); ?>

    <?= $form->field($model, 'end_date')->widget(DateTimePicker::classname(), [
        'options' => ['placeholder' => 'Enter  date  ...'],
        'pluginOptions' => [
            'todayHighlight' => true,
            'todayBtn' => true,
            'format' => 'yyyy-mm-dd hh:ii:ss',
            'autoclose' => true,
        ]
    ])->label("По"); ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('flight', 'Create') : Yii::t('flight', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
