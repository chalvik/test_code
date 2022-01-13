<?php

namespace common\modules\scheduler\controllers\console;

use common\modules\ccpFlight\models\Flight;
use common\modules\EdbPassenger\models\EdbPassenger;
use common\modules\EdbPassenger\models\console\EdbFlightVipPassengerParse;
use common\modules\EdbPassenger\models\EdbPassengerCode;
use common\modules\EdbPassenger\models\EdbPassengerGroup;
use common\modules\EdbPassenger\models\PassengerNationality;
use common\modules\food\models\FoodSurplusLog;
use common\modules\food\models\Meal;
use common\modules\food\models\MealType;
use common\modules\food\models\MealTypeAssignment;
use common\modules\food\models\Menu;
use common\modules\food\models\Ration;
use common\modules\food\models\RationClass;
use common\modules\food\models\RationClassMeal;
use common\modules\food\models\RationClassMealTypeAssignment;
use common\modules\food\models\RationClassType;
use common\modules\food\models\translations\MealTypeTranslation;
use common\modules\passenger\models\FlightPassengerNationality;
use common\modules\report\models\Report;
use common\modules\scheduler\models\SchedulerTask;
use common\modules\scheduler\models\SchedulerTaskLog;
use common\modules\scheduler\models\SchedulerTaskRun;
use common\tests\unit\TestHelper;
use console\controllers\BaseController;
use GuzzleHttp\Client;
use Yii;
use yii\console\widgets\Table;
use yii\db\JsonExpression;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

/**
 * Class InstallController for update and install task from scheduler
 * Class InstallController
 * @package common\modules\scheduler\controllers\console
 */
class InstallController extends BaseController
{


    public function actionImportNationalities()
    {
        $codes = [];
        if ($nalionalities = PassengerNationality::$reference) {
            if ($rows = preg_split("/\n/", $nalionalities)) {
                foreach ($rows as $row) {
                    if ($cells = preg_split("/\s/", $row)) {
                        $cells = array_reverse($cells);
                        $codes[] = [
                            'code1' => $cells[3],
                            'code2' => $cells[4],
                            'description' => implode(" ", array_slice($cells, 5)),
                        ];
                    }
                }

            }
            if ($codes) {
                if ($group = EdbPassengerGroup::findOne(['title' => 'Иностранный пассажир'])) {
                } else {
                    $group = new EdbPassengerGroup(['title' => 'Иностранный пассажир']);
                    $group->save();
                }
                foreach ($codes as $code) {
                    $new_code1 = new EdbPassengerCode(['code' => $code['code1'], 'title' => $code['description']]);
                    $new_code1->group_id = $group->id;
                    $new_code1->save();

                    $new_code1 = new EdbPassengerCode(['code' => $code['code2'], 'title' => $code['description']]);
                    $new_code1->group_id = $group->id;
                    $new_code1->save();
                }
            }
        };

    }


    public
    function actionSearchVip($from, $to)
    {

        $count_hours = $from * 60 * 60;
        $current_time = time();
        $start = gmdate("Y-m-d H:i", $current_time + $count_hours);
        $end = gmdate("Y-m-d H:i", $current_time + $count_hours + 3600 * $to);

        $flights = Flight::find()
            ->where(['between', 'std', $start, $end])
            ->limit(50)
            ->all();

        echo count($flights);
        //  return 1;

        if ($flights) {
            foreach ($flights as $flight) {
                $parser = new EdbFlightVipPassengerParse($flight);
                $parser->make();

                if ($vipList = TestHelper::callMethod($parser, 'getVipInfo', [])) {
                    echo "FLIGHT_ID" . $flight->id . " - YES" . PHP_EOL;
                    print_r($vipList);
                    echo PHP_EOL;
                } else {
                    echo "FLIGHT_ID" . $flight->id . " - NO" . PHP_EOL;
                }

            }

        }

    }

    public
    function actionClassBooking($flight_id)
    {

        if ($flight = Flight::findOne($flight_id)) {
            print_r($flight->getPassengers()->select('CLASS_BOOKING')->distinct()->column());

            if ($passengers = $flight->getPassengers()->select('CLASS_BOOKING')->asArray()->all()) {
                foreach ($passengers as $passenger) {
                    print_r($passenger);
                    echo PHP_EOL;
                }
            }

        }

    }


