<?php 

namespace App\Http;

class JWT 
{
    private static string $secret = "secret-key";

    public static function generate(array $data = [])
    {
        $header  = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode($data);
        
        $base64UrlHeader  = self::base64url_encode($header);
        $base64UrlPayload = self::base64url_encode($payload);

        $signature = self::signature($base64UrlHeader, $base64UrlPayload);

        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $signature;

        return $jwt;
    }

    public static function verify(string $jwt)
    {
        $tokenPartials = explode('.', $jwt);

        if (count($tokenPartials) != 3) return false;

        [$header, $payload, $signature] = $tokenPartials;

        if ($signature !== self::signature($header, $payload)) return false;

        return self::base64url_decode($payload);
    }

    public static function signature(string $header, string $payload)
    {
        $signature = hash_hmac('sha256', $header . "." . $payload, self::$secret, true);

        return self::base64url_encode($signature);
    }

    public static function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function base64url_decode($data)
    {
        $padding = strlen($data) % 4;

        $padding !== 0 && $data .= str_repeat('=', 4 -  $padding);

        $data = strtr($data, '-_', '+/');

        return json_decode(base64_decode($data), true);
    }
}