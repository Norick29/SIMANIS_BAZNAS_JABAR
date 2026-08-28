<?php

namespace App\Services;

class RSAService
{
    /**
     * Generate RSA Key Pair
     */
    public function generateKeyPair()
    {
        $config = [
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
            "digest_alg"       => "sha256",
        ];

        $localOpenSslConfig = "C:/xampp/php/extras/ssl/openssl.cnf";
        if (file_exists($localOpenSslConfig)) {
            $config["config"] = $localOpenSslConfig;
        }

        // Generate key pair
        $res = openssl_pkey_new($config);

        if ($res === false) {
            $error = openssl_error_string();

            throw new \Exception(
                'Gagal generate RSA key: ' .
                ($error ?: 'Unknown error')
            );
        }

        // Export private key
        $privateKey = null;

        $success = openssl_pkey_export(
            $res,
            $privateKey
        );

        if (!$success || empty($privateKey)) {

            $error = openssl_error_string();

            throw new \Exception(
                'Gagal export private key: ' .
                ($error ?: 'Unknown error')
            );
        }

        // Get public key
        $publicKeyDetails = openssl_pkey_get_details($res);

        if (
            $publicKeyDetails === false ||
            !isset($publicKeyDetails['key'])
        ) {

            $error = openssl_error_string();

            throw new \Exception(
                'Gagal extract public key: ' .
                ($error ?: 'Unknown error')
            );
        }

        $publicKey = $publicKeyDetails['key'];

        return [
            'public_key'  => $publicKey,
            'private_key' => encrypt($privateKey),
        ];
    }

    /**
     * Sign Data
     */
    public function signData($data, $encryptedPrivateKey)
    {
        // Decrypt private key
        $privateKey = decrypt($encryptedPrivateKey);

        $signature = null;

        $success = openssl_sign(
            $data,
            $signature,
            $privateKey,
            OPENSSL_ALGO_SHA256
        );

        if (!$success) {

            $error = openssl_error_string();

            throw new \Exception(
                'Gagal sign data: ' .
                ($error ?: 'Unknown error')
            );
        }

        return base64_encode($signature);
    }

    /**
     * Verify Signature
     */
    public function verifySignature(
        $data,
        $signatureBase64,
        $publicKey
    ) {

        $signature = base64_decode($signatureBase64);

        $result = openssl_verify(
            $data,
            $signature,
            $publicKey,
            OPENSSL_ALGO_SHA256
        );

        return $result === 1;
    }
}
