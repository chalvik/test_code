<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


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

            <?= $form->field($model, 'roster_id') ?>

            <?= $form->field($model, 'name') ?>
        </div>

        <div class="col-md-3">
            <?= $form->field($model, 'fio_eng') ?>

            <?= $form->field($model, 'fio_rus') ?>

            <?php  echo $form->field($model, 'quals_list') ?>

        </div>

        <div class="col-md-3">
            <?php  echo $form->field($model, 'langs_list') ?>

            <?php  echo $form->field($model, 'port_base') ?>

            <?php  echo $form->field($model, 'block_hours') ?>
        </div>

        <div class="col-md-3">
            <?php  echo $form->field($model, 'crewcatidx') ?>

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


