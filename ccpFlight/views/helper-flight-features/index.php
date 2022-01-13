<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\ccpFlight\models\helpers\HelperFlightFeaturesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Helper Flight Features';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="helper-flight-features-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Helper Flight Features', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'flight_id',
           // 'codes:ntext',
            [
                'attribute' => 'codes',
                'value' => function ($model) {
                    return (is_array($model->codes))?implode(",", $model->codes):'';
                }
            ], [
                'attribute' => 'existed_codes',
                'value' => function ($model) {
                    return  (is_array($model->existed_codes))?implode(",", $model->existed_codes):'';
                }
            ],
           // 'existed_codes:ntext',
            'has_fail_transfers:boolean',
            'created_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
