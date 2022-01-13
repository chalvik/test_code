<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\scheduler\models\SchedulerTaskLog */

$this->title = Yii::t('scheduler', 'Update {modelClass}: ', [
    'modelClass' => 'Scheduler Task Log',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('scheduler', 'Scheduler Task Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('scheduler', 'Update');
?>
<div class="scheduler-task-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
