<?php
require_once __DIR__ . '/jwt.php';
require_once __DIR__ . '/response.php';

function getAuthUser(): ?array {
    $token = $_COOKIE[COOKIE_NAME] ?? null;
    if (!$token) return null;
    return verifyToken($token);
}

function requireAuth(): array {
    $user = getAuthUser();
    if (!$user) jsonError('Não autenticado.', 401);
    return $user;
}

function requireAdmin(): array {
    $user = requireAuth();
    if ($user['role'] !== 'admin') jsonError('Acesso negado.', 403);
    return $user;
}

function requireMasterOrAdmin(): array {
    $user = requireAuth();
    if (!in_array($user['role'], ['admin', 'gestor'])) jsonError('Acesso negado.', 403);
    return $user;
}

function setAuthCookie(string $token): void {
    $secure   = str_starts_with(SITE_URL, 'https');
    $expires  = time() + JWT_TTL;
    setcookie(COOKIE_NAME, $token, [
        'expires'  => $expires,
        'path'     => '/',
        'secure'   => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function clearAuthCookie(): void {
    setcookie(COOKIE_NAME, '', [
        'expires'  => time() - 3600,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}
