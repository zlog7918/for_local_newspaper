<?php
    namespace DB;
    enum DB_QueriesOpt :string {
        case DB_Queries='DB_Queries';
        public function get_conn_string() :string {
            return match($this) {
                static::DB_Queries => "pgsql:host=postgres;port={$_ENV['POSTGRES_PORT']};dbname={$_ENV['POSTGRES_DB']}",
            };
        }
        public function get_usr() :string {
            return match($this) {
                static::DB_Queries => $_ENV['POSTGRES_USER'],
            };
        }
        public function get_pass() :string {
            return match($this) {
                static::DB_Queries => $_ENV['POSTGRES_PASSWORD'],
            };
        }
    }