    public
    static function updatingStaticMethods()
    {
        return [
            RationClassType::className() => 'updateStatic'
        ];
    }


    /**
     * Array tasks for updating
     * @var array
     */
    private
        $tasks = [
      /*  [
            'title' => 'TSI за 2 часа',
            'class' => '\common\modules\passenger\tasks\TaskTsiBeforeDeparture',
            'status' => SchedulerTask::STATUS_RUN,
            'period' => 10 * 60,
        ],*/
        [
            'title' => 'Обновление рейсов по прилету',
            'class' => '\common\modules\ccpFlight\tasks\TaskFlightAfterDeparture',
            'status' => SchedulerTask::STATUS_RUN,
            'period' => 10 * 60,
        ],
        [
            'title' => 'Ffm	',
            'class' => '\common\modules\cargo\tasks\TaskFfm',
            'status' => SchedulerTask::STATUS_RUN,
            'period' => 5 * 60,
        ],
        [
            'title' => 'Cargo	',
            'class' => '\common\modules\cargo\tasks\TaskCargo',
            'status' => SchedulerTask::STATUS_RUN,
            'period' => 5 * 60,
        ],
        // [
        //     'title' => 'Карта салона	',
        //     'class' => '\common\modules\ccpAircraft\tasks\TaskAircraftDcsPure',
        //     'status' => SchedulerTask::STATUS_RUN,
        //     'period' => 180 * 60,
        // ],
        [
            'title' => 'Экипаж на три дня	',
            'class' => '\common\modules\ccpFlight\tasks\TaskFlightCrewSheduledFor3day',
            'status' => SchedulerTask::STATUS_RUN,
            'period' => 10 * 60,
        ],
        [
            'title' => 'Ростер за 2 часа',
            'class' => '\common\modules\ccpFlight\tasks\TaskFlightBeforeDeparture',
            'status' => SchedulerTask::STATUS_RUN,
            'period' => 5 * 60,
        ],
        [
            'title' => 'Ростер на 3 дня вперед',
            'class' => '\common\modules\ccpFlight\tasks\TaskFlightSheduledFor3day',
            'status' => SchedulerTask::STATUS_RUN,
            'period' => 60 * 60,
        ],
        [
            'title' => 'Сотрудники',
            'class' => '\common\modules\ccpEmployee\tasks\TaskEmployee',
            'status' => SchedulerTask::STATUS_RUN,
            'period' => 30 * 60,
        ],
        [
            'title' => 'Аэропорты',
            'class' => '\common\modules\ccpAirport\tasks\TaskAirport',
            'status' => SchedulerTask::STATUS_RUN,
            'period' => 30 * 60,
        ],
        [
            'title' => 'Самолеты',
            'class' => '\common\modules\ccpAircraft\tasks\TaskAircraft',
            'status' => SchedulerTask::STATUS_RUN,
            'period' => 30 * 60,
        ],
        [
            'title' => 'Удаление логов позднее 7 дней',
            'class' => '\common\modules\LogRefresh\tasks\TaskRemoveLog7day',
            'status' => SchedulerTask::STATUS_RUN,
            'period' => 24 * 60 * 60,
        ],
        [
            'title' => 'Обновление фото сотрудников',
            'class' => '\common\modules\ccpEmployee\tasks\TaskEmployeePhotoUpdate',
            'status' => SchedulerTask::STATUS_RUN,
            'period' => 24 * 60 * 60 * 7,
        ],
        [
            'title' => 'Удаление логов',
            'class' => '\common\modules\scheduler\tasks\TrashTask',
            'status' => SchedulerTask::STATUS_RUN,
            'period' => 24 * 60 * 60,
        ],
        [
            'title' => 'Отправка отчетов остатков еды',
            'class' => '\common\modules\report\tasks\TaskSendFoodSurplusReport',
            'status' => SchedulerTask::STATUS_RUN,
            'period' => 60 * 60,
        ],

        [
            'title' => 'Формирование выгрузки отчетов Excel',
            'class' => '\common\modules\report\tasks\TaskCreateReportExcel',
            'status' => SchedulerTask::STATUS_RUN,
            'period' => 24 * 60 * 60,
        ],

        [
            'title' => 'Запрос причин выдачи компенсационных пакетов',
            'class' => '\common\modules\compPackage\tasks\TaskGetReason',
            'status' => SchedulerTask::STATUS_RUN,
            'period' => 24 * 3600,
        ],
       /* [
            'title' => 'Запрос промокодов компенсационных пакетов',
            'class' => '\common\modules\compPackage\tasks\TaskGetCompPackets',
            'status' => SchedulerTask::STATUS_RUN,
            'period' => 60,
        ],*/
        [
            'title' => 'Отправка использованных компенсационных пакетов',
            'class' => '\common\modules\compPackage\tasks\TaskPushCompPackets',
            'status' => SchedulerTask::STATUS_RUN,
            'period' => 24 * 60 * 60,
        ],
        [
            'title' => 'Отправка Отзывов пассажиров на хранение в Кафку',
            'class' => 'common\modules\feedback\tasks\TaskFeedbackKafkaSender',
            'status' => SchedulerTask::STATUS_RUN,
            'period' => 2 * 60 * 60,
        ],
        [
            'title' => 'Отпр-ка Предпочтений Питания пассажиров на хранение в Кафку',
            'class' => 'common\modules\food\tasks\TaskPassengerPrefFoodKafkaSender',
            'status' => SchedulerTask::STATUS_RUN,
            'period' => 2 * 60 * 60,
        ],
    ];


