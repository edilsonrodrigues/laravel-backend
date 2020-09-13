<?php

namespace App\Core\Library;

use Carbon\Carbon;

/**
 * Class Jwt
 * @package App\Core\Library
 * @autor Edilson Rodirgues
 */
class JWT
{

    /**
     *
     */
    public static function encode($params)
    {
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];

        $key = env('APP_KEY');

        $dateNow = Carbon::now(new \DateTimeZone('America/Sao_Paulo'));
        $expires = $dateNow->addDays(1);
        $payload = [
            'exp' => $expires->getTimestamp(),
            'sub' => $params['id'],
            'iss' => $_SERVER['HTTP_HOST']
        ];

        $header = base64_encode(json_encode($header));
        $payload = base64_encode(json_encode($payload));

        $sign = base64_encode(JWT::sign("{$header}.{$payload}",$key));

        return "{$header}.{$payload}.{$sign}";
    }

    private static function sign($msg, $key, $method = 'sha256')
    {
        return hash_hmac($method, $msg, $key, true);
    }

    public static function decode($jwt, $key = null, $verify = true)
    {
        $tks = explode('.', $jwt);
        if (count($tks) != 3) {
            throw new \Exception('Wrong number of segments');
        }
        list($headb64, $bodyb64, $cryptob64) = $tks;
        if (null === ($header = json_decode(JWT::urlsafeB64Decode($headb64)))) {
            throw new \Exception('Invalid segment encoding');
        }
        if (null === $payload = json_decode(JWT::urlsafeB64Decode($bodyb64))) {
            throw new \Exception('Invalid segment encoding');
        }
        $sig = JWT::urlsafeB64Decode($cryptob64);
        if ($verify) {
            if (empty($header->alg)) {
                throw new \Exception('Empty algorithm');
            }
            if ($sig != JWT::sign("$headb64.$bodyb64", $key)) {//, $header->alg
               // throw new Exception('Signature verification failed');
            }
        }
        return $payload;
    }

    /**
     * Decode a string with URL-safe Base64.
     *
     * @param string $input A Base64 encoded string
     *
     * @return string A decoded string
     */
    public static function urlsafeB64Decode($input)
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }

}