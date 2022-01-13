<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\ccpFlight\models\Flight */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('flight', 'Flights'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;



?>



<?= Html::tag('h1', 'Flight: ' . $model->fltDes) ?>

<section>
    <ul class="nav nav-tabs control-sidebar-tabs">
        <li class="active">
            <a href="#flight-wid-tab" data-toggle="tab" aria-expanded="true">
                <i class="fa fa-address-card-o"></i>
            </a>
        </li>
        <li>
            <a href="#flight-report-tab" data-toggle="tab">
                <i class="fa fa-cart-arrow-down"></i>
                Отчеты на рейсе
            </a>
        </li>
        <li>
            <a href="#flight-cargo-tab" data-toggle="tab">
                <i class="fa fa-cart-arrow-down"></i>
                Загрузка на рейсе
            </a>
        </li>
        <li>
            <a href="#flight-chat-tab" data-toggle="tab">
                <i class="fa fa-mobile"></i>
                Тикеты на рейсе
            </a>
        </li>
        <li>
            <a href="#flight-detail-which-toggle="tab">
                <i class="fa fa-plane"></i>
                DetailView
            </a>
        </li>
        <li>
            <a href="#flight-menu-tab" data-toggle="tab">
                <i class="fa fa-cutlery"></i>
                Menu
            </a>
        </li>
        <li>
            <a href="#flight-passengers-tab" data-toggle="tab">
                <i class="fa fa-users"></i>
                Пассажиры
            </a>
        </li>
        <li>
            <a href="#flight-dop-tab" data-toggle="tab">
                <i class="fa fa-files-o"></i>
                Допинформация
            </a>
        </li>
        <li>
            <a href="#flight-kompens-tab" data-toggle="tab">
                <i class="fa fa-balance-scale"></i>
                Компенсационные пакеты
            </a>
        </li>
        <li>
            <a href="#flight-profilePas-tab" data-toggle="tab">
                <i class="fa fa-balance-scale"></i>
                Пассажиры Профайл
            </a>
        </li>
        <li>
            <a href="#flight-meal-tab" data-toggle="tab">
                <i class="fa fa-balance-scale"></i>
                Пассажиры Мил
            </a>
        </li>
        <li>
            <a href="#flight-cdw-tab" data-toggle="tab">
                <i class="fa fa-balance-scale"></i>
                ЦДВ Мил
            </a>
        </li>
    </ul>
</section>
<p></p>
<section>
    <div class="tab-content">

        <div id="flight-wid-tab" class="tab-pane fade in active">
            <?=$this->render('_view/_wid_tab', ['model'=>$model, 'coords'=>$coords, 'current' => $current]); ?>
        </div>

        <div id="flight-cargo-tab" class="tab-pane fade">
            <?=$this->render('_view/_cargo_tab', ['model'=>$model]); ?>
        </div>

        <div id="flight-detail-tab" class="tab-pane fade">
            <?=$this->render('_view/_detail_tab', ['model'=>$model]); ?>
        </div>

        <div id="flight-chat-tab" class="tab-pane fade">
            <?=$this->render('_view/_chat_tab', ['model'=>$model]); ?>
        </div>

        <div id="flight-menu-tab" class="tab-pane fade">
            <?=$this->render('_view/_menu_tab', ['model'=>$model]); ?>
        </div>

        <div id="flight-report-tab" class="tab-pane fade">
            <?=$this->render('_view/_report_tab', ['model'=>$model]); ?>
        </div>

        <div id="flight-passengers-tab" class="tab-pane fade">
            <?=$this->render('_view/_passengers', ['model'=>$model]); ?>
        </div>

        <div id="flight-dop-tab" class="tab-pane fade">
            <?=$this->render('_view/_dop', ['model'=>$model]); ?>
        </div>
        
        <div id="flight-kompens-tab" class="tab-pane fade">
            <?=$this->render('_view/_kompens', ['model'=>$model]); ?>
        </div>
        
        <div id="flight-profilePas-tab" class="tab-pane fade">
          <?=$this->render('_view/_profilePas', ['model'=>$model]); ?>
        </div>
        
        <div id="flight-meal-tab" class="tab-pane fade">
          <?=$this->render('_view/_meal', ['model'=>$model]); ?>
        </div>
        
        <div id="flight-cdw-tab" class="tab-pane fade">
          <?=$this->render('_view/_cdw', ['model'=>$model]); ?>
        </div>

    </div>
</section>
