<?php
namespace common\modules\ccpEmployee\models;

use common\components\PSqlDecoder;
use common\modules\ccpAirport\models\Airport;
use common\modules\ccpFlight\models\Carrier;
use common\modules\report\models\ReportDocuments;
use Yii;
use common\behaviors\SaveManyToManyBehavior;
use common\models\base\BaseActiveRecord;
use common\modules\ccpFlight\models\FlightLegCrew;
use common\modules\library\models\Library;
use common\modules\library\models\LibraryEmployee;
use common\modules\storagefiles\models\Storagefiles;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * Аэропорты (Employee)
 * класс работающий с ActiveRecord объекта и связями с этим объектом
 *
 * This is the model class for table "ccp_employee".
 * Class Employee
 * @package common\modules\ccpEmployee\models
 * @property integer $id
 * @property integer $roster_id
 * @property string $name
 * @property string $fio_eng
 * @property string $fio_rus
 * @property string $quals_list
 * @property string $langs_list
 * @property string $port_base
 * @property string $block_hours
 * @property integer $file_id
 * @property integer $port_base_id
 * @property string $created_at
 * @property string $updated_at
 * @property string last_updated_at
 * @property integer $type
 * @property array $crewcatidx
 * @property array $qualsList
 */
class Employee extends BaseActiveRecord
{
    public $_documents;

    const TYPE_DEFAULT = 0;
    const TYPE_SUPER_USER = 777;
    const TYPE_SUPER_STEWARD = 888;

