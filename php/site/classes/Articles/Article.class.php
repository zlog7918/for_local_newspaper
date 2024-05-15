<?php
    namespace Articles;
    class Article {
        private int $id;
        private ?string $text=null;
        private ArticleType $type;
        private ?\DateTimeImmutable $publish_date=null;
        private ?\DateTimeImmutable $creation_date=null;
        private ?\DateTimeImmutable $last_edit_date=null;
        private bool $not_called_text=true;
        private bool $not_called_publish_date=true;
        private bool $not_called_creation_date=true;
        private bool $not_called_last_edit_date=true;

        public function __construct(int $id, ArticleType $aType) {
            $this->id=$id;
            $this->type=$aType;
        }

        public function get_id() :int {
            return $this->id;
        }

        public function get_text() {
            require 'connect.php';
            if($this->not_called_text) {
                $this->text=$db->get_article_text($this);
                $this->not_called_text=false;
            }
            return $this->text;
        }
        public function get_publish_date() {
            require 'connect.php';
            if($this->not_called_publish_date) {
                $this->publish_date=$db->get_article_publish_date($this);
                $this->not_called_publish_date=false;
            }
            return $this->text;
        }
        public function get_creation_date() {
            require 'connect.php';
            if($this->not_called_creation_date) {
                $this->creation_date=$db->get_article_creation_date($this);
                $this->not_called_creation_date=false;
            }
            return $this->text;
        }
        public function get_last_edit_date() {
            require 'connect.php';
            if($this->not_called_last_edit_date) {
                $this->last_edit_date=$db->get_article_last_edit_date($this);
                $this->not_called_last_edit_date=false;
            }
            return $this->text;
        }

        public function edit(string $text) :void
        public function is_archived() :bool
        public function is_published() :bool
        public function toggle_archive() :bool
    }
