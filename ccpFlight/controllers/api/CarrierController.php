<?php
namespace common\modules\ccpFlight\controllers\api;

use api\rest\RestController;
use common\modules\ccpFlight\models\Carrier;
use common\modules\ccpUser\models\User;
use Yii;
use yii\filters\AccessControl;

/**
 * Класс реализует методы для обработки апи запросов
 * для справочника компаний
 *
 * This is the api controller class for carrier.
 * Class CarrierController
 * @package common\modules\ccpFlight\controllers\api
 */
class CarrierController extends RestController
{
    /**
     * @return array
     */
    public function behaviors()
    {
        $parent = parent::behaviors();

        $parent['access'] = [
            'class' => AccessControl::className(),
            'except' => ['options'],
            'rules' => [
                [
                    'actions' => ['index'],
                    'allow' => true,
                    'roles' => ['@'],
                ]
            ],
        ];
        return $parent;
    }

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        $verbs = parent::verbs();
        $verbs['index'] = ['GET', 'HEAD'];
        return $verbs;
    }

    // TO DO нужно немного доработать третий вариант, учитывая роли пользователя , плюс все
    /**
     * Возвращает список компаний
     * @param int $view
     * @return array
     */
    public function actionIndex($view = Carrier::TYPE_VIEW_ONLY_CARRIER)
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;
        $carriers = Carrier::findAll($view);
        if ($user->type == User::TYPE_IOS || $view == Carrier::TYPE_VIEW_ALL) {
            return $carriers;
        } else {
            $result = [];
            $userCarriers = $user->getCarriers();

            if (in_array(Carrier::CARRIER_ALL, $userCarriers)) {
                $userCarriers = [Carrier::CARRIER_S7, Carrier::CARRIER_GH];
            }

            foreach ($carriers as $carrier) {
                if (in_array($carrier->id, $userCarriers)) {
                    $result[] = $carrier;
                }
            }
            return $result;
        }
    }
}
