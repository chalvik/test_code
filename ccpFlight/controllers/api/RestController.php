<?php
namespace common\modules\ccpFlight\controllers\api;

use common\modules\ccpFlight\models\Flight;
use Yii;
use api\rest\ActiveRestController;
use console\models\Aims;

/**
 * Класс реализует методы для обработки апи запросов
 * для получение полной сводки о рейсе
 *
 * This is the api controller class for flights.
 * Class RestController
 * @package common\modules\ccpFlight\controllers\api
 */
class RestController extends ActiveRestController
{
    public $modelClass = 'common\modules\ccpFlight\models\Flight';

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        $verbs = parent::verbs();
        $verbs['data']    =  ['POST', 'HEAD'];
        return $verbs;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'options' => [
                'class' => 'yii\rest\OptionsAction',
            ],
            
        ];
    }

    /**
     * Возвращает общий массив по рейсу
     * который содержит
     * = данные рейса
     * = данные пассажиров
     * = данные по анкетировани.
     * @return array
     * @throws \Exception
     */
    public function actionData() // проверить запрос к бд
    {
        $output = [];
        $date = Yii::$app->request->post('date');
        $flt = Yii::$app->request->post('flt');
        if (!$date || !$flt) {
            throw new \Exception("Date or Flt incorrect");
        }
        
        //$flt,$date
        /** @var Flight $model */
        $model = $this->modelClass;
        $date = gmdate("Y-m-d", strtotime($date." UTC"));
        $day = Aims::DateToAimsDate($date);
        $flight = $model::find()->with(['passengers','depAirport','arrAirport',
                'passengers.questionnaireAnswers','passengers.questionnaireAnswers.question' ])
                ->where([
                    'flt' => $flt,
                    'day' => $day
                ])
                ->one();
                
        $output['flight'] = [
            'flt' => $flight->flt,
            'carrier' => $flight->carrier,
            'dep_airport' => $flight->depAirport->iata,
            'arr_airport' => $flight->arrAirport->iata,
            'std' => $flight->std,
        ];
        
        $output['passengers'] = [];
        if (count($flight->passengers)) {
            foreach ($flight->passengers as $pass) {
                $questionnaire = [];
                if (isset($pass->questionnaireAnswers->question->questionnaire_id)) {
                    $answers = [];
                    if (count($pass->questionnaireAnswers)) {
                        foreach ($pass->questionnaireAnswers as $answer) {
                            $answers[] = [
                                'text' =>   $answer->text
                            ];
                        }
                    }
                    $questionnaire['id'] = $pass->questionnaireAnswers->question->questionnaire_id;
                    $questionnaire['answers'] = $answers;
                }
                $output['passengers'][] = [
                    'surname' => $pass->surname,
                    'name' => $pass->name,
                    'patronymic' => $pass->patronymic,
                    'ticket' => $pass->ticket,
                    'contact' => '',
                    'menu' => [],
                    'review' => [],
                    'questionnaire' => $questionnaire,
                ];
            }
        }
        $output['menu'] = [];
        return $output;
    }
}
