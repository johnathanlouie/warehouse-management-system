<?php

namespace wms;

require_once 'auth.php';

class LoginStatus {

    const SUCCESS = 0;
    const FAIL = 1;

    private $message;
    private $code;

    public function __construct($message, $code) {
        $this->message = $message;
        $this->code = $code;
    }

}
