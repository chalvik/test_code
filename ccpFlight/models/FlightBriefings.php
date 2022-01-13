<?php

namespace common\modules\ccpFlight\models;

use common\models\helpers\TimeHelper;
use common\modules\ccpFlight\models\renderers\FlightBriefingRenderer;
use common\modules\ccpUser\models\User;
use common\modules\storagefiles\models\Storagefiles;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Url;

/**
 * This is the model class for table "ccp_flight_briefings".
 *
 * @property int $id
 * @property string $title
 * @property int $flight_id Flight id
 * @property string $std Std
 * @property int $flt Flt
 * @property int $user_id Пользователь
 * @property int $roster_id Табельный
 * @property int $file_id Файл
 * @property int $status Статус
 * @property string $created_at
 * @property string $update_at
 * 
 * @property Flight flight
 * @property Storagefiles $file
 */
class FlightBriefings extends \yii\db\ActiveRecord
{
    const REFRESH_AFTER_FLIGHT_FINISHED = true;

    public $file_name;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ccp_flight_briefings';
    }


    /**
     * @return array
     */
    public function fields()
    {
        $fields = parent::fields();
        $fields['file'] = function ($model) {
            return $model->file;
        };
        $fields['link'] = function ($model) {
            return Url::to(['/sf/files/download', 'file_id' => $model->file_id]);
        };
        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['flight_id', 'flt', 'user_id', 'roster_id', 'file_id', 'status'], 'default', 'value' => null],
            [['flight_id', 'flt', 'user_id', 'roster_id', 'file_id', 'status'], 'integer'],
            [['std', 'created_at', 'update_at'], 'safe'],
            [['title'], 'string', 'max' => 256],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getFile()
    {
        return $this->hasOne(Storagefiles::className(), ['id' => 'file_id']);
    }

    /**
     * @return bool
     */
    public function beforeValidate()
    {


        if ($flight = Flight::findOne($this->flight_id)) {
            $this->flt = $flight->flt;
            $this->std = $flight->std;
        };

        $this->title = $this->title = "Рейс " . $this->flt . " - " . $this->std;

        // setting user settings
        if (!$this->user_id) {
            try {
                $this->user_id = Yii::$app->user->id;
                /** @var User $user */
                $user = Yii::$app->user->identity;
                $this->roster_id = $user->employee->roster_id;
            } catch (\Exception $exception) {

            }

        }

        return parent::beforeValidate();
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        $this->refreshPdf();
        return parent::beforeSave($insert);
    }

    public function compose()
    {
        $this->makePdf();
        return true;
    }

    public function makePdf()
    {
        /** @var \common\modules\storagefiles\StorageFiles $fileStorage */
        $fileStorage = Yii::$app->storagefiles;
        $renderer = new FlightBriefingRenderer($this);
        $pdf = new Mpdf(['tempDir' => sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'mpdf']);
        $pdf->WriteHTML($renderer->render());
        $string = $pdf->Output('', Destination::STRING_RETURN);

        $file_id = $fileStorage->uploadFileContent($this, $string, 'pdf', $this->title);

        if ($file_id) {
            // delete old pdf
            $this->deleteFile();
            $this->file_id = $file_id;
        } else throw new \Exception(' file cannot be save');


    }


    /**
     * @return ActiveQuery|null
     */
    public function getFlight()
    {
        return $this->hasOne(Flight::className(), ['id' => 'flight_id']);
    }

    public function deleteFile()
    {

        // deleting file
        try {
            if ($this->file) $this->file->delete();
        } catch (\Exception $exception) {

        }

    }

    public function refreshPdf()
    {
        // check for if report was loaded
        if ($this->flight) {
            if (self::REFRESH_AFTER_FLIGHT_FINISHED || (TimeHelper::getGreenwichCurrentTime() <= $this->flight->sta)) {
                $this->compose();
                $this->status++;
            }
        }

    }


    public function beforeDelete()
    {

        $this->deleteFile();
        return parent::beforeDelete();
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'value' => TimeHelper::getGreenwichCurrentTime(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'flight_id' => 'Flight ID',
            'std' => 'Std',
            'flt' => 'Flt',
            'user_id' => 'User ID',
            'roaster_id' => 'Roaster ID',
            'file_id' => 'File ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'update_at' => 'Update At',
        ];
    }
}