    /**
     * @var array
     */
    public static $list_types = [
        self::TYPE_DEFAULT      => 'По умолчанию',
        self::TYPE_SUPER_USER   => 'Супер пользователь',
        self::TYPE_SUPER_STEWARD   => 'Супер бортпроводник '
    ];


    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors[] = [
            'class' => SaveManyToManyBehavior::className(),
            'additionAttribute' => "_documents",
            'relationName' => 'libraries',
            'relationModel' => LibraryEmployee::className(),
            'selfAttribute' => 'employee_id',
            'remoteAttribute' => 'library_id',
        ];
        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%ccp_employee}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['roster_id','file_id', 'port_base_id', 'type'], 'integer'],
            [['created_at', 'updated_at','last_updated_at', 'documents', 'crewcatidx'], 'safe'],
            [
                [
                    'name',
                    'fio_eng',
                    'fio_rus',
                    'quals_list',
                    'langs_list',
                    'port_base',
                    'block_hours'
                ], 'string',
                'max' => 255
            ],
            ['type', 'default', 'value'=> self::TYPE_DEFAULT]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('employee', 'ID'),
            'roster_id' => Yii::t('employee', 'Roster ID'),
            'name' => Yii::t('employee', 'Name'),
            'fio_eng' => Yii::t('employee', 'Fio Eng'),
            'fio_rus' => Yii::t('employee', 'Fio Rus'),
            'quals_list' => Yii::t('employee', 'Quals List'),
            'langs_list' => Yii::t('employee', 'Langs List'),
            'port_base' => Yii::t('employee', 'Port Base'),
            'block_hours' => Yii::t('employee', 'Block Hours'),
            'created_at' => Yii::t('employee', 'Created At'),
            'file_id' => Yii::t('employee', 'File Id'),
            'updated_at' => Yii::t('employee', 'Updated At'),
            'last_updated_at' => Yii::t('employee', 'Last Updated At'),
            'type' => Yii::t('employee', 'Type'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        $expand = \Yii::$app->request->get('expand');
        $links = explode(',', $expand);
        $fields = parent::fields();

        if (in_array('crewcatidx', $links)) {
            $fields['crewcatidx'] = function ($model) {
                return PSqlDecoder::decodeArray($model->crewcatidx);
            };
        } else {
            unset($fields['crewcatidx']);
        }

        if (in_array('photo', $links)) {
            $fields['photo'] = function ($model) {
                if (isset($model->sf->id)) {
                    $item = new \stdClass();
                    $item->file_id = $model->sf->id;
                    $item->url = $model->sf->url;
                    $item->name  =  $model->sf->origin_name;
                    $item->size  =  $model->sf->size;
                    $item->mime  =  $model->sf->mime;
                    $item->extension = $model->sf->extension;
                } else {
                    $item = null;
                }
                return $item;
            };
        }

        if (in_array('portBase  ', $links)) {
            $fields['portBase'] = function ($model) {
                return $this->portBase;
            };
        }

        $fields['carrier'] = function ($model) {
            /** $model Employee */
 //           return isset($model->legCrew->flight->carrier) ? $model->legCrew->flight->carrier : null;
            return $model->carrier;
        };
        return $fields;
    }

    /**
     * Возвращает список документов библиотеки
     * @return array
     */
    public function getDocuments()
    {
        return ArrayHelper::getColumn($this->libraries, "id");
    }

    /**
     * Устанавливает список документов библиотеки
     * @param $value
     */
    public function setDocuments($value)
    {
        $this->_documents = $value;
    }

    /**
     * Связь на доукменты библиотеки
     * Get relation Library for Employee
     * @return \yii\db\ActiveQuery
     */
    public function getLibraries()
    {
        return $this->hasMany(Library::className(), ['id' => 'library_id'])
            ->viaTable(LibraryEmployee::tableName(), ['employee_id' => 'id']);
    }

    /**
     * Связь на непрочитанные документы библиотеки
     * @return ActiveQuery
     */
    public function getNotReadLibraries()
    {
        return $this->hasMany(Library::className(), ['id' => 'library_id'])
            ->viaTable(LibraryEmployee::tableName(), ['employee_id' => 'id'], function ($query) {
                /** @var $query ActiveQuery */
                return $query->andWhere(['flag_new' => true]);
            });
    }

    /**
     * Связь с членом экипажа
     * @return ActiveQuery
     */
    public function getLegCrew()
    {
        return $this->hasOne(FlightLegCrew::className(), ['roster_id' => 'roster_id']);
    }

    /**
     * Связь с аэропортом базирования сотрудника
     * @return ActiveQuery
     */
    public function getPortBase()
    {
        return $this->hasOne(Airport::className(), ['id' => 'port_base_id']);
    }

    /**
     * Связь с файловым хранилищем
     * @return ActiveQuery
     */
    public function getSf()
    {
        return $this->hasOne(Storagefiles::className(), ['id' => 'file_id']);
    }

    /**
     * Возвращает флаг является ли пользователь супер юзером
     * @return boolean
     */
    public function getIsSuperUser()
    {
        return $this->type == self::TYPE_SUPER_USER;
    }

    /**
     * @return bool
     */
    public function beforeDelete()
    {
        Yii::$app->storagefiles->delete($this->file_id);
        return parent::beforeDelete();
    }

    /**
     * возвращает массив квалификаций
     * @return array
     */
    public function getQualsList()
    {
        return explode(';', $this->quals_list);
    }

    /**
     * Возвращает массив компаний к которым привязан пользователь
     * для IOS пользователя по табельному номеру
     * для SSO пользователей по таблице связи
     * @return int
     */
    public function getCarrier()
    {
//        $carrier = null;
//        $code = (int)substr($this->roster_id, 0, 3);
//        if ($code == 110) {
//            $carrier = Carrier::CARRIER_GH;
//        } elseif ($code == 100) {
            $carrier = Carrier::CARRIER_S7;
//        }
        return $carrier;
    }

    /**
     * Возвращает массив для вывода списка сотрудников
     */
    public static function dropDownList()
    {
        return ArrayHelper::map(self::find()->all(), 'id', 'roster_id');
    }

    /**
     * Возвращает флвг для разоешения аудентификации  приложении
     * @param $quals array массив квалификаций сотрудника
     * @param $crewcatidx array массив кодов сотрудника (разрешений)
     * @return  bool
     **/
    public function checkPermissions($quals, $crewcatidx)
    {
        return  (
            $this->type == Employee::TYPE_SUPER_USER
            || $this->type == Employee::TYPE_SUPER_STEWARD
            || array_intersect($quals, $this->qualsList)
            || array_intersect($crewcatidx, PSqlDecoder::decodeArray($this->crewcatidx))
        );
    }

    /**
     * Проверка кодов сотрудника
     * @param $crewcatidx array массив кодов сотрудника
     * @return  bool
     **/
    public function checkCrewCode($quals, $crewcatidx)
    {
        return  (
            array_intersect($quals, $this->qualsList)
            && array_intersect($crewcatidx, PSqlDecoder::decodeArray($this->crewcatidx))
        );
    }

}
