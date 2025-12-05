<?php 

namespace App\Services;

class EncryptionService
{
    private static function getKey()
    {
        return $_ENV['ENCRYPTION_KEY'];
    }

    public static function encrypt($data)
    {   
        $key = self::getKey();

        $ivLength = openssl_cipher_iv_length('AES-256-CBC');

        $iv = openssl_random_pseudo_bytes($ivLength);

        $encryptedData = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
        return base64_encode($iv . $encryptedData);
    }

    public static function decrypt($data)
    {   
        $key = self::getKey();

        $data = base64_decode($data);
        $ivLength = openssl_cipher_iv_length('AES-256-CBC');

        $iv = substr($data, 0, $ivLength);
        
        $encryptedData = substr($data, $ivLength);
        return openssl_decrypt($encryptedData, 'AES-256-CBC', $key, 0, $iv);
    }
}