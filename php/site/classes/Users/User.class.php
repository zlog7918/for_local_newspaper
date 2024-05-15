<?php
    namespace Users;
    class User extends UsrBase {
        private int $id;
        private ?string $name=null;
        private ?string $nick=null;
        private ?string $surname=null;
        private ?string $academic_titles=null;
        // private bool $is_active=null;

        private bool $not_called_name=true;
        private bool $not_called_nick=true;
        private bool $not_called_surname=true;
        private bool $not_called_academic_titles=true;

        public function __construct(int $id) {
            $this->id=$id;
        }

        public function get_id() :int {
            return $this->id;
        }
        // private function set_name(string $name) :void {
        //     $this->name=$name;
        // }
        public function get_name() :string {
            require 'connect.php';
            if($this->not_called_name) {
                $this->name=$db->get_user_name($this);
                $this->not_called_name=false;
            }
            return $this->name;
        }
        // private function set_nick(string $nick) :void {
        //     $this->nick=$nick;
        // }
        public function get_nick() :string {
            require 'connect.php';
            if($this->not_called_nick) {
                $this->nick=$db->get_user_nick($this);
                $not_called_nick=false;
            }
            return $this->nick;
        }
        // private function set_surname(string $surname) :void {
        //     $this->surname=$surname;
        // }
        public function get_surname() :string {
            require 'connect.php';
            if($this->not_called_surname) {
                $this->surname=$db->get_user_surname($this);
                $not_called_surname=false;
            }
            return $this->surname;
        }
        // private function set_academic_titles(string $academic_titles) :void {
        //     $this->academic_titles=$academic_titles;
        // }
        public function get_academic_titles() :string {
            require 'connect.php';
            if($this->not_called_academic_titles) {
                $this->academic_titles=$db->get_user_academic_titles($this);
                $not_called_academic_titles=false;
            }
            return $this->academic_titles;
        }
        // protected function set_is_active(bool $is_active) :void {
        //     $this->is_active=$is_active;
        // }
        // public function get_is_active() :bool {
        //     return $this->is_active;
        // }

        public function add_article() :string { // void
        // public function log_in(string $title, string $text) :UsrBase {
            if(!(isset($_POST['title']) && isset($_POST['text'])))
                return ['error'=>true, 'message'=>'Title or content of article not sent.'];
            require 'connect.php';
            $ret=$db->add_article($this, $_POST['title'], $_POST['text']);
            return \json_encode($ret);
            
        }
        public function add_review(Article $a, string $text, int $degree) :string { // void

        }
        public function archive_article(Article $a) :string { // void

        }
        public function change_info(string $nick, string $name, string $surname, string $academic_titles) :string { // void

        }
        public function change_password() :string { // bool
        // public function change_password(string $pass, string $pass_repeat) :string { // bool
            if(!(isset($_POST['pass']) && isset($_POST['pass_repeat'])))
                return ['error'=>true, 'message'=>'Password or repeated password not sent.'];
            require 'connect.php';
            $ret=$db->change_pass($this, $pass);
            return \json_encode($ret);
        }
        public function dearchive_article(Article $a) :string { // void

        }
        public function delete_review(Review $r) :string { // void

        }
        public function edit_article(Article $a, string $text) :string { // void

        }
        public function edit_review(Review $r, string $text, int $degree) :string { // void

        }
        public function get_articles() :string { // array
        // public function get_articles(ArticleType ...$aTypes) :string { // array
            require 'connect.php';
            $ret=$db->change_pass($this, $pass);
            return \json_encode($ret);
        }
        public function get_display_name() :string {

        }
        public function get_reviews() :string { // array

        }
        public function publish_article(Article $a) :string { // void

        }

        public function is_logged() :bool { // bool
            return true;
        }
        public function log_out() :UnloggedUser {
            return new UnloggedUser();
        }
    }
    