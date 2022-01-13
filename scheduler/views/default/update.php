<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\scheduler\models\SchedulerTask */

$this->title = Yii::t('scheduler', 'Update {modelClass}: ', [
    'modelClass' => 'Scheduler Task',
]) . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('scheduler', 'Scheduler Tasks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('scheduler', 'Update');
?>
<div class="scheduler-task-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
