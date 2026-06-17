<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

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

        // Marca a matrícula como concluída se todas as aulas foram concluídas
        // Vamos calcular o progresso real e atualizar a matrícula
        $stmtAulas = $db->prepare('
            SELECT COUNT(*) AS total 
            FROM aulas a
            JOIN modulos m ON a.modulo_id = m.id
            WHERE m.curso_id = ?
        ');
        $stmtAulas->execute([$matricula['curso_id']]);
        $totalAulas = (int)$stmtAulas->fetch()['total'];

        $stmtProg = $db->prepare('
            SELECT COUNT(*) AS concluidas 
            FROM progresso_aula 
            WHERE matricula_id = ? AND concluida = 1
        ');
        $stmtProg->execute([$matriculaId]);
        $concluidas = (int)$stmtProg->fetch()['concluidas'];

        $percentual = $totalAulas > 0 ? (int)round(($concluidas / $totalAulas) * 100) : 100;
        if ($percentual > 100) $percentual = 100;

        $concluido = ($percentual >= 100) ? 1 : 0;
        $dataConclusao = $concluido ? date('Y-m-d H:i:s') : null;

        $stmtUpdateMat = $db->prepare('
            UPDATE matriculas
            SET progresso_total = ?, concluido = ?, data_conclusao = COALESCE(data_conclusao, ?)
            WHERE id = ?
        ');
        $stmtUpdateMat->execute([$percentual, $concluido, $dataConclusao, $matriculaId]);

        $db->commit();
        jsonOk(['success' => true]);
    } catch (Exception $e) {
        $db->rollBack();
        jsonError($e->getMessage(), 500);
    }
}

methodNotAllowed();
