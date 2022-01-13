<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\scheduler\models\SchedulerTaskRun */

$this->title = Yii::t('scheduler', 'Create Scheduler Task Run');
$this->params['breadcrumbs'][] = ['label' => Yii::t('scheduler', 'Scheduler Task Runs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scheduler-task-run-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
