<?php

namespace common\modules\ccpFlight\widgets;


use common\modules\ccpFlight\models\renderers\FlightBriefingRenderer;

class BriefingWidget extends \yii\base\Widget
{

    public $renderer;

    public function init()
    {
        if (!$this->renderer) throw new \Exception(' property renderer must be set');

        parent::init();
    }

    public function run()
    {
      return  $this->render('briefing',['model' => $this->renderer]);
    }

    public function renderInString()
    {
        return  $this->render('briefing',['model' => $this->renderer]);

    }

}