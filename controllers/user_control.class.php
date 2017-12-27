<?php
/**
 * Created by PhpStorm.
 * User: kalivoda
 * Date: 05.12.2017
 * Time: 11:56
 */

include_once "models/db.php";

class user_control {

    private $user_id;

    private $user_nick;

    private $user_role;

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     */
    private function setUserId($user_id): void
    {
        $this->user_id = $user_id;
    }

    /**
     * @return mixed
     */
    public function getUserNick()
    {
        return $this->user_nick;
    }

    /**
     * @param mixed $user_nick
     */
    private function setUserNick($user_nick): void
    {
        $this->user_nick = $user_nick;
    }

    /**
     * @return mixed
     */
    public function getUserRole()
    {
        return $this->user_role;
    }

    /**
     * @param mixed $user_role
     */
    private function setUserRole($user_role): void
    {
        $this->user_role = $user_role;
    }

    public function logUser() {
        $valid = db::selectOne("SELECT users_id, role FROM users WHERE nickname = ? AND password = sha1(?)", array($_POST['nickname'], $_POST['password']));
        if($valid) {
            $this->setUserId($valid['users_id']);
            $this->setUserNick($_POST['nickname']);
            $this->setUserRole($valid['role']);
            return "";
        } else {
            return "login or password is incorrect!";
        }
    }

    public function logOut() {
        unset($_SESSION['user']);
    }

    public function userExists($nickname) {
        $exists = db::selectAll("SELECT users_id FROM users WHERE nickname = ? LIMIT 1", array($nickname));
        if($exists) {
            return true;
        } else {
            return false;
        }
    }

    public function registerUser() {
        if(!$this->userExists($_POST["nickname"])) {
            db::insert('users', array('nickname' => $_POST['nickname'],'password' => sha1($_POST['password']),'email' => $_POST['email']));
            return "Excellent, your account was created! You can log in now :-)";
        } else {
            return "this nickname is already in use!";
        }
    }

    public static function getUser($user_id) {
        $result = db::selectAll("SELECT * FROM users WHERE users_id = ? LIMIT 1", array($user_id));
        return $result;
    }
}