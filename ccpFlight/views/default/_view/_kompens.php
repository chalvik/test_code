<?php

/* @var $this \yii\web\View */

use common\modules\compPackage\models\search\CompPackageSearch;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $model \common\modules\ccpFlight\models\Flight|\yii\db\ActiveRecord */

$searchModel = new CompPackageSearch();
$dataProvider = $searchModel->search(['flight_id' => $model->id]);

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'id',
        'number',
        'flight_id',
        'created_at',
        'updated_at',
        'status',
        'user_id',
        [
            'class' => 'yii\grid\ActionColumn',
            'urlCreator' => function ($action, $model, $key, $index)
            {
                $params = is_array($key) ? $key : ['id' => (string) $key];
                $params[0] = '/admin/comp-package/default/' . $action;
                return Url::toRoute($params);
            }
        ],
    ],
]);
