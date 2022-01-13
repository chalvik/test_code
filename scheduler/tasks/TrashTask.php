<?php

namespace common\modules\scheduler\tasks;


use common\modules\ccpFlight\models\FlightBriefings;
use common\modules\ccpNotification\models\Notification;
use common\modules\compPackage\models\CompPackageLog;
use common\modules\compPackage\models\CompPackagePassengerLog;
use common\modules\EdbPassenger\models\EdbPassengerTransfer;
use common\modules\food\models\FoodSurplusLog;
use common\modules\LogRefresh\models\LogRefresh;
use common\modules\report\models\Report;
use common\modules\report\models\ReportLog;
use common\modules\scheduler\components\AbstractTask;
use common\modules\scheduler\models\ExtendedLogger;
use common\modules\statistics\models\search\StatisticsItemSearch;
use common\modules\statistics\models\StatisticsItem;
use common\modules\storagefiles\models\Storagefiles;
use common\modules\feedback\models\FeedbackLog;
use common\modules\food\models\FoodLog;
use common\modules\cargo\models\CargoSdLog;
use common\modules\cargo\models\SpecialCargo;

/**
 * Class TaskAirport
 * @package common\modules\scheduler\tasks
 */
class TrashTask extends AbstractTask
{

    public function run()
    {

        $count = 11;
        $i = 0;

//        $date = gmdate("Y-m-d H:i:s", strtotime('- 1 month'));
        try {
            $this->saveLastActivity(round($i++ / $count * 100));
            // удаление статистики
            StatisticsItem::deleteAll(['<', 'created_at', gmdate("Y-m-d H:i:s", strtotime('- 14 days'))]);

            // удаление логов
            LogRefresh::deleteAll(['<', 'created_at', gmdate("Y-m-d H:i:s", strtotime('- 1 month'))]);
            $this->saveLastActivity(round($i++ / $count * 100));

            // удаление логов Отчётов (Report)
            ReportLog::deleteAll(['<', 'log_time', strtotime('- 4 week')]);
            $this->saveLastActivity(round($i++ / $count * 100));
            
            // удаление логов Отзывов (Feedback)
            FeedbackLog::deleteAll(['<', 'log_time', strtotime('- 2 week')]);
            $this->saveLastActivity(round($i++ / $count * 100));
            
            // удаление логов Предпочтений Питания пассажиров
            FoodLog::deleteAll(['<', 'log_time', strtotime('- 2 week')]);
            $this->saveLastActivity(round($i++ / $count * 100));
            
            // удаление логов получения данных Специальных и Опасных грузов из kafka
            CargoSdLog::deleteAll(['<', 'log_time', strtotime('- 2 week')]);
            $this->saveLastActivity(round($i++ / $count * 100));
            
            // удаление данных Специальных и Опасных грузов из kafka
            SpecialCargo::deleteAll(['<', 'log_time', strtotime('- 1 month - 1 day')]);
            $this->saveLastActivity(round($i++ / $count * 100));

            // удаленние уведомлений сроком более чем 1 месяц
            Notification::deleteAll(['<', 'created_at', gmdate("Y-m-d H:i:s", strtotime('- 1 month'))]);
            $this->saveLastActivity(round($i++ / $count * 100));

            // лог остатком питания
            FoodSurplusLog::deleteAll(['<', 'created_at', gmdate("Y-m-d H:i:s", strtotime('- 1 week'))]);
            $this->saveLastActivity(round($i++ / $count * 100));

            // лог компенсационных пакетов недельной давности
            CompPackageLog::deleteAll(['<', 'created_at', gmdate("Y-m-d H:i:s", strtotime('- 1 week'))]);
            $this->saveLastActivity(round($i++ / $count * 100));

            CompPackagePassengerLog::deleteAll(['<', 'created_at', gmdate("Y-m-d H:i:s", strtotime('- 1 week'))]);
            $this->saveLastActivity(round($i++ / $count * 100));

            // удаление отчетов !!!  нестарших борт проводников сроком больше 2 дней
            Report::deleteAll(['AND',
                ['crew_pos' => Report::CREW_NOT_PU],
                //  ['is_sent' => true],
                ['<', 'created_at', gmdate("Y-m-d H:i:s", strtotime('- 2 days'))]
            ]);

            $this->saveLastActivity(round($i++ / $count * 100));

            // удаление брифинга старее, чем за 5 суток
            if ($briefings = FlightBriefings::find()->where(['<', 'created_at', gmdate("Y-m-d H:i:s", strtotime('- 5 days'))])->all()) {
                /** @var FlightBriefings $briefing */
                foreach ($briefings as $briefing) {
                    $briefing->delete();
                }
            }

            $this->saveLastActivity(round($i++ / $count * 100));

            // удаление сохраненных логов для выгрузки старее, чем 14 дней
            if ($fileLogs = Storagefiles::find()
                ->where(['<', 'created_at', gmdate("Y-m-d H:i:s", strtotime('- 14 days'))])
                ->andWhere(['extension' => 'json'])
                ->andWhere(['like', 'name', StatisticsItemSearch::EXPORT_FILE_NAME_PREFIX])
                ->all()) {
                /** @var FlightBriefings $briefing */
                foreach ($fileLogs as $briefing) {
                    $briefing->delete();
                }
            }

            $this->saveLastActivity(round($i++ / $count * 100));

            // удаление информации о трансферных пассажирах старее чем 5 дней
            $transfersPassengersCount = EdbPassengerTransfer::deleteAll(['<=', 'created_at', gmdate("Y-m-d H:i:s", strtotime('- 5 days'))]);
            ExtendedLogger::storeLog(" COUNT TRANSFER PASSENGER TO DELETE =".$transfersPassengersCount);

            $this->saveLastActivity(100);

        } catch (\Exception $e) {
            $this->status = false;
            $this->addErrors('exception', $e->getMessage());
        }
    }
}
