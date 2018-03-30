<?php

namespace wms;

require_once 'mysql.php';

class Name {

    private $first;
    private $last;

    public function __construct($first, $last) {
        $this->first = $first;
        $this->last = $last;
    }

    public function getFull() {
        return $this->first . ' ' . $this->last;
    }

    public function getComma() {
        return $this->last . ', ' . $this->first;
    }

    public function getFirst() {
        return $this->first;
    }

    public function getLast() {
        return $this->last;
    }

    public function setFirst($first) {
        $this->first = $first;
    }

    public function setLast($last) {
        $this->last = $last;
    }

}

class Auth {

    private $name;
    private $username;
    private $password;
    private $role;

    public function __construct($name, $username, $password, $role) {
        $this->name = $name;
        $this->username = $username;
        $this->password = $password;
        $this->role = $role;
    }

    public function verifyPassword($password) {
        return password_verify($password, $this->password);
    }

    public function getName() {
        return $this->name;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getRole() {
        return $this->role;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function setPassword($password) {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }

    public function setRole($role) {
        $this->role = $role;
    }

}

class AuthDao {

    const TABLE = 'users';
    const USERID = 'id';
    const USERNAME = 'username';
    const PASSWORD = 'password';
    const FIRSTNAME = 'name_first';
    const LASTNAME = 'name_last';
    const ROLE = 'role';

    public static function getUser($username) {
        $db = new Mysql();
        $DB_TABLE = self::TABLE;
        $DB_USER = self::USERNAME;
        $DB_PW = self::PASSWORD;
        $DB_FN = self::FIRSTNAME;
        $DB_LN = self::LASTNAME;
        $DB_ROLE = self::ROLE;
        $sql = "SELECT $DB_USER, $DB_PW, $DB_ROLE, $DB_FN, $DB_LN FROM $DB_TABLE WHERE $DB_USER  = ?";
        $results = $db->preparedQuery($sql, 's', $username);
        $db->close();
        if (!isset($results[0])) {
            return null;
        }
        $name = new Name($results[self::FIRSTNAME], $results[self::LASTNAME]);
        $user = new Auth($name, $results[self::USERNAME], $results[self::PASSWORD], $results[self::ROLE]);
        return $user;
    }
    
    public static function setup(){}
    
    public static function createUser(){}
    
    public static function updateUser(){}

}

class Session {

    const COOKIE_AUTH_TOKEN = 'key';
    const SESSION_AUTH_TOKEN = 'key';
    const SESSION_AUTH_DATA_OBJECT = 'key';

    public function login($username, $password) {
        session_start();
        $user = $this->getUser($username);
        if ($user === null) {
            return false;
        }
        if (!$user->verifyPassword($password)) {
            return false;
        }
        $_SESSION[self::SESSION_AUTH_DATA_OBJECT] = $user;
        $authKey = $this->authKey();
        $_SESSION[self::SESSION_AUTH_TOKEN] = $authKey;
        setcookie('key', $authKey);
        return true;
    }

    public static function logout() {
        setcookie(session_name(), '', time() - 42000);
        setcookie(self::COOKIE_AUTH_TOKEN, '', time() - 42000);
        session_destroy();
    }

    public static function isLoggedIn() {
        session_start();
        if (isset($_COOKIE[self::COOKIE_AUTH_TOKEN]) && isset($_SESSION[self::SESSION_AUTH_TOKEN])) {
            return $_COOKIE[self::COOKIE_AUTH_TOKEN] === $_SESSION[self::SESSION_AUTH_TOKEN];
        }
        return false;
    }

    private static function authToken() {
        return hash('sha256', $_SESSION[self::SESSION_AUTH_DATA_OBJECT]->getUsername() . time() . random_int());
    }

}
