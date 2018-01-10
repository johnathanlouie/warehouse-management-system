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

class User {

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

    public function getPassword() {
        return $this->password;
    }

    public function getRole() {
        return $this->role;
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

class Auth {

    const TABLE = 'users';
    const USERNAME = 'username';
    const PASSWORD = 'password';
    const FIRSTNAME = 'name_first';
    const LASTNAME = 'name_last';
    const ROLE = 'role';

    private static $instance;

    private function __construct() {
        session_start();
    }

    public static function getInstance() {
        if ($this->instance === null) {
            $this->instance = new Auth();
        }
        return $this->instance;
    }

    public function logout() {
        setcookie(session_name(), '', time() - 42000);
        session_destroy();
    }

    public function login($username, $password) {
        session_start();
        $user = $this->getUser($username);
        if ($user === null) {
            return false;
        }
        if (!$user->verifyPassword($password)) {
            return false;
        }
        $_SESSION['user'] = $user;
        $authKey = $this->authKey();
        $_SESSION['key'] = $authKey;
        setcookie('key', $authKey);
        return true;
    }

    public function isLoggedIn() {
        if (isset($_COOKIE['key']) && isset($_SESSION['key'])) {
            return $_COOKIE['key'] === $_SESSION['key'];
        }
        return false;
    }

    private function getUser($username) {
        $db = new Mysql();
        $AUTH_TABLE = Auth::TABLE;
        $AUTH_USER = Auth::USERNAME;
        $sql = "SELECT * FROM $AUTH_TABLE WHERE $AUTH_USER  = ?";
        $results = $db->preparedQuery($sql, 's', $username);
        $db->close();
        if (!isset($results[0])) {
            return null;
        }
        $name = new Name($results[Auth::FIRSTNAME], $results[Auth::LASTNAME]);
        $user = new User($name, $results[Auth::USERNAME], $results[Auth::PASSWORD], $results[Auth::ROLE]);
        return $user;
    }

    private function authKey() {
        return hash('sha256', $_SESSION["username"] . time() . random_int());
    }

}
