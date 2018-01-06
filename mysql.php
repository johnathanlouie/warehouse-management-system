<?php

require_once 'config.php';

namespace wms;

class MysqlConnectionException extends \Exception {
    
}

class MysqlCharsetException extends \Exception {
    
}

class MysqlParamException extends \Exception {
    
}

class MysqlPreparationException extends \Exception {
    
}

class MysqlQueryException extends \Exception {
    
}

class MysqlGetResultException extends \Exception {
    
}

class Mysql {

    private $mysqli;

    public function __construct() {
        $this->mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
        if ($this->mysqli->connect_error) {
            throw new MysqlConnectionException($this->mysqli->connect_error, $this->mysqli->errno);
        }
        $this->mysqli->set_charset(DB_CHARSET);
        if ($this->mysqli->connect_error) {
            throw new MysqlCharsetException($this->mysqli->connect_error, $this->mysqli->errno);
        }
    }

    public function preparedQuery($sql, $types, $params) {
        $stmt = $this->mysqli->prepare($sql);
        if (!$stmt) {
            throw new MysqlPreparationException($this->mysqli->connect_error, $this->mysqli->errno);
        }
        if (!is_array($params)) {
            $params = [$params];
        }
        foreach ($params as $value) {
            $bind = $stmt->bind_param($types, ...$value);
            if (!$bind) {
                throw new MysqlParamException($stmt->error, $stmt->errno);
            }
            $exe = $stmt->execute();
            if (!$exe) {
                throw new MysqlParamException($stmt->error, $stmt->errno);
            }
        }
        $resultObj = $stmt->get_result();
        if ($this->mysqli->errno) {
            throw new MysqlGetResultException($stmt->error, $stmt->errno);
        }
        $results = null;
        if ($resultObj) {
            $results = $resultObj->fetch_all(MYSQLI_ASSOC);
            $resultObj->free();
        }
        $stmt->reset();
        $stmt->close();
        return $results;
    }

    public function query($sql) {
        $resultObj = $this->mysqli->query($sql);
        if ($resultObj === false) {
            throw new MysqlQueryException();
        } elseif ($resultObj === true) {
            return null;
        }
        return $resultObj->fetch_all(MYSQLI_ASSOC);
    }

    public function close() {
        $this->mysqli->close();
    }

}
