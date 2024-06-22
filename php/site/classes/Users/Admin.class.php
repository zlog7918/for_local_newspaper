<?php
    namespace Users;
    class Admin extends User {
        public function __construct(int $id) {
            parent::__construct($id);
        }

        public function get_users() :string { // void
            require 'connect.php';
            $ret=$db->get_users($this);
            return \json_encode(isset($ret['error']) ? $ret:['error'=>false, 'data'=>$ret]);
        }
        public function view_user() :string { // void
        // public function view_user(User $u) :string { // User
            if(!isset($_GET['user_nr']))
                return \json_encode(['error'=>true, 'message'=>'ID of user not sent.']);
            require 'connect.php';
            $ret=$db->view_user($this, $_GET['user_nr']);
            return \json_encode($ret);
        }
        public function activate_user() :string { // void
        // public function activate_user(User $u) :string { // void
            if(!isset($_GET['user_nr']))
                return \json_encode(['error'=>true, 'message'=>'ID of user not sent.']);
            require 'connect.php';
            $ret=$db->activate_user($this, $_GET['user_nr']);
            return \json_encode(isset($ret['error']) ? $ret:['error'=>false, 'data'=>$ret]);
        }
        public function create_user() :string { // 
        // public function create_user(string $nick, string $name, string $surname, string $academic_titles, string $pass) :string { // 
            if(!(isset($_POST['nick']) && isset($_POST['name']) && isset($_POST['surname']) && isset($_POST['academic_titles']) && isset($_POST['pass'])))
                return \json_encode(['error'=>true, 'message'=>'Neccessary information about user not sent.']);
            require 'connect.php';
            $ret=$db->create_user($this, $_POST['nick'], $_POST['name'], $_POST['surname'], $_POST['academic_titles'], $_POST['pass']);
            return \json_encode($ret);
        }
        public function deactivate_user() :string { // void
        // public function deactivate_user(User $u) :string { // void
            if(!isset($_GET['user_nr']))
                return \json_encode(['error'=>true, 'message'=>'ID of user not sent.']);
            require 'connect.php';
            $ret=$db->deactivate_user($this, $_GET['user_nr']);
            return \json_encode(isset($ret['error']) ? $ret:['error'=>false, 'data'=>$ret]);
        }
        public function give_admin_rights() :string { // void
        // public function give_admin_rights(User $u) :string { // void
            if(!isset($_GET['user_nr']))
                return \json_encode(['error'=>true, 'message'=>'ID of user not sent.']);
            require 'connect.php';
            $ret=$db->elevate_user($this, $_GET['user_nr']);
            return \json_encode(isset($ret['error']) ? $ret:['error'=>false, 'data'=>$ret]);
        }
        public function revoke_admin_rights() :string { // void
        // public function revoke_admin_rights(User $u) :string { // void
            if(!isset($_GET['user_nr']))
                return \json_encode(['error'=>true, 'message'=>'ID of user not sent.']);
            require 'connect.php';
            $ret=$db->deelevate_user($this, $_GET['user_nr']);
            return \json_encode(isset($ret['error']) ? $ret:['error'=>false, 'data'=>$ret]);
        }
    }
    