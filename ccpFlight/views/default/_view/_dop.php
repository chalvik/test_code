<?php

/* @var $this \yii\web\View */

use yii\data\ActiveDataProvider;
use yii\grid\GridView;

/* @var $model \common\modules\ccpFlight\models\Flight|\yii\db\ActiveRecord */

$additionalsByFlightQuery = Yii::$app->AdditionalsByFlight->GetAdditionalsByFlight($model->id);
$dataProvider = new ActiveDataProvider([
  'query' => $additionalsByFlightQuery,
]);

echo GridView::widget([
  'dataProvider' => $dataProvider,
  'columns' => [
    ['class' => 'yii\grid\SerialColumn'],
    'title',
    'text',
    'created_at',
    'updated_at',
    'date_from',
    'date_to',
    'carrier',
    'is_inter',
    'file_id',
  ],
]);