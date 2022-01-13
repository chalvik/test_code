<?php
namespace common\modules\ccpFlight\controllers;

use common\components\BaseController;
use common\modules\ccpFlight\models\console\FeatureFlightParse;
use Yii;
use yii\data\ArrayDataProvider;

/**
 * Контроллер для админки разработчика (backend)
 * реализует методы для работы, с рейсами совместной эксплуатации
 *
 * FeatureFlightController implements the CRUD actions for FeatureFlight model.
 * Class FeatureFlightController
 * @package common\modules\ccpFlight\controllers
 * @property string $modelName
 */
class FeatureFlightController extends BaseController
{

    public $modelName = 'FeatureFlight';

    public function actionTest()
    {
        $dataProvider = new ArrayDataProvider([
            'allModels' => [],
        ]);

        $flt = Yii::$app->request->post('flightFlt');
        $parser = new FeatureFlightParse();
        $parser->flightFlt = $flt;
        $models = $parser->parseFeatures();

        $dataProvider->allModels = $models;

        return $this->render('test', ['dataProvider' => $dataProvider, 'flightFlt' => $flt]);
    }

}
