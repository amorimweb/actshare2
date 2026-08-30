<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAdmin();
$id = (int)($GLOBALS['_ROUTE']['id'] ?? 0);
if (!$id) jsonError('ID inválido.', 400);

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $db->prepare('
        SELECT u.*, o.id AS organizacao_id, o.certificado_acesso
        FROM usuarios u
        LEFT JOIN organizacoes o ON o.gestor_id = u.id
        WHERE u.id = ?
    ');
    $stmt->execute([$id]);
    $cliente = $stmt->fetch();
    if (!$cliente) jsonError('Cliente não encontrado.', 404);
    unset($cliente['senha_hash']);
    jsonOk($cliente);
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $body = json_decode(file_get_contents('php://input'), true) ?? [];

    $campos = [
        'nome', 'tipo_pessoa', 'documento', 'razao_social', 'inscricao_estadual',
        'telefone', 'cep', 'endereco', 'numero', 'complemento', 'bairro', 'cidade',
        'estado', 'observacao_admin',
    ];
    $sets = [];
    $params = [];
    foreach ($campos as $campo) {
        if (array_key_exists($campo, $body)) {
            $sets[] = "$campo = ?";
            $params[] = trim($body[$campo]) === '' ? null : trim($body[$campo]);
        }
    }
    if ($sets) {
        $params[] = $id;
        $stmt = $db->prepare('UPDATE usuarios SET ' . implode(', ', $sets) . ' WHERE id = ?');
        $stmt->execute($params);
    }

    if (array_key_exists('certificado_acesso', $body)) {
        $acesso = in_array($body['certificado_acesso'], ['empresa', 'aluno', 'ambos']) ? $body['certificado_acesso'] : 'ambos';
        $stmt = $db->prepare('SELECT id FROM organizacoes WHERE gestor_id = ? LIMIT 1');
        $stmt->execute([$id]);
        $org = $stmt->fetch();
        if ($org) {
            $stmt = $db->prepare('UPDATE organizacoes SET certificado_acesso = ? WHERE id = ?');
            $stmt->execute([$acesso, $org['id']]);
        } else {
            $stmt = $db->prepare('INSERT INTO organizacoes (gestor_id, ativo, certificado_acesso) VALUES (?, 1, ?)');
            $stmt->execute([$id, $acesso]);
        }
    }

    jsonOk(['success' => true]);
}

methodNotAllowed();
