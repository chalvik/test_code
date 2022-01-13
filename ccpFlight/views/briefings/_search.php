<?php

use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\ccpFlight\models\search\FlightBriefingsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="flight-briefings-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'flight_id')->textInput(); ?>

        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'std')->widget(DatePicker::class, [
                'options' => ['placeholder' => 'Enter  date  ...'],
                'pluginOptions' => [
                    'todayHighlight' => true,
                    'todayBtn' => true,
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true,
                ]
            ])->label("Плановая дата отправления"); ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'flt')->textInput(); ?>
        </div>
        <div class="col-lg-3">
            <div class="form-group">
                <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
            </div>


        </div>
        <?php ActiveForm::end(); ?>

    </div>


</div>
