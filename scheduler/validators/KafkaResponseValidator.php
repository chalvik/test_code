<?php
namespace common\modules\scheduler\validators;

use common\modules\scheduler\interfaces\ResponseValidator;
use common\components\ArrayHelper;

class KafkaResponseValidator implements ResponseValidator
{
    private $_response;
    
    public function __construct($response)
    {
        $this->_response = $response;   
    }
    
    public function validate()
    {
        if (!empty($this->_response)) {
            
            $this->beforeValidate();
            
            if (is_array($this->_response)) {
                if (array_key_exists('error_code', $this->_response) ||
                    (ArrayHelper::getValue($this->_response, 'message') == 'Unauthorized')
                ) {
                    return false;
                }
            }
            
            return true;
        }
    }
    
    private function beforeValidate()
    {
        if (is_string($this->_response)) {
            $this->_response = json_decode($this->_response, true);
        }
    }
}