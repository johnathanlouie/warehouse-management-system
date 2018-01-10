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

function login() {
    $auth = Auth::getInstance();
    if ($auth->isLoggedIn()) {
        return new LoginStatus('Already logged in.', LoginStatus::SUCCESS);
    } elseif (!isset($_POST['username']) || !isset($_POST['password'])) {
        return new LoginStatus('Missing login information.', LoginStatus::FAIL);
    } elseif (!$auth->login($_POST['username'], $_POST['password'])) {
        return new LoginStatus('Wrong login information.', LoginStatus::FAIL);
    }
    return new LoginStatus('Successfully logged in.', LoginStatus::SUCCESS);
}

echo json_encode(login());
