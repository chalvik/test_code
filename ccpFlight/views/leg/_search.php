<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\modules\ccpAirport\models\Airport;
use kartik\date\DatePicker;



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

            <?= $form->field($model, 'day') ?>

            <?= $form->field($model, 'flt') ?>

            <?= $form->field($model, 'carrier') ?>

        </div>

        <div class="col-md-3">

            <?= $form->field($model, 'std')->widget(DatePicker::classname(), [
                'options' => ['placeholder' => 'Enter  date  ...'],
                'pluginOptions' => [
                    'todayHighlight' => true,
                    'todayBtn' => true,
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true,
                ]
            ])->label("Плановая  дата отправления"); ?>

            <?= $form->field($model, 'sta')->widget(DatePicker::classname(), [
                'options' => ['placeholder' => 'Enter  date  ...'],
                'pluginOptions' => [
                    'todayHighlight' => true,
                    'todayBtn' => true,
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true,
                ]
            ])->label("Плановая  дата прибытия"); ?>


            <?=  $form->field($model, 'etd')->widget(DatePicker::classname(), [
                'options' => ['placeholder' => 'Enter  date  ...'],
                'pluginOptions' => [
                    'todayHighlight' => true,
                    'todayBtn' => true,
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true,
                ]
            ])->label("Расчетная  дата отправления "); ?>

            <?= $form->field($model, 'eta')->widget(DatePicker::classname(), [
                'options' => ['placeholder' => 'Enter  date  ...'],
                'pluginOptions' => [
                    'todayHighlight' => true,
                    'todayBtn' => true,
                    'format' => 'yyyy-M-dd',
                    'autoclose' => true,
                ]
            ])->label("Расчетная дата прибытия"); ?>

        </div>

        <div class="col-md-3">

            <?php $airports = ArrayHelper::map(Airport::find()->all(), 'id', 'iata'); ?>

            <?= $form->field($model, 'arr_airport_id')
                ->dropDownList($airports, ['prompt'=>"Select"]) ?>

            <?= $form->field($model, 'dep_airport_id')
                ->dropDownList($airports, ['prompt'=>"Select"]) ?>

            <?= $form->field($model, 'aircraft_id') ?>
            <?php /* $form->field($model, 'aircraft_id')
                ->dropDownList(ArrayHelper::map(Aircraft::find()->all(), 'id', 'iata'),['prompt'=>"Select"]) */ ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('flight', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('flight', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
