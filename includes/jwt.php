<?php
require_once __DIR__ . '/../config.php';

class JWT {
    private static function b64ue(string $data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function b64ud(string $data): string {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    public static function encode(array $payload): string {
        $header  = self::b64ue(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        $payload = self::b64ue(json_encode($payload));
        $sig     = self::b64ue(hash_hmac('sha256', "$header.$payload", JWT_SECRET, true));
        return "$header.$payload.$sig";
    }

    public static function decode(string $token): ?array {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return null;
        [$header, $payload, $sig] = $parts;

        $expected = self::b64ue(hash_hmac('sha256', "$header.$payload", JWT_SECRET, true));
        if (!hash_equals($expected, $sig)) return null;

        $data = json_decode(self::b64ud($payload), true);
        if (!$data || (isset($data['exp']) && $data['exp'] < time())) return null;

        return $data;
    }
}

function signToken(array $user): string {
    return JWT::encode([
        'id'    => $user['id'],
        'email' => $user['email'],
        'role'  => $user['role'],
        'nome'  => $user['nome'],
        'iat'   => time(),
        'exp'   => time() + JWT_TTL,
    ]);
}

function verifyToken(string $token): ?array {
    return JWT::decode($token);
}
