<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\scheduler\models\SchedulerTaskRun */

$this->title = Yii::t('scheduler', 'Update {modelClass}: ', [
    'modelClass' => 'Scheduler Task Run',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('scheduler', 'Scheduler Task Runs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('scheduler', 'Update');
?>
<div class="scheduler-task-run-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
