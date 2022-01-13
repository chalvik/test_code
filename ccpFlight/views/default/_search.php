<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
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
        'id' => 'flight_search_form'
    ]); ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'id') ?>

            <?= $form->field($model, 'day') ?>

            <?= $form->field($model, 'flt') ?>

            <?php //= $form->field($model, 'fltDes') ?>

            <?php //= $form->field($model, 'os') ?>

            <?php //= $form->field($model, 'carrier') ?>
            <?= $form->field($model, 'carrier')->dropDownList($model::listCarriers(), [
                    'prompt' => "Select carrier company ID"]
            ) ?>

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


            <?= $form->field($model, 'etd')->widget(DatePicker::class, [
                'options' => ['placeholder' => 'Enter  date  ...'],
                'pluginOptions' => [
                    'todayHighlight' => true,
                    'todayBtn' => true,
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true,
                ]
            ])->label("Расчетная  дата отправления "); ?>

            <?= $form->field($model, 'eta')->widget(DatePicker::class, [
                'options' => ['placeholder' => 'Enter  date  ...'],
                'pluginOptions' => [
                    'todayHighlight' => true,
                    'todayBtn' => true,
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true,
                ]
            ])->label("Расчетная дата прибытия"); ?>

            <?= $form->field($model, 'origin_std_date')->widget(DatePicker::class, [
                'options' => ['placeholder' => 'Enter  date  ...'],
                'pluginOptions' => [
                    'todayHighlight' => true,
                    'todayBtn' => true,
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true,
                ]
            ])->label("Оригинальное STD "); ?>

            <?= $form->field($model, 'show_flight_transfer')->checkbox() ?>

        </div>

        <div class="col-md-3">

            <?= $form->field($model, 'arr_airport_id')
                ->dropDownList($model::listAirports(), ['prompt' => "Select"]) ?>

            <?= $form->field($model, 'dep_airport_id')
                ->dropDownList($model::listAirports(), ['prompt' => "Select"]) ?>

            <?php //= $form->field($model, 'aircraft_id') ?>
            <?php echo $form->field($model, 'aircraft_id')
                ->dropDownList($model::listAircrafts(), ['prompt' => "Select"]) ?>

            <?= $form->field($model, 'crew_roster_id') ?>
        </div>

        <div class="col-md-3">
            <?= $form->field($model, 'arr_gate') ?>
            <?= $form->field($model, 'dep_gate') ?>
            <?= $form->field($model, 'deleted')->checkbox() ?>
            <?= $form->field($model, 'canceled') ?>
            <!-- <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal">
                SMART SEARCH
            </button> -->
        </div>
    </div>
    <?php //= $this->render('_search_smart', ['form' => $form, 'model' => $model]); ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('flight', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('flight', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>

<div>
    <ul>
        <?php foreach ($model->getAttributes() as $key => $value): ?>
            <?php if ($value): ?>
                <li>
                    <?= Html::activeLabel($model, $key) ?> : <?= $value ?>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</div>
