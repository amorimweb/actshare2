<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    requireAdmin();
    $stmt = getDB()->query('
        SELECT id, nome, email, role, ativo, created_at, documento, telefone, tipo_pessoa, data_nascimento,
               razao_social, inscricao_estadual, cep, endereco, numero, complemento, bairro, cidade, estado, pais
        FROM usuarios ORDER BY created_at DESC
    ');
    jsonOk($stmt->fetchAll());
}

if ($method === 'PUT') {
    // Ficha completa do cliente (dados fiscais/contato) — a mesma coisa que
    // o aluno edita em "Meus Dados", só que agora acessível pelo admin.
    requireAdmin();
    $body   = json_decode(file_get_contents('php://input'), true) ?? [];
    $userId = (int)($body['id'] ?? 0);
    if (!$userId) jsonError('id é obrigatório.', 400);

    $nome  = trim($body['nome']  ?? '');
    $email = trim($body['email'] ?? '');
    if (!$nome || !$email) jsonError('Nome e e-mail são obrigatórios.', 400);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) jsonError('E-mail inválido.', 400);

    $db = getDB();
    $stmt = $db->prepare('SELECT id FROM usuarios WHERE email = ? AND id != ? LIMIT 1');
    $stmt->execute([$email, $userId]);
    if ($stmt->fetch()) jsonError('Este e-mail já está sendo utilizado por outro usuário.', 400);

    $camposOpcionais = [
        'documento', 'telefone', 'tipo_pessoa', 'data_nascimento', 'razao_social',
        'inscricao_estadual', 'cep', 'endereco', 'numero', 'complemento', 'bairro', 'cidade', 'estado',
    ];
    $sets = ['nome = ?', 'email = ?'];
    $params = [$nome, $email];
    foreach ($camposOpcionais as $campo) {
        if (array_key_exists($campo, $body)) {
            $sets[] = "$campo = ?";
            $params[] = trim((string)$body[$campo]) ?: null;
        }
    }
    $params[] = $userId;

    $db->prepare('UPDATE usuarios SET ' . implode(', ', $sets) . ' WHERE id = ?')->execute($params);

    $stmt = $db->prepare('
        SELECT id, nome, email, role, ativo, created_at, documento, telefone, tipo_pessoa, data_nascimento,
               razao_social, inscricao_estadual, cep, endereco, numero, complemento, bairro, cidade, estado, pais
        FROM usuarios WHERE id = ?
    ');
    $stmt->execute([$userId]);
    jsonOk($stmt->fetch());
}

if ($method === 'PATCH') {
    requireAdmin();
    $body   = json_decode(file_get_contents('php://input'), true) ?? [];
    $userId = (int)($body['userId'] ?? 0);
    $role   = $body['role'] ?? '';
    if (!$userId || !$role) jsonError('userId e role são obrigatórios.', 400);

    $allowed = ['admin', 'gestor', 'aluno', 'instrutor'];
    if (!in_array($role, $allowed)) jsonError('Role inválida.', 400);

    $db   = getDB();
    $stmt = $db->prepare('UPDATE usuarios SET role = ? WHERE id = ?');
    $stmt->execute([$role, $userId]);

    $stmt = $db->prepare('SELECT id, nome, email, role, ativo FROM usuarios WHERE id = ?');
    $stmt->execute([$userId]);
    jsonOk($stmt->fetch());
}

methodNotAllowed();
