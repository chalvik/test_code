<?php


use common\modules\ccpFlight\models\FlightBriefings;
use common\modules\ccpFlight\models\renderers\FlightBriefingRenderer;
use common\modules\ccpFlight\widgets\BriefingWidget;

/** @var FlightBriefings $model */
$widget = new BriefingWidget(['renderer' => new FlightBriefingRenderer($model)]);
echo $widget->run();
?>