    /**
     * Install tasks from $this->tasks to scheduler and truncate old task, line run and log
     * @param int $display_log
     */
    public
    function actionIndex($display_log = BaseController::LOG_DISPLAY)
    {

        $this->truncateTaskLog($display_log);
        $this->truncateTaskRun($display_log);
        $this->truncateTask($display_log);

        foreach ($this->tasks as $data) {
            $task = new SchedulerTask();
            $task->load($data, '');
            $task->progress = 0;
            $this->log("task  : " . $data['title'], "yellow", $display_log);
            if ($task->save()) {
                $this->log("task  : " . $data['title'] . " added", "green", $display_log);
            } else {
                $this->log("error added task  : " . $data['title'] . " : " . json_encode($task->errors), "red", $display_log);
            }
        }
    }


    /**
     * Update tasks from $this->tasks  search by field class
     * @param int $display_log
     */
    public
    function actionUpdate($display_log = BaseController::LOG_DISPLAY)
    {
        foreach ($this->tasks as $data) {
            $task = SchedulerTask::find()
                ->where([
                    'class' => $data['class']
                ])
                ->one();
            if (!$task) {
                $task = new SchedulerTask();
            }

            $task->load($data, '');
            if ($task->save()) {
                $this->log("task  : " . $data['title'] . " updated", "green", $display_log);
            } else {
                $this->log("error added task  : " . $data['title'] . " : " . json_encode($task->errors), "red", $display_log);
            }
        }
    }


    /**
     * Update static for all reference book
     * @param int $display_log
     */


    public
    function actionUpdateStatic($display_log = BaseController::LOG_DISPLAY)
    {
        foreach (self::updatingStaticMethods() as $class => $method) {
            (new $class)->$method($this);
        }

    }

    /**
     * Truncate old task, line run and log
     * @param int $display_log
     */
    public
    function actionTruncate($display_log = BaseController::LOG_DISPLAY)
    {
        $this->truncateTask($display_log);
        $this->truncateTaskRun($display_log);
        $this->truncateTaskLog($display_log);
    }

    /**
     * @param int $display_log
     */
    private
    function truncateTask($display_log = BaseController::LOG_DISPLAY)
    {
        $this->log("truncate SchedulerTask", "yellow", $display_log);
        try {
            $table = SchedulerTask::tableName();
            SchedulerTask::deleteAll();
            Yii::$app->db->createCommand()->resetSequence($table);
            $this->log("truncate SchedulerTask done", "green", $display_log);
        } catch (\Exception $e) {
            $this->log("truncate SchedulerTask error: " . $e->getMessage(), "red", $display_log);
        }
    }


    /**
     * @param int $display_log
     */

