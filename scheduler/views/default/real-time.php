<?php

use yii\helpers\Html;
use bburim\flot\Chart as Chart;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\scheduler\models\search\SchedulerTaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('scheduler', 'Scheduler Tasks Real Time');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scheduler-task-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
<?php
    echo Chart::widget([
        'data' => [
            [
                'label' => 'Task run',
                'data'  => [
                    [1, 1],
                    [2,7],
                    [3,12],
                    [4,32],
                    [5,62],
                    [6,150],
                ],
                'lines'  => ['show' => true],
                'points' => ['show' => true],
                ],
//                [
//                  'label' => 'bars',
//                    'data'  => [
//                        [1,12],
//                        [2,16],
//                        [3,89],
//                        [4,44],
//                        [5,38],
//                    ],
//                    'bars' => ['show' => true],
//                ],
        ],
        'options' => [
            'legend' => [
                'position'          => 'nw',
                'show'              => true,
                'margin'            => 10,
                'backgroundOpacity' => 0.5
            ],
        ],
        'htmlOptions' => [
            'style' => 'width:100%;height:400px;'
        ]
    ]);


?>
</div>
