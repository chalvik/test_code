<?php

/* @var $this \yii\web\View */

use common\modules\EdbPassenger\models\helpers\EdbPassengerExtractor;
use yii\grid\GridView;
use yii\helpers\Url;
use common\modules\food\models\PassengerMeal;

$items = PassengerMeal::find();

$dataProvider = new \yii\data\ActiveDataProvider(['query' => $items, 'pagination' => false]);

echo GridView::widget([
  'dataProvider' => $dataProvider,
]);