    private
    function truncateTaskLog($display_log = BaseController::LOG_DISPLAY)
    {
        try {
            $table = SchedulerTaskLog::tableName();
            Yii::$app->db->createCommand()->setRawSql("SET FOREIGN_KEY_CHECKS=0");
            Yii::$app->db->createCommand()->truncateTable($table)->execute();
            Yii::$app->db->createCommand()->resetSequence($table);
            Yii::$app->db->createCommand()->setRawSql("SET FOREIGN_KEY_CHECKS=1");
            $this->log("truncate SchedulerTaskLog done", "green", $display_log);
        } catch (\Exception $e) {
            $this->log("truncate SchedulerTaskLog error: " . $e->getMessage(), "red", $display_log);
        }
    }


    /**
     * @param int $display_log
     */
    private
    function truncateTaskRun($display_log = BaseController::LOG_DISPLAY)
    {
        try {
            $table = SchedulerTaskRun::tableName();
//            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS=0')->execute();
            Yii::$app->db->createCommand()->setRawSql("SET FOREIGN_KEY_CHECKS=0");
            Yii::$app->db->createCommand()->truncateTable($table)->execute();
            Yii::$app->db->createCommand()->resetSequence($table);
            Yii::$app->db->createCommand()->setRawSql("SET FOREIGN_KEY_CHECKS=1");
            $this->log("truncate SchedulerTaskRun done", "green", $display_log);
        } catch (\Exception $e) {
            $this->log("truncate SchedulerTaskRun error: " . $e->getMessage(), "red", $display_log);
        }
    }


    public
    function actionResetPushingReports()
    {
        if ($prompt = $this->prompt('Сбросить статус отправки отчетов Остатков питания? (yes|no)')) {
            $count = Report::updateAll(['is_pushed_cloud' => 0],
                ['AND',
                    ['type_id' => Report::TYPE_ADDITIONAL_FOOD_SURPLUS],
                    ['>=', 'created_at', '2019-05-25']
                ]);

            $this->stdout(' Сбросили ' . $count . PHP_EOL);

            $logCount = FoodSurplusLog::deleteAll();

            $this->stdout(' Удалили  ' . $logCount . " логов отправки " . PHP_EOL);

        }

    }

    public
    function actionExistedMealTypes()
    {

        $transaction = Yii::$app->getDb()->beginTransaction();

        if ($mealTypes = MealType::find()->all()) {
            echo count($mealTypes) . PHP_EOL;
            foreach ($mealTypes as $mealType) {
                if ($meals = Meal::find()->where(['meal_type_id' => $mealType->id])->asArray()->all()) {
                    echo $mealType->title . PHP_EOL;
                    print_r(implode(",", ArrayHelper::getColumn($meals, 'id')));
                    echo PHP_EOL;
                }
                $mealType->meals = ArrayHelper::getColumn($meals, 'id');
                $mealType->save();
            }
        }
        $transaction->commit();
    }

    public
    function actionMenuFormatter()
    {
        $transaction = Yii::$app->getDb()->beginTransaction();
        // $menusId = array_rand(Menu::find()->indexBy('id')->column(), 1);
        $countCreated = 0;

        if ($menus = Menu::find()->all()) {
            $this->stdout('доступно ' . count($menus) . " меню для обновлений" . PHP_EOL);
            /** @var Menu $menu */
            foreach ($menus as $key => $menu) {
                /* if ($menu->settings && ($menu->settings->flight_flts) &&  count($menu->settings->flight_flts->getValue())) {
                     echo $this->ansiFormat('  И меню имеет flts   ' . implode(",", $menu->settings->flight_flts->getValue()), Console::FG_BLUE);
                     print_r($menu->settings->flight_flts->getValue());
                 }*/
                if ($rations = $menu->rations) {
                    $this->stdout(' Меню ' . $menu->title . ' имеет рационы ' . implode(",", ArrayHelper::getColumn($rations, 'title')) . PHP_EOL);
                    /** @var Ration $ration */
                    foreach ($rations as $ration) {
                        if ($rationClasses = $ration->classes) {
                            $this->stdout('  Рацион  ' . $ration->id . " - " . $ration->title . ' имеет классы рационов ' . implode(",", ArrayHelper::getColumn($rationClasses, 'type.title')) . PHP_EOL);
                            /** @var RationClass $rationClass */
                            foreach ($rationClasses as $rationClass) {
                                if ($rationClass->meal_type_order_json) {
                                    if ($order = json_decode($rationClass->meal_type_order_json)) {
                                        if ($mealTypesId = ArrayHelper::getColumn($order, 'id')) {
                                            foreach ($mealTypesId as $item) {
                                                (new RationClassMealTypeAssignment(['meal_type_id' => $item, 'ration_class_id' => $rationClass->id]))->save();

                                            }

                                        }

                                    }

                                }

                            }

                        }
                    }

                }


            }
        }
        $transaction->commit();
        echo $countCreated . PHP_EOL;
    }

