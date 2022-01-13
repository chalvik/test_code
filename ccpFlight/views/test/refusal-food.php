<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;

$this->title='Features Flight';
$this->params['breadcrumbs'][] = ['label' => 'Aims Oracle Views','url' => ['/admin/aims']];
$this->params['breadcrumbs'][] = ['label' => $this->title];

?>

<div class="row api">
    <div class="col-md-12">
        <h1><?= Html::encode($this->title) ?></h1>
        <h2>Запрос к серверу </h2>
        <?php $form = ActiveForm::begin(); ?>

        <div>
            <label>Параметры </label>
            <div>
                <?=Html::label('Локальная дата рейса: '); ?>
                <?=Html::input('text', 'date', $date); ?>
            </div>
            <div>
                <?=Html::label('Номер рейса: '); ?>
                <?=Html::input('text', 'flt',$flt); ?>
            </div>
        </div>
        OR
        <div>
            <div>
                <?=Html::label('Id рейса: '); ?>
                <?=Html::input('text', 'flt_id',$flt_id); ?>
            </div>
        </div>


        <div class="form-group">
            <?= Html::submitButton(' Отправить ', ['class' => 'btn btn-primary']) ?>
        </div>

        <div>
            <h2>Данные   </h2>

            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#request"> Запроса </a></li>
            </ul>

            <div class="tab-content">
                <div id="request" class="tab-pane fade in active" style="">
                    <?php
                    echo "<pre>";
                    print_r($params);
                    echo "</pre>";
                    ?>
                </div>
            </div>

        </div>
        <div>
            <h2>Ответ </h2>

            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#request"> Ответ </a></li>
            </ul>

            <div class="tab-content">
                <div id="request" class="tab-pane fade in active" style="">
                    <?php
                    echo "<pre>";
                    print_r($request);
                    echo "</pre>";
                    ?>
                </div>
            </div>

        </div>


        <?php ActiveForm::end(); ?>
    </div>
    <div class="col-md-6">

    </div>
</div>

