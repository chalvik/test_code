<?php
namespace common\modules\ccpFlight\models\console;

use common\components\SSOLogin;
use common\modules\ccpFlight\models\FeatureFlight;
use common\modules\scheduler\models\ExtendedLogger;
use Yii;
use yii\base\Object;

/**
 * Class FeatureFlightParse
 * @package common\modules\ccpFlight\models\console
 */
class FeatureFlightParse extends Object
{

    public $flightFlt;

    /**
     * @return array
     */
    public function parseFeatures()
    {
        $url = Yii::$app->params['ssoFeatureFlightUrl'];
        if (empty($this->flightFlt) || empty($url)) {
            return [];
        }

        $data = [
            'flightNumber' => $this->flightFlt,
            'actualDate' => time() * 1000,
        ];

        $sso = new SSOLogin();
        $result = $sso->request($url, $data);
        if (!is_array($result) || count($result) == 0) {
            return [];
        }

        $models = [];
        foreach ($result as $array) {
            ExtendedLogger::storeLog("FEATURES FLIGHT LOG");
            ExtendedLogger::storeLog($array);

            FeatureFlight::deleteAll(['id' => $array['id']]);
            $model = new FeatureFlight([
                'id' => $array['id'],
                // as external note
                'type' => FeatureFlight::TYPE_EXTERNAL,
                'flight_flt' => $array['flightNumber'],
                'note' => $array['note'],
                'start_date' => date('Y-m-d H:m:i', $array['startDate']/1000),
                'end_date' => date('Y-m-d H:m:i', $array['endDate']/1000),
            ]);
            $model->save();
            $models[] = $model;
        }

        return $models;
    }

}
