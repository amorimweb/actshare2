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

function getGestorContext(array $user, PDO $db): array {
    $userId = $user['id'];
    
    // 1. Check if they are the main gestor (owner of an organization)
    $stmt = $db->prepare('SELECT id, gestor_id, certificado_acesso FROM organizacoes WHERE gestor_id = ? AND ativo = 1 LIMIT 1');
    $stmt->execute([$userId]);
    $org = $stmt->fetch();
    
    if ($org) {
        return [
            'id' => $userId,
            'org_id' => $org['id'],
            'certificado_acesso' => $org['certificado_acesso'],
            'is_subgestor' => false
        ];
    }
    
    // 2. Check if they are a sub-gestor (member of an active organization)
    $stmt = $db->prepare('
        SELECT o.id, o.gestor_id, o.certificado_acesso 
        FROM membros_organizacao mo
        JOIN organizacoes o ON mo.organizacao_id = o.id
        WHERE mo.usuario_id = ? AND o.ativo = 1
        LIMIT 1
    ');
    $stmt->execute([$userId]);
    $org = $stmt->fetch();
    
    if ($org) {
        return [
            'id' => (int)$org['gestor_id'],
            'org_id' => (int)$org['id'],
            'certificado_acesso' => $org['certificado_acesso'],
            'is_subgestor' => true
        ];
    }
    
    // Fallback if not linked to any active organization
    return [
        'id' => $userId,
        'org_id' => null,
        'certificado_acesso' => null,
        'is_subgestor' => false
    ];
}
