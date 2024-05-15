<?php
    namespace DB;
    class DB_Factory {
        private static array $dbs=[];
        public static function get_db(DB_QueriesOpt $db_type): DB_Queries {
            if(array_key_exists($db_type->value, static::$dbs))
                return static::$dbs[$db_type->value];
            return (static::$dbs[$db_type->value]=new DB_Queries(
                $db_type->get_conn_string(),
                $db_type->get_usr(),
                $db_type->get_pass()
            ));
        }
    }