    public
    function actionNewMenuFormatter()
    {

        $transaction = Yii::$app->getDb()->beginTransaction();
        RationClassMealTypeAssignment::deleteAll();
        $this->actionRemoveGeneratedMealTypes();
        // $menusId = array_rand(Menu::find()->indexBy('id')->column(), 1);
        $countCreated = 0;

        if ($menus = Menu::find()->all()) {
            $this->stdout('доступно ' . count($menus) . " меню для обновлений" . PHP_EOL);
            /** @var Menu $menu */
            foreach ($menus as $key => $menu) {
                /* if ($menu->settings && ($menu->settings->flight_flts) &&  count($menu->settings->flight_flts->getValue())) {
                     echo $this->ansiFormat('  И меню имеет flts   ' . implode(",", $menu->settings->flight_flts->getValue()), Console::FG_BLUE);
                     print_r($menu->settings->flight_flts->getValue());
                 }*/
                if ($rations = $menu->rations) {
                    $this->stdout(' Меню ' . $menu->title . 'ID - ' . $menu->id . ' имеет рационы ' . implode(",", ArrayHelper::getColumn($rations, 'title')) . PHP_EOL);
                    /** @var Ration $ration */
                    foreach ($rations as $ration) {
                        if ($rationClasses = $ration->classes) {
                            $this->stdout('  Рацион  ' . $ration->id . " - " . $ration->title . ' имеет классы рационов ' . implode(",", ArrayHelper::getColumn($rationClasses, 'type.title')) . PHP_EOL);
                            /** @var RationClass $rationClass */
                            foreach ($rationClasses as $rationClass) {

                                if ($rationClass->meal_type_order_json) {
                                    $order = json_decode($rationClass->meal_type_order_json);
                                    //  print_r($order);

                                }

                                if ($meals = Meal::find()->where(['id' => RationClassMeal::find()->where(['ration_class_id' => $rationClass->id])->select(['meal_id'])->column()])->all()) {
                                    echo $this->ansiFormat('  Рацион Класс  ' . $rationClass->type->title, Console::FG_BLUE) . ' имеет блюда ' .
                                        implode(",", ArrayHelper::getColumn($meals, 'title')) . PHP_EOL;

                                    $newMeatType = new MealType([
                                        'title' => '!###!Новый справочник рациона ' . $rationClass->type->title . " ДЛЯ МЕНЮ " . $menu->title,
                                        'class_type_id' => $rationClass->type->id
                                    ]);

                                    $newMeatType->meals = ArrayHelper::getColumn($meals, 'id');

                                    if ($newMeatType->save()) {

                                        $countCreated++;
                                        (new RationClassMealTypeAssignment(['meal_type_id' => $newMeatType->id, 'ration_class_id' => $rationClass->id]))->save();

                                        // TODO КОД ДЛЯ КОРРЕТНОСТИ ПОЯВЛЕНЯ И УДАЛЕНИЯ ЗАПИСЕЙ ИЗ ТАБЛИЦ

                                        // $this->checkData($rationClass);
                                        if ($foundedMealType = $rationClass->getMealTypes()->one()) {
                                            echo "  Создан новый Справочник рациона " . $this->ansiFormat($foundedMealType->id . "- " . $foundedMealType->title, Console::FG_BLUE) . PHP_EOL;
                                        }

                                    }

                                } else {
                                    //   echo $this->ansiFormat('  Рацион Класс  ' . $rationClass->type->title, Console::FG_RED) . PHP_EOL . '  НЕ имеет блюда ' . PHP_EOL;
                                }

                            }

                        }
                    }

                }

                //  if ($key > 50) break;

            }
        }

        $transaction->commit();
        echo $countCreated . PHP_EOL;
    }

