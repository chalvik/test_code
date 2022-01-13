<?php
namespace common\modules\scheduler\controllers;

use common\modules\scheduler\models\SchedulerTaskLog;
use yii\web\Controller;
use Yii;

/**
 * Class ChartController
 * @package common\modules\scheduler\controllers
 */
class ChartController extends Controller
{

    /**
     * @param null $date
     * @return string
     */
    public function actionIndex()
    {
        $date = Yii::$app->request->post('date', gmdate("Y-m-d 00:00:00"));

        $period = 1;
        $start = gmdate("Y-m-d 00:00:00", strtotime($date));
        $end = gmdate("Y-m-d 23:59:59", strtotime($date));
        $log_error = SchedulerTaskLog::find()
            ->select([
                'count'=>'COUNT(id)',
                'aligned' => "(date_trunc('hour', (created_at at time zone 'UTC' - timestamptz 'epoch') / $period) * $period + timestamptz 'epoch')"
            ])
            ->where(['status' => SchedulerTaskLog::STATUS_ERROR])
            ->andWhere(['between','created_at',$start, $end])
            ->groupBy('aligned')
            ->asArray()
            ->all();
        $log_ok = SchedulerTaskLog::find()
            ->select([
                'count'=>'COUNT(id)',
                'aligned' => "(date_trunc('hour', (created_at at time zone 'UTC' - timestamptz 'epoch') / $period) * $period + timestamptz 'epoch')"
            ])

            ->where(['status' => SchedulerTaskLog::STATUS_SUCCESS])
            ->andWhere(['between','created_at',$start, $end])
            ->groupBy('aligned')
            ->asArray()
            ->all();

        $log_line_error = SchedulerTaskLog::find()
            ->select([
                'count'=>'COUNT(id)',
                'aligned' => "(date_trunc('hour', (created_at at time zone 'UTC' - timestamptz 'epoch') / $period) * $period + timestamptz 'epoch')"
            ])
            ->where(['status' => SchedulerTaskLog::STATUS_ERROR_ADD_IN_LINE])
            ->andWhere(['between','created_at',$start, $end])
            ->groupBy('aligned')
            ->asArray()
            ->all();
        $log_line_ok = SchedulerTaskLog::find()
            ->select([
                'count'=>'COUNT(id)',
                'aligned' => "(date_trunc('hour', (created_at at time zone 'UTC' - timestamptz 'epoch') / $period) * $period + timestamptz 'epoch')"
            ])
            ->where(['status' => SchedulerTaskLog::STATUS_SUCCESS_IN_LINE])
            ->andWhere(['between','created_at',$start, $end])
            ->groupBy('aligned')
            ->asArray()
            ->all();

        $step = 0;
        $data = [];
        $axios = [];
        $diff_time = 0;
        $diff_period = strtotime($end.' UTC') - strtotime($start.' UTC')+1;
        while ($diff_period > $diff_time) {
            $data['ok'][$step] = [$step, 0];
            $data['error'][$step] = [$step, 0];
            $data['line_ok'][$step] = [$step, 0];
            $data['line_error'][$step] = [$step, 0];

            $axios[$step] = [$step, gmdate("H:i", strtotime($start.' UTC')+$diff_time)];

            foreach ($log_ok as $item) {
                if (gmdate("H", strtotime($item['aligned'])) ==
                    gmdate("H", strtotime($start." UTC")+$diff_time)
                ) {
                    $data['ok'][$step] = [$step, $item['count']];
                }
            }
            foreach ($log_error as $item) {
                if (gmdate("H", strtotime($item['aligned'])) ==
                    gmdate("H", strtotime($start." UTC")+$diff_time)
                ) {
                    $data['error'][$step] = [$step, $item['count']];
                }
            }
            foreach ($log_line_ok as $item) {
                if (gmdate("H", strtotime($item['aligned'])) ==
                    gmdate("H", strtotime($start." UTC")+$diff_time)
                ) {
                    $data['line_ok'][$step] = [$step, $item['count']];
                }
            }
            foreach ($log_line_error as $item) {
                if (gmdate("H", strtotime($item['aligned'])) ==
                    gmdate("H", strtotime($start." UTC")+$diff_time)
                ) {
                    $data['line_error'][$step] = [$step, $item['count']];
                }
            }

            $step++;
            $diff_time = $diff_time+$period*3600;
        }

        return $this->render('index', [
            'data' => $data,
            'start' => $start,
            'end' => $end,
            'x' => $axios,
            'date' => $date
        ]);
    }

}