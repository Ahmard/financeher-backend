<?php

namespace App\Helpers;

use Exception;
use JetBrains\PhpStorm\ArrayShape;

class CryptoHelper
{
    /**
     * Generates a pair of private and public keys
     *
     * @param int $privateKeyBits Length of the private key in bits (default 2048)
     * @return array Contains private key and public key in PEM format
     * @throws Exception If key generation fails
     */
    #[ArrayShape(['private_key' => "string", 'public_key' => "mixed"])]
    public static function generateKeys(int $privateKeyBits = 4096): array
    {
        // Configuration for the RSA key pair
        $config = [
            "private_key_bits" => $privateKeyBits,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ];

        // Generate the private and public keys
        $res = openssl_pkey_new($config);

        // Check if key generation was successful
        if ($res === false) {
            throw new Exception('Key generation failed');
        }

        // Export the private key to a variable
        openssl_pkey_export($res, $privateKey);

        // Get the public key from the generated key pair
        $keyDetails = openssl_pkey_get_details($res);
        $publicKey = $keyDetails['key'];

        // Return the private and public keys as an array
        return [
            'private_key' => $privateKey,
            'public_key'  => $publicKey,
        ];
    }
}


