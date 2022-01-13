<?php
namespace common\modules\ccpEmployee\controllers\console;


use Yii;
use console\controllers\BaseController;
use common\modules\ccpEmployee\models\Employee;
/**
 * This is the controller class for module Employee.
 * Console commands.
 */

class UpdatePhotoController extends BaseController
{
/**
 * Upload / Update  photo for employee in storagefiles
 * @param string $path  Path where are photo for upload
 */
    public function actionIndex($path)
    {
        if ($path) {
            $query =  Employee::find();
            foreach ($query->batch() as $data) {
                /** @var Employee $employee */
                foreach ($data as $employee) {
                    $this->log($employee->roster_id, "green");
                    $path_file = $this->GetFile($path, $employee->roster_id);
                    if ($path_file) {
                        if ($employee->sf) {
                            Yii::$app->storagefiles->delete($employee->file_id);
                        }
                        $file_id = Yii::$app->storagefiles->saveStorageFileFromPath($path_file, $employee, false);
                        if ($file_id) {
                            $employee->file_id = $file_id;
                            $employee->save();
                        }
                    } else {
                        $this->log(" Photo didn`t find from Employee  roster_id = $employee->roster_id ", "red");
                    }
                }
            }
        } else {
            $this->log(" Error path to directory with files ", "red");
        }
    }

    /**
     * Get path file for
     * @param string $base_path  path directory
     * @param integer $roster_id
     * @return string
     */
    private function GetFile($base_path, $roster_id)
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
