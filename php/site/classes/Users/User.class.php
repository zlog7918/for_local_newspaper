<?php
    namespace Users;
    class User extends UsrBase {
        private int $id;
        private ?string $nick=null;
        private ?string $name=null;
        private ?string $surname=null;
        private ?string $academic_titles=null;
        private ?string $full_name=null;
        // private bool $is_active=null;

        public function __construct(int $id) {
            $this->id=$id;
            require 'connect.php';
            $info=$db->get_user_info($id);
            if($info['error'])
                return;
            $info=$info['data'];
            $this->nick=$info['nick'];
            $this->name=$info['name'];
            $this->surname=$info['surname'];
            $this->academic_titles=$info['academic_titles'];
            $this->full_name=(is_null($this->academic_titles) ? '':"{$this->academic_titles} ")."{$this->name} {$this->surname}";
        }

        public function get_id() :int {
            return $this->id;
        }
        public function get_name() :string {
            return $this->name;
        }
        public function get_nick() :string {
            return $this->nick;
        }
        public function get_surname() :string {
            return $this->surname;
        }
        public function get_academic_titles() :string {
            return $this->academic_titles;
        }
        public function get_display_name() :string {
            return $this->full_name;
        }

        public function add_article() :string { // void
        // public function log_in(string $title, string $text) :UsrBase {
            if(!(isset($_POST['title']) && isset($_POST['text'])))
                return \json_encode(['error'=>true, 'message'=>'Title or content of article not sent.']);
            require 'connect.php';
            $ret=$db->add_article($this, $_POST['title'], $_POST['text']);
            return \json_encode($ret);
        }
        public function add_review() :string { // void
        // public function add_review(Article $a, string $text, int $degree) :string { // void
            if(!(isset($_POST['article_nr']) && isset($_POST['text']) && isset($_POST['degree'])))
                return \json_encode(['error'=>true, 'message'=>'Degree, text of review or ID of article not sent.']);
            require 'connect.php';
            $ret=$db->add_review($this, $_POST['article_nr'], $_POST['degree'], $_POST['text']);
            return \json_encode(isset($ret['error']) ? $ret:['error'=>false, 'data'=>$ret]);
        }
        public function view_review() :string { // void
        // public function view_review(Review $r) :string { // void
            if(!isset($_GET['review_nr']))
                return \json_encode(['error'=>true, 'message'=>'ID of review not sent.']);
            require 'connect.php';
            $ret=$db->view_review($this, $_GET['review_nr']);
            return \json_encode(isset($ret['error']) ? $ret:['error'=>false, 'data'=>$ret]);
        }
        public function archive_article() :string { // void
        // public function archive_article(Article $a) :string { // void
            if(!isset($_GET['article_nr']))
                return \json_encode(['error'=>true, 'message'=>'ID of article not sent.']);
            require 'connect.php';
            $ret=$db->archive_article($this, $_GET['article_nr']);
            return \json_encode(isset($ret['error']) ? $ret:['error'=>false, 'data'=>$ret]);
        }
        public function change_info() :string { // void
        // public function change_info(string $nick, string $name, string $surname, string $academic_titles) :string { // void
            if(!(isset($_POST['nick']) && isset($_POST['name']) && isset($_POST['surname']) && isset($_POST['academic_titles'])))
                return \json_encode(['error'=>true, 'message'=>'Neccessary information about user not sent.']);
            require 'connect.php';
            $ret=$db->change_info($this, $_POST['nick'], $_POST['name'], $_POST['surname'], $_POST['academic_titles']);
            return \json_encode($ret);
        }
        public function change_password() :string { // bool
        // public function change_password(string $pass, string $pass_rep) :string { // bool
            if(!(isset($_POST['pass']) && isset($_POST['pass_rep'])))
                return \json_encode(['error'=>true, 'message'=>'Password or repeated password not sent.']);
            require 'connect.php';
            $ret=$db->change_pass($this, $_POST['pass'], $_POST['pass_rep']);
            return \json_encode($ret);
        }
        public function dearchive_article() :string { // void
        // public function dearchive_article(Article $a) :string { // void
            if(!isset($_GET['article_nr']))
                return \json_encode(['error'=>true, 'message'=>'ID of article not sent.']);
            require 'connect.php';
            $ret=$db->dearchive_article($this, $_GET['article_nr']);
            return \json_encode(isset($ret['error']) ? $ret:['error'=>false, 'data'=>$ret]);
        }
        public function delete_review() :string { // void
        // public function delete_review(Review $r) :string { // void
            if(!isset($_GET['review_nr']))
                return \json_encode(['error'=>true, 'message'=>'ID of review not sent.']);
            require 'connect.php';
            $ret=$db->delete_review($this, $_GET['review_nr']);
            return \json_encode(isset($ret['error']) ? $ret:['error'=>false, 'data'=>$ret]);
        }
        public function edit_article() :string { // void
        // public function edit_article(Article $a, string $text) :string { // void
            if(!(isset($_POST['article_nr']) && isset($_POST['text'])))
                return \json_encode(['error'=>true, 'message'=>'ID or content of article not sent.']);
            require 'connect.php';
            $ret=$db->edit_article($this, $_POST['article_nr'], $_POST['text']);
            return \json_encode($ret);
        }
        public function edit_review() :string { // void
        // public function edit_review(Review $r, string $text, int $degree) :string { // void
            if(!(isset($_POST['review_nr']) && isset($_POST['text']) && isset($_POST['degree'])))
                return \json_encode(['error'=>true, 'message'=>'ID of article not sent.']);
            require 'connect.php';
            $ret=$db->edit_review($this, $_POST['review_nr'], $_POST['degree'], $_POST['text']);
            return \json_encode(isset($ret['error']) ? $ret:['error'=>false, 'data'=>$ret]);
        }
        public function get_reviews() :string { // array
        // public function get_reviews(int $id) :string { // array
            if(!isset($_GET['article_nr']))
                return \json_encode(['error'=>true, 'message'=>'ID of article not sent.']);
            require 'connect.php';
            $ret=$db->get_reviews($this, $_GET['article_nr']);
            return \json_encode(isset($ret['error']) ? $ret:['error'=>false, 'data'=>$ret]);
        }
        public function publish_article() :string { // void
        // public function publish_article(Article $a) :string { // void
            if(!isset($_GET['article_nr']))
                return \json_encode(['error'=>true, 'message'=>'ID of article not sent.']);
            require 'connect.php';
            $ret=$db->publish_article($this, $_GET['article_nr']);
            return \json_encode(isset($ret['error']) ? $ret:['error'=>false, 'data'=>$ret]);
        }
        public function unpublish_article() :string { // void
        // public function unpublish_article(Article $a) :string { // void
            if(!isset($_GET['article_nr']))
                return \json_encode(['error'=>true, 'message'=>'ID of article not sent.']);
            require 'connect.php';
            $ret=$db->unpublish_article($this, $_GET['article_nr']);
            return \json_encode(isset($ret['error']) ? $ret:['error'=>false, 'data'=>$ret]);
        }
        public function is_logged() :bool { // bool
            return true;
        }
        public function log_out() :UnloggedUser {
            return new UnloggedUser();
        }
    }
    