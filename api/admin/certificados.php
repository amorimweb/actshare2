<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAdmin();
$method = $_SERVER['REQUEST_METHOD'];
$db = getDB();

if ($method === 'GET') {
    $stmt = $db->query('SELECT * FROM certificados_manuais ORDER BY created_at DESC');
    jsonOk($stmt->fetchAll());
}

if ($method === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true) ?? [];

    $cliente_nome   = trim($body['cliente_nome'] ?? '');
    $curso_nome     = trim($body['curso_nome'] ?? '');
    $carga_horaria  = intval($body['carga_horaria'] ?? 0);
    $data_conclusao = trim($body['data_conclusao'] ?? '');
    $tipo_texto     = trim($body['tipo_texto'] ?? 'participacao');
    $instrutor_nome = trim($body['instrutor_nome'] ?? '');
    $assinatura_url = trim($body['assinatura_url'] ?? '');

    if (empty($cliente_nome) || empty($curso_nome) || $carga_horaria <= 0 || empty($data_conclusao) || empty($instrutor_nome)) {
        jsonError('Por favor, preencha todos os campos obrigatórios (Nome do aluno, nome do curso, carga horária, data e instrutor).', 400);
    }
    if (!in_array($tipo_texto, ['participacao', 'aprovacao'])) {
        jsonError('Tipo de certificado inválido.', 400);
    }

    // Gera um código de autenticidade único de 12 caracteres (ex: QUA-48D7-1A)
    // Vamos usar o prefixo do nome do curso ou apenas MC (Manual Certificate) + hash
    $sigla = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $curso_nome), 0, 3));
    if (empty($sigla)) $sigla = 'MAN';
    
    $tentativas = 0;
    do {
        $randHex = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
        $codigo_autenticidade = $sigla . '-' . substr($randHex, 0, 4) . '-' . substr($randHex, 4, 4);
        
        $stmtCheck = $db->prepare('SELECT id FROM certificados_manuais WHERE codigo_autenticidade = ? LIMIT 1');
        $stmtCheck->execute([$codigo_autenticidade]);
        $existe = $stmtCheck->fetch();
        $tentativas++;
    } while ($existe && $tentativas < 10);

    $stmt = $db->prepare('
        INSERT INTO certificados_manuais (cliente_nome, curso_nome, carga_horaria, data_conclusao, tipo_texto, instrutor_nome, assinatura_url, codigo_autenticidade)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ');
    $stmt->execute([
        $cliente_nome,
        $curso_nome,
        $carga_horaria,
        $data_conclusao,
        $tipo_texto,
        $instrutor_nome,
        $assinatura_url,
        $codigo_autenticidade
    ]);

    jsonOk([
        'success' => true,
        'id' => $db->lastInsertId(),
        'codigo_autenticidade' => $codigo_autenticidade
    ], 201);
}

methodNotAllowed();
