<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\modules\ccpFlight\models\search\FlightRefusalFoodSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('FlightRefusalFood', 'Flight Refusal Foods');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-refusal-food-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?= $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('FlightRefusalFood', 'Create Flight Refusal Food'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute' => 'flight_id',
                'format' => 'raw',
                'value' => function ($data) {
                    return Html::a($data->flight_id,
                        [
                            '/admin/flight/default/index',
                            'FlightSearch[id]' => $data->flight_id,
                        ],
                        [
                            'data-pjax' => 0,
                        ]
                    );
                }
            ],
            'roster_id',
            'crewMemberFullName',
            'role',
            //'status',
            //'created_at',
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
