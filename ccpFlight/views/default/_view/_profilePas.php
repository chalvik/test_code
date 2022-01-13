<?php

/* @var $this \yii\web\View */

use common\modules\EdbPassenger\models\EdbPassenger;
use common\modules\EdbPassenger\models\helpers\EdbPassengerExtractor;
use common\modules\ccpProfilePassenger\models\ProfilePassenger;
use yii\grid\GridView;
use yii\helpers\Url;

$items = ProfilePassenger::find();

$dataProvider = new \yii\data\ActiveDataProvider(['query' => $items, 'pagination' => false]);

echo GridView::widget([
    'dataProvider' => $dataProvider,
]);
