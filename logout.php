<?php

namespace wms;

require_once 'loginstatus.php';

class LogoutHandler {

    private static function respond() {
        if (!Session::getInstance()->isLoggedIn()) {
            return new LoginStatus('Not logged in.', LoginStatus::SUCCESS);
        }
        return new LoginStatus('Successfully logged out.', LoginStatus::SUCCESS);
    }

    public static function handle() {
        echo json_encode(self::respond());
    }

}

LogoutHandler::handle();
