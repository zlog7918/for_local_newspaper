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
                \unset($e);
                $this->db=false;
            }
        }

        public function is_connection() :bool {
            return $this->db!==false;
        }

        public function create_user(\Users\Admin $u, string $nick, string $name, string $surname, string $academic_titles, string $pass) :array {
            $ret=['error'=>true, 'message'=>'Password did not pass validation'];
            if(\validate_pass($pass)) {
                $q=$this->db->prepare('
                    INSERT INTO usr(nick, "name", surname, academic_titles, password) VALUES
                    (:nick, :name, :surname, :academic_titles, :passwd)
                    RETURNING id
                ');
                $key_pass=\EnDeCoder\EnDeCoder::getSymmetricKey(\EnDeCoder\EnDeCoderOpt::PASS_KEY);
                try {
                    $q->execute([
                        ':nick'=>$nick,
                        ':name'=>$name,
                        ':surname'=>$surname,
                        ':academic_titles'=>$academic_titles,
                        ':passwd'=>\EnDeCoder\EnDeCoder::simpleEncrypt(
                            \password_hash($pass, PASSWORD_DEFAULT),
                            $key_pass
                        )
                    ]);
                    if(\count($q=$q->fetchAll(\PDO::FETCH_ASSOC))>0) {
                        $q=$q[0];
                        self::log_activity($u, true, __FUNCTION__, [
                            'desc'=>"user (id: {$q['id']}) created"
                        ]);
                        return ['error'=>false, 'id'=>$q['id']];
                    }
                    self::log_activity($u, false, __FUNCTION__, [
                        'err'=>'statment executed no affected rows'
                    ]);
                    $ret['message']='Unknown error';
                    return $ret;
                } catch(Error $e) {
                    self::log_activity($u, false, __FUNCTION__, [
                        'err'=>$e->__toString()
                    ]);
                    $ret['message']='Unknown error';
                    return $ret;
                }
            }
            return $ret;
        }

        public function change_pass(\Users\User $u, string $pass, string $pass_rep) :array {
            $ret=['error'=>true, 'message'=>'Password did not pass validation'];
            if($pass!==$pass_rep) {
                $ret['message']='Passwords don\'t match';
                return $ret;
            }
            if(!\validate_pass($pass)) {
                return $ret;
            }
            $q=$this->db->prepare('
                UPDATE usr SET
                    password=:passwd
                WHERE id=:id
                RETURNING id
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
                if(\count($q=$q->fetchAll(\PDO::FETCH_ASSOC))>0) {
                    self::log_activity($u, true, __FUNCTION__, [
                        'desc'=>'user changed password'
                    ]);
                    return ['error'=>false];
                }
                self::log_activity($u, false, __FUNCTION__, [
                    'err'=>'statment executed no affected rows'
                ]);
                $ret['message']='Unknown user';
                return $ret;
            } catch(Error $e) {
                self::log_activity($u, false, __FUNCTION__, [
                    'err'=>$e->__toString()
                ]);
                $ret['message']='Unknown error';
                return $ret;
            }
        }

        public function change_info(\Users\User $u, string $nick, string $name, string $surname, string $academic_titles) :array {
            $ret=['error'=>true, 'message'=>'Unknown user'];
            $q=$this->db->prepare('
                UPDATE usr SET
                    nick=:nick
                    ,"name"=:name
                    ,surname=:surname
                    ,academic_titles=:academic_titles
                WHERE id=:id
                RETURNING id
            ');
            try {
                $q->execute([
                    ':id'=>$u->get_id(),
                    ':nick'=>$nick,
                    ':name'=>$name,
                    ':surname'=>$surname,
                    ':academic_titles'=>$academic_titles,
                ]);
                if(\count($q=$q->fetchAll(\PDO::FETCH_ASSOC))>0) {
                    self::log_activity($u, true, __FUNCTION__, [
                        'desc'=>'user info'
                    ]);
                    return ['error'=>false];
                }
                self::log_activity($u, false, __FUNCTION__, [
                    'err'=>'statment executed no affected rows'
                ]);
                return $ret;
            } catch(Error $e) {
                self::log_activity($u, false, __FUNCTION__, [
                    'err'=>$e->__toString()
                ]);
                $ret['message']='Unknown error';
                return $ret;
            }
        }

        // public function change_pass_n(string $u, string $pass) :array {
        //     $ret=['error'=>true, 'message'=>'Password did not pass validation'];
        //     if(\validate_pass($pass)) {
        //         $q=$this->db->prepare('
        //             UPDATE usr SET
        //                 password=:passwd
        //             WHERE nick=:nick
        //             RETURNING id
        //         ');
        //         $key_pass=\EnDeCoder\EnDeCoder::getSymmetricKey(\EnDeCoder\EnDeCoderOpt::PASS_KEY);
        //         try {
        //             $q->execute([
        //                 ':nick'=>$u,
        //                 ':passwd'=>\EnDeCoder\EnDeCoder::simpleEncrypt(
        //                     \password_hash($pass, PASSWORD_DEFAULT),
        //                     $key_pass
        //                 )
        //             ]);
        //             if(\count($q=$q->fetchAll(\PDO::FETCH_ASSOC))>0) {
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
                if(\count($q=$q->fetchAll(\PDO::FETCH_ASSOC))>0) {
                    $q=$q[0];
                    self::log_activity($u, true, __FUNCTION__, [
                        'desc'=>"article (id: {$q['id']})"
                    ]);
                    return ['error'=>false, 'id'=>$q['id']];
                }
                self::log_activity($u, false, __FUNCTION__, [
                    'desc'=>'article not added - unknown, no error'
                ]);
            } catch(\PDOException $e) {
                if(\strpos($e->getMessage(), 'duplicate key value')===false) {
                    self::log_activity($u, false, __FUNCTION__, [
                        'desc'=>'article not added - error',
                        'err'=>$e->__toString()
                    ]);
                    $ret['message']='Unknown error';
                } else {
                    self::log_activity($u, false, __FUNCTION__, [
                        'desc'=>'article not added - possible copyright violation',
                        'err'=>$e->__toString()
                    ]);
                    $ret['message']='Possible copyright violation - has been reported.';
                }
            }
            return $ret;
        }

        public function edit_article(\Users\User $u, int $article_id, string $text) :array {
            $ret=['error'=>true, 'message'=>'Article does not exist or user does not have access.'];
            $q=$this->db->prepare('
                UPDATE article SET
                    "text"=:txt
                    ,last_edit_date=:ed_date
                WHERE
                    id=:id
                    AND author_id=:uid
                RETURNING id
            ');
            try {
                $q->execute([
                    ':id'=>$article_id,
                    ':uid'=>$u->get_id(),
                    ':txt'=>$text,
                    ':ed_date'=>self::get_timestamp(),
                ]);
                if(\count($q=$q->fetchAll(\PDO::FETCH_ASSOC))>0) {
                    self::log_activity($u, true, __FUNCTION__, [
                        'desc'=>"article (id: $article_id) edited"
                    ]);
                    return ['error'=>false];
                }
                self::log_activity($u, false, __FUNCTION__, [
                    'desc'=>'article not edited - unknown, no error'
                ]);
            } catch(\PDOException $e) {
                if(\strpos($e->getMessage(), 'duplicate key value')===false) {
                    self::log_activity($u, false, __FUNCTION__, [
                        'desc'=>'article not edited - error',
                        'err'=>$e->__toString()
                    ]);
                    $ret['message']='Unknown error';
                } else {
                    self::log_activity($u, false, __FUNCTION__, [
                        'desc'=>'article not edited - possible copyright violation',
                        'err'=>$e->__toString()
                    ]);
                    $ret['message']='Possible copyright violation - has been reported.';
                }
            }
            return $ret;
        }

        public function log_in(string $nick, string $pass) :array {
            $ret=['error'=>true, 'message'=>'Wrong nick or password'];
            if(\validate_pass($pass)) {
                $q=$this->db->prepare('
                    SELECT
                        id, password, is_admin, is_active
                    FROM usr
                    WHERE nick=:nick AND loggin_tries<4
                ');
                $key_pass=\EnDeCoder\EnDeCoder::getSymmetricKey(\EnDeCoder\EnDeCoderOpt::PASS_KEY);
                try {
                    $q->execute([':nick'=>$nick]);
                    if(\count($q=$q->fetchAll(\PDO::FETCH_ASSOC))>0) {
                        $q=$q[0];
                        if($q['is_active']==true) {
                            $q['password']=\stream_get_contents($q['password']);
                            if(\password_verify(
                                $pass,
                                \EnDeCoder\EnDeCoder::simpleDecrypt(
                                    $q['password'],
                                    $key_pass
                                )
                            )) {
                                self::log_activity($q['id'], true, __FUNCTION__, [
                                    'desc'=>'user logged'
                                ]);
                                return ['error'=>false, 'id'=>$q['id'], 'is_admin'=>$q['is_admin']];
                            } else {
                                self::log_activity($q['id'], false, __FUNCTION__, [
                                    'desc'=>'wrong pass'
                                ]);
                            }
                        } else {
                            self::log_activity($q['id'], true, __FUNCTION__, [
                                'desc'=>'inactive user tries to log_in'
                            ]);
                            $ret['message']='User is inactive';
                        }
                    }
                    return $ret;
                } catch(\PDOException $e) {
                    self::log_activity(null, false, __FUNCTION__, [
                        'err'=>$e->__toString()
                    ]);
                    $ret['message']='Unknown error';
                    return $ret;
                }
            }
            $ret['message']='Password did not pass validation';
            return $ret;
        }

        public function get_user_info($u_id, ?\Users\Admin $ad=null) :array {
            $ret=['error'=>true, 'message'=>'User does not exist'];
            $q=$this->db->prepare('
                SELECT
                    u.nick
                    ,u.name
                    ,u.surname
                    ,u.academic_titles
                FROM usr u
                WHERE u.id=:id
            ');
            try {
                $q->execute([':id'=>$u_id]);
                if(\count($q=$q->fetchAll(\PDO::FETCH_ASSOC))>0) {
                    return ['error'=>false, 'data'=>$q[0]];
                }
                return $ret;
            } catch(\PDOException $e) {
                self::log_activity(
                    $ad,
                    false,
                    __FUNCTION__,
                    ['err'=>$e->__toString(), 'user_id'=>$u_id]
                );
                $ret['message']='Unknown error';
                return $ret;
            }
        }

        public function get_articles(\Users\UsrBase $u, array $types=[]) :array {
            $ret=[];
            if($u instanceof \Users\Admin)
                $q='
                    SELECT
                        aa.id
                        ,aa.title
                        ,aa.author
                        ,aa.is_archived
                    FROM article_with_author_and_degree aa
                ';
            elseif($u instanceof \Users\User)
                $q='
                    SELECT
                        aa.id
                        ,aa.title
                        ,aa.author
                        ,aa.is_archived
                    FROM article_with_author_and_degree aa
                    WHERE
                        u.id=:uid
                        AND (
                            NOT aa.is_archived
                            OR aa.author_id=:uid
                        )
                ';
            else
                $q='
                    SELECT
                        aa.id
                        ,aa.title
                        ,aa.author
                        ,aa.is_archived
                    FROM article_with_author_and_degree aa
                    WHERE
                        aa.is_published
                ';
            $q=$this->db->prepare($q);
            try {
                if($u instanceof \Users\User && !($u instanceof \Users\Admin))
                    $q->execute([':uid'=>$u->get_id()]);
                else
                    $q->execute();
                self::log_activity(
                    ($u instanceof \Users\User) ? $u:null,
                    true,
                    __FUNCTION__,
                    ['desc'=>'articles fetched']
                );
                if(\count($q=$q->fetchAll(\PDO::FETCH_ASSOC))>0) {
                    if(\count($types)>0) {
                        $q=\array_filter($q, function($v) use ($types) {
                            foreach($types as $val)
                                if($val!==$v['is_archived'])
                                    return false;
                            return true;
                        });
                    }
                    return $q;
                }
                return $ret;
            } catch(\PDOException $e) {
                self::log_activity(
                    ($u instanceof \Users\User) ? $u:null,
                    false,
                    __FUNCTION__,
                    ['err'=>$e->__toString()]
                );
                return ['error'=>true, 'message'=>'Error: Unknown error'];
            }
        }

        public function get_users(\Users\Admin $u) :array {
            $ret=[];
            $q=$this->db->prepare('
                SELECT
                    u.id
                    ,u.nick
                    ,u.author
                    ,u.is_active
                    ,u.is_admin
                FROM author u
                WHERE
                    u.id!=:uid
            ');
            try {
                $q->execute([':uid'=>$u->get_id()]);
                self::log_activity(
                    $u,
                    true,
                    __FUNCTION__,
                    ['desc'=>'users fetched']
                );
                if(\count($q=$q->fetchAll(\PDO::FETCH_ASSOC))>0)
                    return $q;
                return $ret;
            } catch(\PDOException $e) {
                self::log_activity(
                    $u,
                    false,
                    __FUNCTION__,
                    ['err'=>$e->__toString()]
                );
                return ['error'=>true, 'message'=>'Error: Unknown error'];
            }
        }

        public function view_user(\Users\Admin $u, int $id) :array {
            $ret=['error'=>true, 'message'=>'User does not exist or user does not have access'];
            $q=$this->db->prepare('
                SELECT
                    u.*
                FROM author u
                WHERE
                    u.id=:id
                    AND u.id!=:uid
            ');
            try {
                $q->execute([
                    ':id'=>$id,
                    ':uid'=>$u->get_id(),
                ]);
                if(\count($q=$q->fetchAll(\PDO::FETCH_ASSOC))>0)
                    return ['error'=>false, 'data'=>$q[0]];
                return $ret;
            } catch(\PDOException $e) {
                self::log_activity(
                    $u,
                    false,
                    __FUNCTION__,
                    ['err'=>$e->__toString(), 'article_id'=>$id]
                );
                $ret['message']='Unknown error';
                return $ret;
            }
        }

        public function view_article(\Users\UsrBase $u, int $id) :array {
            $ret=['error'=>true, 'message'=>'Article does not exist or user does not have access'];
            if($u instanceof \Users\Admin)
                $q='
                    SELECT
                        aa.*
                    FROM article_with_author_and_degree aa
                    WHERE
                        aa.id=:id
                ';
            elseif($u instanceof \Users\User)
                $q='
                    SELECT
                        aa.*
                    FROM article_with_author_and_degree aa
                    WHERE
                        aa.id=:id
                        AND (
                            NOT aa.is_archived
                            OR aa.author_id=:uid
                        )
                ';
            else
                $q='
                    SELECT
                        aa.*
                    FROM article_with_author_and_degree aa
                    WHERE
                        aa.id=:id
                        AND aa.is_published
                ';
            $q=$this->db->prepare($q);
            try {
                if($u instanceof \Users\User && !($u instanceof \Users\Admin))
                    $q->execute([
                        ':id'=>$id,
                        ':uid'=>$u->get_id(),
                    ]);
                else
                    $q->execute([':id'=>$id]);
                if(\count($q=$q->fetchAll(\PDO::FETCH_ASSOC))>0)
                    return ['error'=>false, 'data'=>$q[0]];
                return $ret;
            } catch(\PDOException $e) {
                self::log_activity(
                    ($u instanceof \Users\User) ? $u:null,
                    false,
                    __FUNCTION__,
                    ['err'=>$e->__toString(), 'article_id'=>$id]
                );
                $ret['message']='Unknown error';
                return $ret;
            }
        }

        public function publish_article(\Users\User $u, int $article_id) :array {
            $ret=['error'=>true, 'message'=>'Article does not exist, is archived, has bad opinions or user does not have access to action on it'];
            $q=$this->db->prepare('
                UPDATE article SET
                    publish_date=CASE
                        WHEN publish_date IS NULL
                            THEN :p_date
                        ELSE
                            publish_date
                    END
                WHERE
                    id=:id
                    AND author_id=:uid
                    AND is_archived=false
                    AND (
                        SELECT
                            COALESCE(AVG(r.degree),0)>3
                        FROM review r
                        WHERE
                            r.article_id=article.id
                    )
                RETURNING id
            ');
            try {
                $q->execute([
                    ':p_date'=>self::get_timestamp(),
                    ':id'=>$article_id,
                    ':uid'=>$u->get_id(),
                ]);
                if(\count($q=$q->fetchAll(\PDO::FETCH_ASSOC))>0) {
                    self::log_activity(
                        $u,
                        true,
                        __FUNCTION__,
                        ['desc'=>"article (id: $article_id) published"]
                    );
                    return ['error'=>false];
                }
                self::log_activity(
                    $u,
                    false,
                    __FUNCTION__,
                    ['desc'=>"article (id: $article_id) not published"]
                );
                return $ret;
            } catch(\PDOException $e) {
                self::log_activity(
                    $u,
                    false,
                    __FUNCTION__,
                    ['err'=>$e->__toString(), 'article_id'=>$article_id]
                );
                $ret['message']='Unknown error';
                return $ret;
            }
        }

        public function unpublish_article(\Users\User $u, int $article_id) :array {
            $ret=['error'=>true, 'message'=>'Article does not exist or user does not have access to action on it'];
            if($u instanceof \Users\Admin)
                $q='
                    UPDATE article SET
                        publish_date=CASE
                            WHEN is_archived=true
                                THEN publish_date
                            ELSE
                                NULL
                        END
                    WHERE
                        id=:id
                    RETURNING id
                ';
            else
                $q='
                    UPDATE article SET
                        publish_date=CASE
                            WHEN is_archived=true
                                THEN publish_date
                            ELSE
                                NULL
                        END
                    WHERE
                        id=:id
                        AND author_id=:uid
                    RETURNING id
                ';
            $q=$this->db->prepare($q);
            try {
                if($u instanceof \Users\Admin)
                    $q->execute([
                        ':id'=>$article_id,
                    ]);
                else
                    $q->execute([
                        ':id'=>$article_id,
                        ':uid'=>$u->get_id(),
                    ]);
                if(\count($q=$q->fetchAll(\PDO::FETCH_ASSOC))>0) {
                    self::log_activity(
                        $u,
                        true,
                        __FUNCTION__,
                        ['desc'=>"article (id: $article_id) unpublished"]
                    );
                    return ['error'=>false];
                }
                self::log_activity(
                    $u,
                    false,
                    __FUNCTION__,
                    ['desc'=>"article (id: $article_id) not unpublished"]
                );
                return $ret;
            } catch(\PDOException $e) {
                self::log_activity(
                    $u,
                    false,
                    __FUNCTION__,
                    ['err'=>$e->__toString(), 'article_id'=>$article_id]
                );
                $ret['message']='Unknown error';
                return $ret;
            }
        }

        public function archive_article(\Users\User $u, int $article_id) :array {
            $ret=['error'=>true, 'message'=>'Article does not exist or user does not have access to action on it'];
            $q=$this->db->prepare('
                UPDATE article SET
                    publish_date=CASE
                        WHEN (publish_date IS NULL OR is_archived=false)
                            THEN :p_date
                        ELSE
                            publish_date
                    END,
                    is_archived=true
                WHERE
                    id=:id
                    AND author_id=:uid
                RETURNING id
            ');
            try {
                $q->execute([
                    ':p_date'=>self::get_timestamp(),
                    ':id'=>$article_id,
                    ':uid'=>$u->get_id(),
                ]);
                if(\count($q=$q->fetchAll(\PDO::FETCH_ASSOC))>0) {
                    self::log_activity(
                        $u,
                        true,
                        __FUNCTION__,
                        ['desc'=>"article (id: $article_id) archived"]
                    );
                    return ['error'=>false];
                }
                self::log_activity(
                    $u,
                    false,
                    __FUNCTION__,
                    ['desc'=>"article (id: $article_id) not archived"]
                );
                return $ret;
            } catch(\PDOException $e) {
                self::log_activity(
                    $u,
                    false,
                    __FUNCTION__,
                    ['err'=>$e->__toString(), 'article_id'=>$article_id]
                );
                $ret['message']='Unknown error';
                return $ret;
            }
        }

        public function dearchive_article(\Users\User $u, int $article_id) :array {
            $ret=['error'=>true, 'message'=>'Article does not exist or user does not have access to action on it'];
            $q=$this->db->prepare('
                UPDATE article SET
                    publish_date=CASE
                        WHEN (publish_date IS NULL OR is_archived=false)
                            THEN publish_date
                        ELSE
                            NULL
                    END,
                    is_archived=false
                WHERE
                    id=:id
                    AND author_id=:uid
                RETURNING id
            ');
            try {
                $q->execute([
                    ':id'=>$article_id,
                    ':uid'=>$u->get_id(),
                ]);
                if(\count($q=$q->fetchAll(\PDO::FETCH_ASSOC))>0) {
                    self::log_activity(
                        $u,
                        true,
                        __FUNCTION__,
                        ['desc'=>"article (id: $article_id) dearchived"]
                    );
                    return ['error'=>false];
                }
                self::log_activity(
                    $u,
                    false,
                    __FUNCTION__,
                    ['desc'=>"article (id: $article_id) not dearchived"]
                );
                return $ret;
            } catch(\PDOException $e) {
                self::log_activity(
                    $u,
                    false,
                    __FUNCTION__,
                    ['err'=>$e->__toString(), 'article_id'=>$article_id]
                );
                $ret['message']='Unknown error';
                return $ret;
            }
        }

        public function activate_user(\Users\Admin $u, int $user_id) :array {
            $ret=['error'=>true, 'message'=>'User does not exist or user does not have access to action on it'];
            $q=$this->db->prepare('
                UPDATE usr SET
                    is_active=true
                WHERE
                    id=:id
                    AND id!=:uid
                RETURNING id
            ');
            try {
                $q->execute([
                    ':id'=>$user_id,
                    ':uid'=>$u->get_id(),
                ]);
                if(\count($q=$q->fetchAll(\PDO::FETCH_ASSOC))>0) {
                    self::log_activity(
                        $u,
                        true,
                        __FUNCTION__,
                        ['desc'=>"user (id: $user_id) activated"]
                    );
                    return ['error'=>false];
                }
                self::log_activity(
                    $u,
                    false,
                    __FUNCTION__,
                    ['desc'=>"user (id: $user_id) not activated"]
                );
                return $ret;
            } catch(\PDOException $e) {
                self::log_activity(
                    $u,
                    false,
                    __FUNCTION__,
                    ['err'=>$e->__toString(), 'user_id'=>$user_id]
                );
                $ret['message']='Unknown error';
                return $ret;
            }
        }

        public function deactivate_user(\Users\Admin $u, int $user_id) :array {
            $ret=['error'=>true, 'message'=>'User does not exist or user does not have access to action on it'];
            $q=$this->db->prepare('
                UPDATE usr SET
                    is_active=false
                WHERE
                    id=:id
                    AND id!=:uid
                    AND is_admin=false
                RETURNING id
            ');
            try {
                $q->execute([
                    ':id'=>$user_id,
                    ':uid'=>$u->get_id(),
                ]);
                if(\count($q=$q->fetchAll(\PDO::FETCH_ASSOC))>0) {
                    self::log_activity(
                        $u,
                        true,
                        __FUNCTION__,
                        ['desc'=>"user (id: $user_id) deactivated"]
                    );
                    return ['error'=>false];
                }
                self::log_activity(
                    $u,
                    false,
                    __FUNCTION__,
                    ['desc'=>"user (id: $user_id) not deactivated"]
                );
                return $ret;
            } catch(\PDOException $e) {
                self::log_activity(
                    $u,
                    false,
                    __FUNCTION__,
                    ['err'=>$e->__toString(), 'user_id'=>$user_id]
                );
                $ret['message']='Unknown error';
                return $ret;
            }
        }

        public function elevate_user(\Users\Admin $u, int $user_id) :array {
            $ret=['error'=>true, 'message'=>'User does not exist or user does not have access to action on it'];
            $q=$this->db->prepare('
                UPDATE usr SET
                    is_admin=true
                WHERE
                    id=:id
                    AND id!=:uid
                    AND is_active=true
                RETURNING id
            ');
            try {
                $q->execute([
                    ':id'=>$user_id,
                    ':uid'=>$u->get_id(),
                ]);
                if(\count($q=$q->fetchAll(\PDO::FETCH_ASSOC))>0) {
                    self::log_activity(
                        $u,
                        true,
                        __FUNCTION__,
                        ['desc'=>"user (id: $user_id) elevated"]
                    );
                    return ['error'=>false];
                }
                self::log_activity(
                    $u,
                    false,
                    __FUNCTION__,
                    ['desc'=>"user (id: $user_id) not elevated"]
                );
                return $ret;
            } catch(\PDOException $e) {
                self::log_activity(
                    $u,
                    false,
                    __FUNCTION__,
                    ['err'=>$e->__toString(), 'user_id'=>$user_id]
                );
                $ret['message']='Unknown error';
                return $ret;
            }
        }

        public function deelevate_user(\Users\Admin $u, int $user_id) :array {
            $ret=['error'=>true, 'message'=>'User does not exist or user does not have access to action on it'];
            $q=$this->db->prepare('
                UPDATE usr SET
                    is_admin=false
                WHERE
                    id=:id
                    AND id!=:uid
                RETURNING id
            ');
            try {
                $q->execute([
                    ':id'=>$user_id,
                    ':uid'=>$u->get_id(),
                ]);
                if(\count($q=$q->fetchAll(\PDO::FETCH_ASSOC))>0) {
                    self::log_activity(
                        $u,
                        true,
                        __FUNCTION__,
                        ['desc'=>"user (id: $user_id) deelevate"]
                    );
                    return ['error'=>false];
                }
                self::log_activity(
                    $u,
                    false,
                    __FUNCTION__,
                    ['desc'=>"user (id: $user_id) not deelevate"]
                );
                return $ret;
            } catch(\PDOException $e) {
                self::log_activity(
                    $u,
                    false,
                    __FUNCTION__,
                    ['err'=>$e->__toString(), 'user_id'=>$user_id]
                );
                $ret['message']='Unknown error';
                return $ret;
            }
        }

        public function add_review(\Users\User $u, int $article_id, int $degree, string $text) :array {
            $ret=['error'=>true, 'message'=>'Review creation was not successful.'];
            $q=$this->db->prepare('
                INSERT INTO review(author_id, article_id, degree, "text", creation_date) VALUES
                (:id, :a_id, :mark, :txt, :cr_date)
                RETURNING id
            ');
            try {
                $q->execute([
                    ':id'=>$u->get_id(),
                    ':a_id'=>$article_id,
                    ':mark'=>$degree,
                    ':txt'=>$text,
                    ':cr_date'=>self::get_timestamp(),
                ]);
                if(\count($q=$q->fetchAll(\PDO::FETCH_ASSOC))>0) {
                    $q=$q[0];
                    self::log_activity($u, true, __FUNCTION__, [
                        'desc'=>"review (id: {$q['id']})"
                    ]);
                    return ['error'=>false, 'id'=>$q['id']];
                }
                self::log_activity($u, false, __FUNCTION__, [
                    'desc'=>'review not added - unknown, no error'
                ]);
            } catch(\PDOException $e) {
                if(\strpos($e->getMessage(), 'duplicate key value')===false) {
                    self::log_activity($u, false, __FUNCTION__, [
                        'desc'=>'review not added - error',
                        'err'=>$e->__toString()
                    ]);
                    $ret['message']='Unknown error';
                } else {
                    self::log_activity($u, false, __FUNCTION__, [
                        'desc'=>'review not added - there is already a review from this usr',
                        'article_id'=>$article_id,
                        'err'=>$e->__toString()
                    ]);
                    $ret['message']='There is already a review from you about this article.';
                }
            }
            return $ret;
        }

        public function view_review(\Users\User $u, int $id) :array {
            $ret=['error'=>true, 'message'=>'Review does not exist or user does not have access'];
            $q=$this->db->prepare('
                SELECT
                    ra.*
                FROM review_with_author_and_article_title ra
                WHERE
                    ra.id=:id
                    AND (
                        ra.article_author_id=:uid
                        OR NOT ra.is_article_archived
                    )
            ');
            try {
                $q->execute([
                    ':id'=>$id,
                    ':uid'=>$u->get_id(),
                ]);
                if(\count($q=$q->fetchAll(\PDO::FETCH_ASSOC))>0)
                    return ['error'=>false, 'data'=>$q[0]];
                return $ret;
            } catch(\PDOException $e) {
                self::log_activity(
                    $u,
                    false,
                    __FUNCTION__,
                    ['err'=>$e->__toString(), 'review_id'=>$id]
                );
                $ret['message']='Unknown error';
                return $ret;
            }
        }

        public function edit_review(\Users\User $u, int $id, int $degree, string $text) :array {
            $ret=['error'=>true, 'message'=>'Review does not exist or user does not have access'];
            $q=$this->db->prepare('
                UPDATE review SET
                    degree=:degree
                    ,"text"=:txt
                    ,last_edit_date=:ed_date
                WHERE
                    id=:id
                    AND author_id=:uid
                RETURNING id
            ');
            try {
                $q->execute([
                    ':id'=>$id,
                    ':uid'=>$u->get_id(),
                    ':degree'=>$degree,
                    ':txt'=>$text,
                    ':ed_date'=>self::get_timestamp(),
                ]);
                if(\count($q=$q->fetchAll(\PDO::FETCH_ASSOC))>0) {
                    self::log_activity(
                        $u,
                        true,
                        __FUNCTION__,
                        ['desc'=>"review (id: ${$id}) edited"]
                    );
                    return ['error'=>false];
                }
                return $ret;
            } catch(\PDOException $e) {
                self::log_activity(
                    $u,
                    false,
                    __FUNCTION__,
                    ['err'=>$e->__toString(), 'review_id'=>$id]
                );
                $ret['message']='Unknown error';
                return $ret;
            }
        }

        public function get_reviews(\Users\User $u, int $id) :array {
            $ret=[];
            $q=$this->db->prepare('
                SELECT
                    ra.id
                    ,ra.author
                    ,ra.degree
                FROM review_with_author_and_article_title ra
                WHERE
                    ra.article_id=:id
                    AND (
                        ra.article_author_id=:uid
                        OR NOT ra.is_article_archived
                    )
            ');
            try {
                $q->execute([
                    ':id'=>$id,
                    ':uid'=>$u->get_id(),
                ]);
                self::log_activity(
                    $u,
                    true,
                    __FUNCTION__,
                    ['desc'=>'reviews fetched']
                );
                if(\count($q=$q->fetchAll(\PDO::FETCH_ASSOC))>0)
                    return $q;
                return $ret;
            } catch(\PDOException $e) {
                self::log_activity(
                    $u,
                    false,
                    __FUNCTION__,
                    ['err'=>$e->__toString(), 'article_id'=>$id]
                );
                return ['error'=>true, 'message'=>'Error: Unknown error'];
            }
        }

        public function delete_review(\Users\User $u, int $id) :array {
            $ret=['error'=>true, 'message'=>'Review does not exist or user does not have access'];
            $q=$this->db->prepare('
                DELETE FROM review r
                WHERE
                    r.id=:id
                    AND r.author_id=:uid
                RETURNING id
            ');
            try {
                $q->execute([
                    ':id'=>$id,
                    ':uid'=>$u->get_id(),
                ]);
                if(\count($q=$q->fetchAll(\PDO::FETCH_ASSOC))>0) {
                    self::log_activity(
                        $u,
                        true,
                        __FUNCTION__,
                        ['desc'=>"review (id: {$id}) deleted"]
                    );
                    return ['error'=>false];
                }
                return $ret;
            } catch(\PDOException $e) {
                self::log_activity(
                    $u,
                    false,
                    __FUNCTION__,
                    ['err'=>$e->__toString(), 'article_id'=>$id]
                );
                return ['error'=>true, 'message'=>'Error: Unknown error'];
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
                    ':log'=>\json_encode($log)
                ]);
            } catch(\PDOException $e) {
                echo '<h1>Operation failed, please inform an administrator.</h1>';
            }
        }

        private function get_timestamp() :string {
            return \date(self::$DATETIME_FORMAT);
        }
    }
