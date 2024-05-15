<?php
    namespace Users;
    class Admin extends User {
        public function __construct(int $id) {
            parent::__construct($id);
        }

        public function activate_user(User $u) :void {

        }
        public function create_user(string $nick, string $name, string $surname, string $academic_titles, string $pass) :void {

        }
        public function deactivate_user(User $u) :void {

        }
        public function give_admin_rights(User $u) :void {

        }
        public function revoke_admin_rights(User $u) :void {

        }
    }
    