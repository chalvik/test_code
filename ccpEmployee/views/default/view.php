<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\components\PSqlDecoder;
/* @var $this yii\web\View */
/* @var $model common\modules\ccpEmployee\models\Employee */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('employee', 'Employees'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employee-view">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('employee', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('employee', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('employee', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="row">

        <div class="col-md-3">

            <!-- Profile Image -->
            <div class="box box-primary">
                <div class="box-body box-profile">

                    <?=Html::img(['get','id'=>$model->file_id], ['class' => "profile-user-img img-responsive"]) ?>

<!--                    <img class="profile-user-img img-responsive img-circle" src="../../dist/img/user4-128x128.jpg" alt="User profile picture">-->

                    <h3 class="profile-username text-center"><?=$model->fio_rus?></h3>

                    <p class="text-muted text-center"><?=$model->quals_list?></p>

                    <ul class="list-group list-group-unbordered">
                        <li class="list-group-item">
                            <b><?=$model->port_base?></b>
                        </li>
                        <li class="list-group-item">
                            <b><?=$model->block_hours?></b>
                        </li>
                    </ul>

                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>

        <div class="col-md-9">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'roster_id',
                    'name',
                    'fio_eng',
                    'fio_rus',
                    'quals_list',
                    'langs_list',
                    'port_base',
                    'block_hours',
                    [
                        'attribute' => 'crewcatidx',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $crewcatidx = PSqlDecoder::decodeArray($model->crewcatidx);
                            $crewcatidx = implode(", ", $crewcatidx);
                            return $crewcatidx;
                        }
                    ],
                    'created_at',
                    'updated_at',
                ],
            ]) ?>
        </div>


    </div>
</div>
