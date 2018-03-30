<?php

namespace wms;

require_once 'loginstatus.php';

class LoginHandler {

    private static function respond() {
        $session = Session::getInstance();
        if ($session->isLoggedIn()) {
            return new LoginStatus('Already logged in.', LoginStatus::SUCCESS);
        } elseif (!isset($_POST['username']) || !isset($_POST['password'])) {
            return new LoginStatus('Missing login information.', LoginStatus::FAIL);
        } elseif (!$session->login($_POST['username'], $_POST['password'])) {
            return new LoginStatus('Wrong login information.', LoginStatus::FAIL);
        }
        return new LoginStatus('Successfully logged in.', LoginStatus::SUCCESS);
    }

    public static function handle() {
        echo json_encode(self::respond());
    }

}

LoginHandler::handle();
