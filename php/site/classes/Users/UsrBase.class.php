<?php
    namespace Users;
    abstract class UsrBase {
        public abstract function is_logged() :bool;

        public function get_articles() :string { // array
        // public function get_articles(ArticleType ...$aTypes) :string { // array
            require 'connect.php';
            $ret=$db->get_articles($this);
            return \json_encode(isset($ret['error']) ? $ret:['error'=>false, 'data'=>$ret]);
        }
        public function view_article() :string { // array
        // public function view_article(int $id) :string { // array
            if(!isset($_GET['article_nr']))
                return \json_encode(['error'=>true, 'message'=>'ID of article not sent.']);
            require 'connect.php';
            $ret=$db->view_article($this, $_GET['article_nr']);
            return \json_encode(isset($ret['error']) ? $ret:['error'=>false, 'data'=>$ret]);
        }
    }
    