    public
    function actionRemoveGeneratedMealTypes()
    {
        if ($mealTypesIds = MealTypeTranslation::find()->where(['like', 'title', '!###!'])->select('meal_type_id')->column()) {

            if ($count = MealTypeAssignment::find()->where(['meal_type_id' => $mealTypesIds])->count()) {
                echo $this->ansiFormat($count . ' TO REMOVE MealTypeAssignment', Console::FG_BLUE) . PHP_EOL;
            }

            if ($count = RationClassMealTypeAssignment::find()->where(['meal_type_id' => $mealTypesIds])->count()) {
                echo $this->ansiFormat($count . ' TO REMOVE RationClassMealTypeAssignment', Console::FG_BLUE) . PHP_EOL;
            }

            echo implode(",", $mealTypesIds) . PHP_EOL;
            if ($mealTypes = MealType::find()->where(['id' => $mealTypesIds])->all()) {
                foreach ($mealTypes as $mealType) {
                    $mealType->delete();
                }

                if (!$count = MealTypeAssignment::find()->where(['meal_type_id' => $mealTypesIds])->count()) {
                    echo $this->ansiFormat('SUCCESS REMOVE MealTypeAssignment', Console::FG_GREEN) . PHP_EOL;
                }

                if (!$count = RationClassMealTypeAssignment::find()->where(['meal_type_id' => $mealTypesIds])->count()) {
                    echo $this->ansiFormat('SUCCESS REMOVE RationClassMealTypeAssignment', Console::FG_GREEN) . PHP_EOL;
                }

            } else {
                echo $this->ansiFormat('NOTHING TO DELETE ', Console::FG_RED) . PHP_EOL;
            }

        } else {
            echo $this->ansiFormat('NOTHING TO DELETE ', Console::FG_RED) . PHP_EOL;
        }
    }

    private
    function checkData(RationClass $rationClass)
    {
        if ($foundedMealType = $rationClass->getMealTypes()->one()) {
            echo "  Создан новый Справочник рациона " . $this->ansiFormat($foundedMealType->id . "- " . $foundedMealType->title, Console::FG_BLUE) . PHP_EOL;

            $newMeals = [];
            $newMealsId = [];
            if ($foundedMealType->meals) {
                foreach ($foundedMealType->meals as $meal) {
                    $newMeals[] = [$meal->id, $meal->title];
                    $newMealsId[] = $meal->id;
                }

            };
            echo Table::widget([
                'headers' => ['id', 'title'],
                'rows' => $newMeals,
            ]);

            echo "   Попытка удалить только что созданный справочник района " . $this->ansiFormat($foundedMealType->id . "- " . $foundedMealType->title, Console::FG_BLUE) . PHP_EOL;

            $foundedMealType->delete();
            if ($oldMeals = $rationClass->getMeals()->all()) {
                echo $this->ansiFormat('  Рацион Класс  ' . $rationClass->type->title, Console::FG_BLUE) . PHP_EOL . '  имеет блюда ' . PHP_EOL;

                $oldMeals = [];
                foreach ($foundedMealType->meals as $meal) {
                    $oldMeals[] = [$meal->id, $meal->title];

                };
                echo Table::widget([
                    'headers' => ['id', 'title'],
                    'rows' => $oldMeals,
                ]);
            } else {
                echo $this->ansiFormat('  Рацион Класс  ' . $rationClass->type->title, Console::FG_RED) . '  НЕ имеет блюда  ПОСЛЕ УДАЛЕНИЯ СТАРОГО СПРАВОЧНИКА РАЦИОНА' . PHP_EOL;
                if ($meals = Meal::find()->where(['id' => $newMealsId])->all()) {
                    echo $this->ansiFormat(' НО САМИ БЛЮДА НА МЕСТЕ', Console::FG_RED) . PHP_EOL;

                    $oldMeals = [];
                    foreach ($meals as $meal) {
                        $oldMeals[] = [$meal->id, $meal->title];

                    };
                    echo Table::widget([
                        'headers' => ['id', 'title'],
                        'rows' => $oldMeals,
                    ]);
                }


            }

        }


    }

    public
    function actionTest()
    {
        print_r(json_decode(new JsonExpression(['codes' => 355])));
    }

}
