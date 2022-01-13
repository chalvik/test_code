<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\scheduler\models\SchedulerTask */

$this->title = Yii::t('scheduler', 'Create Scheduler Task');
$this->params['breadcrumbs'][] = ['label' => Yii::t('scheduler', 'Scheduler Tasks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scheduler-task-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
