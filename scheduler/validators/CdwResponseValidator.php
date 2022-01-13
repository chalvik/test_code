<?php
namespace common\modules\scheduler\validators;

use common\modules\scheduler\interfaces\ResponseValidator;

class CdwResponseValidator implements ResponseValidator
{
    private $_response;
    
    public function __construct($response)
    {
        $this->_response = $response;
    }
    
    public function validate()
    {
        return false;
    }
}
