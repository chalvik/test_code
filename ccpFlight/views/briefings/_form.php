<?php

use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\ccpFlight\models\FlightBriefings */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="flight-briefings-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'std')->widget(DatePicker::classname(), [
        'options' => ['placeholder' => 'Enter  date  ...'],
        'pluginOptions' => [
            'todayHighlight' => true,
            'todayBtn' => true,
            'format' => 'yyyy-mm-dd',
            'autoclose' => true,
        ]
    ])->label("Плановая дата отправления"); ?>


    <?= $form->field($model, 'flt')->textInput() ?>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
