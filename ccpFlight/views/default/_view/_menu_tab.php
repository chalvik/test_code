<?php
/**
 * Created by PhpStorm.
 * User: Chernogor Alexey
 * Date: 08.12.17
 * Time: 15:05
 */

use yii\helpers\Html;

?>


<div class="row">
    <span class="info-box-text">
        loc-STA:
        <?= gmdate("Y-m-d- H:i:s", $model->staAirport) ?>
    </span>
    <span class="info-box-text">
        hour:
        <?= gmdate("G", $model->staAirport) ?>
    </span>
    <?php if (isset($model->menu->id)) : ?>
    <p> Title:  <?=Html::a($model->menu->title, ['/admin/food/menu/view', 'id'=>$model->menu->id]) ?> </p>
    <p> Id:  <?= $model->menu->id ?></p>

    <?php else :?>
        <p>Меню не установлено </p>
    <?php endif; ?>
</div>