<?php
namespace common\modules\ccpEmployee\tasks;

use common\modules\ccpEmployee\models\Employee;
use common\modules\scheduler\components\AbstractTask;
use Yii;

/**
 * Задача для планировщика (модуль сommon/modules/scheduler )
 * Цель задачи  - обновление foto for employees
 * минимальный расчетный период обновление ... 5 минут
 *
 * Class TaskEmployeePhotoUpdate
 * @package common\modules\ccpEmployee\tasks
 */
class TaskEmployeePhotoUpdate extends AbstractTask
{

    const PATH_TO_DIR_PHOTO = '/mnt/photo_share';

    /**
     * @inheritdoc
     */
    public function run()
    {
        $psize = 100;
        $page = 0;
        $count = Employee::find()->count();
        $pages = ceil($count/$psize);

        try {
            if (self::PATH_TO_DIR_PHOTO) {
                $query =  Employee::find();
                foreach ($query->batch($psize) as $data) {
                    /** @var Employee $employee */
                    foreach ($data as $employee) {
                        $path_file = $this->GetFile(self::PATH_TO_DIR_PHOTO, $employee->roster_id);
                        if ($path_file) {
                            if ($employee->sf) {
                                Yii::$app->storagefiles->delete($employee->file_id);
                            }
                            $file_id = Yii::$app->storagefiles->saveStorageFileFromPath($path_file, $employee, false);
                            if ($file_id) {
                                $employee->file_id = $file_id;
                                $employee->save();
                            }
                        }
                    }
                    $page++;
                    $process = floor($page/$pages*100);
                    $this->saveLastActivity($process);
                }
            }
            $this->status = true;
        } catch (\Exception $e) {
            $this->status = false;
            $this->addErrors('exception', $e->getMessage());
        }
    }

    /**
     * Get path file for
     * @param string $base_path  path directory
     * @param integer $roster_id
     * @return string
     */
    private function getFile($base_path, $roster_id)
    {
        $result = null;
        $photo = glob(Yii::getAlias("$base_path/$roster_id*"));
        if (isset($photo[0])) {
            $ar = explode('/', $photo[0]);
            $name = end($ar);
            $path = "$base_path/$name";
            if (file_exists($path)) {
                $result =  $path;
            }
        }
        return $result;
    }
}
