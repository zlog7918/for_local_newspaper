<?php
    namespace DB;
    class DB_Queries {
        private \PDO $db;
        private static string $DATETIME_FORMAT='Y-m-d H:i:s';
        public function __construct(string $connect_str, string $usr, string $pass) {
            try {
                $this->db=new \PDO(
                    $connect_str
                    ,$usr
                    ,$pass
                    ,[\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
                );
            } catch(\PDOException $e) {
                unset($e);
                $this->db=false;
            }
        }

        public function is_connection() :bool {
            return $this->db!==false;
        }

        // public function create_usr(string $nick, /*string $name, string $surname, string $academic_titles,*/ string $pass) :array {
        //     $action_name='create_user';
        //     $ret=['error'=>true, 'message'=>'Password did not pass validation'];
        //     if(validate_pass($pass)) {
        //         $q=$this->db->prepare('
        //             INSERT usr SET
        //                 password=:passwd
        //             WHERE id=:id
        //         ');
        //         $key_pass=\EnDeCoder\EnDeCoder::getSymmetricKey(\EnDeCoder\EnDeCoderOpt::PASS_KEY);
        //         try {
        //             $q->execute([
        //                 ':id'=>$u->get_id(),
        //                 ':passwd'=>\EnDeCoder\EnDeCoder::simpleEncrypt(
        //                     \password_hash($pass, PASSWORD_DEFAULT),
        //                     $key_pass
        //                 )
        //             ]);
        //             if($q->rowCount()>0) {
        //                 self::log_activity($u, true, $action_name, [
        //                     'desc'=>'user changed password'
        //                 ]);
        //                 return ['error'=>false];
        //             }
        //             self::log_activity($u, false, $action_name, [
        //                 'err'=>'statment executed no affected rows'
        //             ]);
        //             $ret['message']='Unknown user';
        //             return $ret;
        //         } catch(Error $e) {
        //             self::log_activity($u, false, $action_name, [
        //                 'err'=>$e->__toString()
        //             ]);
        //             $ret['message']='Unknown error';
        //             return $ret;
        //         }
        //     }
        //     return $ret;
        // }

        public function change_pass(\Users\User $u, string $pass) :array {
            $action_name='ch_pass';
            $ret=['error'=>true, 'message'=>'Password did not pass validation'];
            if(validate_pass($pass)) {
                $q=$this->db->prepare('
                    UPDATE usr SET
                        password=:passwd
                    WHERE id=:id
                ');
                $key_pass=\EnDeCoder\EnDeCoder::getSymmetricKey(\EnDeCoder\EnDeCoderOpt::PASS_KEY);
                try {
                    $q->execute([
                        ':id'=>$u->get_id(),
                        ':passwd'=>\EnDeCoder\EnDeCoder::simpleEncrypt(
                            \password_hash($pass, PASSWORD_DEFAULT),
                            $key_pass
                        )
                    ]);
                    if($q->rowCount()>0) {
                        self::log_activity($u, true, $action_name, [
                            'desc'=>'user changed password'
                        ]);
                        return ['error'=>false];
                    }
                    self::log_activity($u, false, $action_name, [
                        'err'=>'statment executed no affected rows'
                    ]);
                    $ret['message']='Unknown user';
                    return $ret;
                } catch(Error $e) {
                    self::log_activity($u, false, $action_name, [
                        'err'=>$e->__toString()
                    ]);
                    $ret['message']='Unknown error';
                    return $ret;
                }
            }
            return $ret;
        }

        // public function change_pass_n(string $u, string $pass) :array {
        //     $action_name='ch_pass';
        //     $ret=['error'=>true, 'message'=>'Password did not pass validation'];
        //     if(validate_pass($pass)) {
        //         $q=$this->db->prepare('
        //             UPDATE usr SET
        //                 password=:passwd
        //             WHERE nick=:nick
        //         ');
        //         $key_pass=\EnDeCoder\EnDeCoder::getSymmetricKey(\EnDeCoder\EnDeCoderOpt::PASS_KEY);
        //         try {
        //             $q->execute([
        //                 ':nick'=>$u,
        //                 ':passwd'=>\EnDeCoder\EnDeCoder::simpleEncrypt(
        //                     password_hash($pass, PASSWORD_DEFAULT),
        //                     $key_pass
        //                 )
        //             ]);
        //             if($q->rowCount()>0) {
        //                 return ['error'=>false];
        //             }
        //             $ret['message']='Unknown user';
        //             return $ret;
        //         } catch(Error $e) {
        //             $ret['message']='Unknown error';
        //             return $ret;
        //         }
        //     }
        //     return $ret;
        // }

        public function add_article(\Users\User $u, string $title, string $text) :array {
            $action_name='add_article';
            $ret=['error'=>true, 'message'=>'Article creation was not successful.'];
            $q=$this->db->prepare('
                INSERT INTO article(author_id, title, "text", creation_date) VALUES
                (:id, :title, :txt, :cr_date)
                RETURNING id
            ');
            try {
                $q->execute([
                    ':id'=>$u->get_id(),
                    ':title'=>$title,
                    ':txt'=>$text,
                    ':cr_date'=>self::get_timestamp(),
                ]);
                if(count($q=$q->fetchAll(\PDO::FETCH_ASSOC))>0) {
                    $q=$q[0];
                    self::log_activity($u, true, $action_name, [
                        'desc'=>"article (id: {$q['id']})"
                    ]);
                    return ['error'=>false, 'id'=>$q['id']];
                }
                self::log_activity($u, false, $action_name, [
                    'desc'=>'article not added - unknown no error'
                ]);
            } catch(\PDOException $e) {
                if(strpos($e->getMessage(), 'duplicate key value')===false) {
                    self::log_activity($u, false, $action_name, [
                        'desc'=>'article not added - error',
                        'err'=>$e->__toString()
                    ]);
                    $ret['message']='Unknown error';
                } else {
                    self::log_activity($u, false, $action_name, [
                        'desc'=>'article not added - possible copyright violation',
                        'err'=>$e->__toString()
                    ]);
                    $ret['message']='Possible copyright violation - has been reported.';
                }
            }
            return $ret;
        }

        public function log_in(string $nick, string $pass) :array {
            $action_name='log_in';
            $ret=['error'=>true, 'message'=>'Wrong nick or password'];
            if(validate_pass($pass)) {
                $q=$this->db->prepare('
                    SELECT
                        id, password, is_admin, is_active
                    FROM usr
                    WHERE nick=:nick AND loggin_tries<4
                ');
                $key_pass=\EnDeCoder\EnDeCoder::getSymmetricKey(\EnDeCoder\EnDeCoderOpt::PASS_KEY);
                try {
                    $q->execute([':nick'=>$nick]);
                    if(count($q=$q->fetchAll(\PDO::FETCH_ASSOC))>0) {
                        $q=$q[0];
                        if($q['is_active']==true) {
                            $q['password']=stream_get_contents($q['password']);
                            if(password_verify(
                                $pass,
                                \EnDeCoder\EnDeCoder::simpleDecrypt(
                                    $q['password'],
                                    $key_pass
                                )
                            )) {
                                self::log_activity($q['id'], true, $action_name, [
                                    'desc'=>'user logged'
                                ]);
                                return ['error'=>false, 'id'=>$q['id'], 'is_admin'=>$q['is_admin']];
                            } else {
                                self::log_activity($q['id'], false, $action_name, [
                                    'desc'=>'wrong pass'
                                ]);
                            }
                        } else {
                            self::log_activity($q['id'], true, $action_name, [
                                'desc'=>'inactive user tries to log_in'
                            ]);
                            $ret['message']='User is inactive';
                        }
                    }
                    return $ret;
                } catch(\PDOException $e) {
                    self::log_activity(null, false, $action_name, [
                        'err'=>$e->__toString()
                    ]);
                    $ret['message']='Unknown error';
                    return $ret;
                }
            }
            $ret['message']='Password did not pass validation';
            return $ret;
        }

        public function get_user_nick(\Users\User $u) :string {
            return self::get_user_info($u, 'nick');
        }
        public function get_user_name(\Users\User $u) :string {
            return self::get_user_info($u, 'name');
        }
        public function get_user_surname(\Users\User $u) :string {
            return self::get_user_info($u, 'surname');
        }
        public function get_user_academic_titles(\Users\User $u) :string {
            return self::get_user_info($u, 'academic_titles');
        }
        private function get_user_info(\Users\User $u, string $prop_name) :string {
            $action_name="get_user_info: $prop_name";
            $ret='Error: User does not exist';
            $q='
                SELECT
                    :col_name
                FROM usr
                WHERE id=:id
            ';
            $q=str_replace(':col_name', $prop_name, $q);
            $q=$this->db->prepare($q);
            try {
                $q->execute([':id'=>$u->get_id()]);
                if(count($q=$q->fetchAll(\PDO::FETCH_ASSOC))>0) {
                    return $q[0][$prop_name];
                }
                return $ret;
            } catch(\PDOException $e) {
                self::log_activity(
                    $u,
                    false,
                    $action_name,
                    ['err'=>$e->__toString()]
                );
                return 'Error: Unknown error';
            }
        }

        public function get_article_text(\Articles\Article $a) :string {
            return self::get_article_info($u, '"text"');
        }
        public function get_article_publish_date(\Articles\Article $a) :?\DateTimeImmutable {
            $d=self::get_article_info($u, 'publish_date');
            return $d===null ? null:(new \DateTimeImmutable::createFromFormat($DATETIME_FORMAT, $d));
        }
        public function get_article_creation_date(\Articles\Article $a) :\DateTimeImmutable {
            $d=self::get_article_info($u, 'creation_date');
            return $d===null ? null:(new \DateTimeImmutable::createFromFormat($DATETIME_FORMAT, $d));
        }
        public function get_article_last_edit_date(\Articles\Article $a) :?\DateTimeImmutable {
            $d=self::get_article_info($u, 'last_edit_date');
            return $d===null ? null:(new \DateTimeImmutable::createFromFormat($DATETIME_FORMAT, $d));
        }
        private function get_article_info(\Articles\Article $a, string $prop_name) :?string {
            $action_name="get_article_info: $prop_name";
            $ret='Error: Article does not exist';
            $q='
                SELECT
                    :col_name
                FROM article
                WHERE id=:id
            ';
            $q=str_replace(':col_name', $prop_name, $q);
            $q=$this->db->prepare($q);
            try {
                $q->execute([':id'=>$a->get_id()]);
                if(count($q=$q->fetchAll(\PDO::FETCH_ASSOC))>0) {
                    return $q[0][$prop_name];
                }
                return $ret;
            } catch(\PDOException $e) {
                self::log_activity(
                    null,
                    false,
                    $action_name,
                    ['err'=>$e->__toString(), 'article_id'=>$a->get_id()]
                );
                return 'Error: Unknown error';
            }
        }

        private function log_activity(int|\Users\User|null $u, bool $is_success, string $action, array $log) :void {
            try {
                $q=$this->db->prepare('
                    INSERT INTO log(user_id, ip, is_success, action, timest, log) VALUES
                        (:usr, :ip, :is_success, :act, :timest, :log)
                ');
                $q->execute([
                    ':usr'=>($u instanceof \Users\User ? $u->get_id():$u),
                    ':is_success'=>($is_success ? 1:0),
                    ':ip'=>$_SERVER['X_REAL_IP'],
                    ':act'=>$action,
                    ':timest'=>self::get_timestamp(),
                    ':log'=>json_encode($log)
                ]);
            } catch(\PDOException $e) {
                echo '<h1>Operation failed, please inform an administrator.</h1>';
            }
        }

        private function get_timestamp() :string
        {
            return date($DATETIME_FORMAT);
        }
    }
