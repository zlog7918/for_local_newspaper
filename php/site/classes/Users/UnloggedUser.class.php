<?php
    namespace Users;
    class UnloggedUser extends UsrBase {
        private ?string $mess=null;
        public function __construct() {}
        private function set_err_mess(string $message) :UnloggedUser{
            $this->mess=$message;
            return $this;
        }

        public function is_logged() :bool {
            return false;
        }
        public function is_err() :string|bool {
            return ($this->mess==null ? false:$this->mess);
        }
        public function log_in() :UsrBase {
        // public function log_in(string $nick, string $pass) :UsrBase {
            if(!(isset($_POST['nick']) && isset($_POST['pass'])))
                return (new UnloggedUser())->set_err_mess('Nick or password not sent.');
            require 'connect.php';
            // $db->change_pass_n($_POST['nick'], $_POST['pass']);
            $ret=$db->log_in($_POST['nick'], $_POST['pass']);
            if($ret['error'])
                return (new UnloggedUser())->set_err_mess($ret['message']);
            else
                return ($ret['is_admin'] ? (new Admin($ret['id'])):(new User($ret['id'])));
        }
    }
