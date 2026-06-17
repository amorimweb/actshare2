<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') methodNotAllowed();

$gestor = requireMasterOrAdmin();
$db = getDB();

$body = json_decode(file_get_contents('php://input'), true) ?? [];
$acesso = trim($body['certificado_acesso'] ?? '');

if (!in_array($acesso, ['empresa', 'aluno', 'ambos'])) {
    jsonError('Acesso inválido. Deve ser: empresa, aluno ou ambos.', 400);
}

// Obtém ou cria a organização do gestor
$stmt = $db->prepare('SELECT id FROM organizacoes WHERE gestor_id = ? AND ativo = 1 LIMIT 1');
$stmt->execute([$gestor['id']]);
$org = $stmt->fetch();

if (!$org) {
    $stmt = $db->prepare('INSERT INTO organizacoes (gestor_id, ativo, certificado_acesso) VALUES (?, 1, ?)');
    $stmt->execute([$gestor['id'], $acesso]);
} else {
    $stmt = $db->prepare('UPDATE organizacoes SET certificado_acesso = ? WHERE id = ?');
    $stmt->execute([$acesso, $org['id']]);
}

jsonOk(['success' => true, 'message' => 'Preferência de certificado salva com sucesso.']);
