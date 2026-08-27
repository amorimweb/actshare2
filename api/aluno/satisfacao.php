<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/matriculas.php';

$user = requireAuth();
$method = $_SERVER['REQUEST_METHOD'];
$db = getDB();

if ($method === 'GET') {
    $matriculaId = (int)($GLOBALS['_ROUTE']['matricula_id'] ?? 0);
    if (!$matriculaId) jsonError('ID da matrícula inválido.', 400);

    // Verifica se a matrícula pertence ao aluno
    $stmt = $db->prepare('SELECT id, curso_id FROM matriculas WHERE id = ? AND aluno_id = ? LIMIT 1');
    $stmt->execute([$matriculaId, $user['id']]);
    $matricula = $stmt->fetch();
    if (!$matricula) jsonError('Matrícula não encontrada.', 403);

    // Verifica se já respondeu
    $stmt = $db->prepare('
        SELECT COUNT(*) AS total
        FROM pesquisa_respostas pr
        JOIN pesquisa_perguntas pp ON pr.pergunta_id = pp.id
        WHERE pr.matricula_id = ?
    ');
    $stmt->execute([$matriculaId]);
    $res = $stmt->fetch();
    
    // Total de perguntas na pesquisa
    $stmtPerg = $db->query('SELECT COUNT(*) AS total FROM pesquisa_perguntas');
    $totalPerg = $stmtPerg->fetch()['total'];

    if ($res && (int)$res['total'] >= (int)$totalPerg && (int)$totalPerg > 0) {
        jsonOk(['respondida' => true]);
    }

    // Carrega as perguntas para responder
    $stmtPerguntas = $db->query('SELECT id, texto FROM pesquisa_perguntas ORDER BY id');
    $perguntas = $stmtPerguntas->fetchAll();

    jsonOk([
        'respondida' => false,
        'perguntas'  => $perguntas
    ]);
}

if ($method === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true) ?? [];
    
    $matriculaId = (int)($body['matricula_id'] ?? 0);
    $respostas   = $body['respostas'] ?? []; // [pergunta_id => nota]

    if (!$matriculaId) {
        jsonError('ID da matrícula é obrigatório.', 400);
    }

    // Verifica se a matrícula pertence ao aluno
    $stmt = $db->prepare('SELECT id, curso_id FROM matriculas WHERE id = ? AND aluno_id = ? LIMIT 1');
    $stmt->execute([$matriculaId, $user['id']]);
    $matricula = $stmt->fetch();
    if (!$matricula) jsonError('Matrícula não encontrada.', 403);

    // Valida que todas as perguntas foram respondidas
    $stmtPerguntas = $db->query('SELECT id FROM pesquisa_perguntas');
    $perguntas = $stmtPerguntas->fetchAll();

    foreach ($perguntas as $p) {
        $nota = isset($respostas[$p['id']]) ? (int)$respostas[$p['id']] : 0;
        if ($nota < 1 || $nota > 5) {
            jsonError('Por favor, responda a todas as questões com notas de 1 a 5.', 400);
        }
    }

    $db->beginTransaction();
    try {
        $stmtIns = $db->prepare('
            INSERT INTO pesquisa_respostas (matricula_id, pergunta_id, nota)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE nota = VALUES(nota)
        ');
        foreach ($perguntas as $p) {
            $stmtIns->execute([$matriculaId, $p['id'], (int)$respostas[$p['id']]]);
        }

        // Recalcula a conclusão: agora que a pesquisa foi respondida, se as
        // aulas já estavam 100%, é aqui que concluido vira 1 de fato.
        $status = recalcularConclusaoMatricula($db, $matriculaId);

        $db->commit();
        jsonOk(['success' => true] + $status);
    } catch (Exception $e) {
        $db->rollBack();
        jsonError($e->getMessage(), 500);
    }
}

methodNotAllowed();
