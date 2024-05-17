<?php
    namespace EnDeCoder;
    abstract class EnDeCoder {
        private static int $NONCE_LEN=\SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES;
        private static int $KEY_LEN=\SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_KEYBYTES;
        private static int $SALT_LEN=4;

        public static function getSymmetricKey(EnDeCoderOpt $opt) :string {
            $opt=$opt->get_string();
            $filename="{$_ENV['KEY_DIR']}/$opt";
            if(\file_exists($filename)) {
                $secretKey=\base64_decode(file_get_contents($filename));
                $secretKey=\mb_substr($secretKey, static::$SALT_LEN, null, '8bit');
            }
            else {
                $secretKey=\random_bytes(static::$KEY_LEN);
                \file_put_contents($filename, \base64_encode(\random_bytes(static::$SALT_LEN).$secretKey));
            }
            return $secretKey;
        }

        /**
         * Wrap crypto_aead_*_encrypt() in a drop-dead-simple encryption interface
         *
         * @link https://paragonie.com/b/kIqqEWlp3VUOpRD7
         * @param string $message
         * @param string $key
         * @return string
         */
        public static function simpleEncrypt(string $message, string $key) :string {
            $nonce=\random_bytes(static::$NONCE_LEN);
            $encrypted=\sodium_crypto_aead_xchacha20poly1305_ietf_encrypt(
                $message,
                $nonce,
                $nonce,
                $key
            );
            return \base64_encode($nonce.$encrypted);
        }

        /**
         * Wrap crypto_aead_*_decrypt() in a drop-dead-simple decryption interface
         *
         * @link https://paragonie.com/b/kIqqEWlp3VUOpRD7
         * @param string $message - Encrypted message
         * @param string $key     - Encryption key
         * @return string
         * @throws Exception
         */
        public static function simpleDecrypt(string $message, string $key) :string {
            $message=\base64_decode($message);
            $nonce=\mb_substr($message, 0, static::$NONCE_LEN, '8bit');
            $ciphertext=\mb_substr($message, static::$NONCE_LEN, null, '8bit');
            $plaintext=\sodium_crypto_aead_xchacha20poly1305_ietf_decrypt(
                $ciphertext,
                $nonce,
                $nonce,
                $key
            );
            // if(!\is_string($plaintext)) {
            //     throw new \Exception('Invalid message');
            // }
            return $plaintext;
        }
    }
