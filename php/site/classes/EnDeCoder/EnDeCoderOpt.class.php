<?php
    namespace EnDeCoder;
    enum EnDeCoderOpt {
        case PASS_KEY;
        // case NAME_KEY;
        // case SURNAME_KEY;
        public function get_string() :string {
            return match($this) {
                static::PASS_KEY => $_ENV['PHP_PASS_CRYPT_KEY'],
                // static::NAME_KEY => $_ENV['PHP_NAME_CRYPT_KEY'],
                // static::SURNAME_KEY => $_ENV['PHP_SURNAME_CRYPT_KEY'],
            };
        }
    }