<?php

namespace common\modules\scheduler\tasks;

use common\modules\ccpFlight\models\Flight;
use common\modules\scheduler\components\AbstractTask;

/**
 * интервал хранения записей отчетов
 */
const INTRVAL_SAVE_REPORTS = 60*24*60*60;

/**
 * интервал хранения записей отчетов
 */
const INTRVAL_SAVE_AIRPORT_DEFECT = 1*60*60;


/**
 * Class TaskСleaningOldData
 * задача для планировщика предназначена для удаления устаревших данных в проекте
 * - записи в базе данных
 * - старые уже не используеммые файлы
 *
 * тем самым освобождает свободное местго на диске
 * @package common\modules\scheduler\tasks
 */
class TaskCleaningOldData extends AbstractTask
{

    /**
     *
     */
    public function run()
    {
        $query = Flight::find()
            ->where([
            ]);
        foreach ($query->batch() as $flights) {
            foreach ($flights as $flight) {
                $this->сleaningFlights();
            }
        }
    }

    /**
     * Очистка устаревших записей
     * дефектов воздушных судос с их файлами
     */
    private function cleaningAircraftDefect()
    {
    }

    /**
     * Очистка устаревших записей
     * дефектов воздушных судос с их файлами
     */
    private function сleaningFlights(Flight $flight)
    {
    }

    /**
     * Очистка устаревших записей
     * дефектов воздушных судос с их файлами
     */
    private function сleaningReports()
    {
    }

    /**
     * Очистка устаревших записей
     * дефектов воздушных судос с их файлами
     */
    private function сleaningCargo()
    {
    }

}
