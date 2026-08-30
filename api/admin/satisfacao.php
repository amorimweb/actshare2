<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAdmin();
$method = $_SERVER['REQUEST_METHOD'];
$db = getDB();

if ($method === 'GET') {
    $cursoId   = isset($_GET['curso_id']) && $_GET['curso_id'] !== '' ? (int)$_GET['curso_id'] : null;
    $clienteId = isset($_GET['cliente_id']) && $_GET['cliente_id'] !== '' ? (int)$_GET['cliente_id'] : null;
    $alunoId   = isset($_GET['aluno_id']) && $_GET['aluno_id'] !== '' ? (int)$_GET['aluno_id'] : null;
    $export    = isset($_GET['export']) && $_GET['export'] === 'csv';

    $where = [];
    $params = [];
    if ($cursoId) { $where[] = 'm.curso_id = ?'; $params[] = $cursoId; }
    if ($alunoId) { $where[] = 'm.aluno_id = ?'; $params[] = $alunoId; }
    if ($clienteId) {
        $where[] = 'm.aluno_id IN (SELECT usuario_id FROM membros_organizacao WHERE organizacao_id = ?)';
        $params[] = $clienteId;
    }
    $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

    $stmt = $db->prepare("
        SELECT pp.id, pp.texto,
               COALESCE(AVG(pr.nota), 0) AS media,
               COUNT(pr.id) AS total_respostas
        FROM pesquisa_perguntas pp
        LEFT JOIN pesquisa_respostas pr ON pp.id = pr.pergunta_id
        LEFT JOIN matriculas m ON m.id = pr.matricula_id
        " . ($where ? "AND $whereSql" : '') . "
        GROUP BY pp.id, pp.texto
        ORDER BY pp.id
    ");
    $stmt->execute($params);
    $stats = $stmt->fetchAll();

    foreach ($stats as &$s) {
        $s['media'] = round((float)$s['media'], 1);
        $s['total_respostas'] = (int)$s['total_respostas'];
    }

    if ($export) {
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="pesquisa_satisfacao.csv"');
        echo "\xEF\xBB\xBF"; // BOM p/ abrir acentuado corretamente no Excel
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Pergunta', 'Média', 'Total de Respostas'], ';');
        foreach ($stats as $s) {
            fputcsv($out, [$s['texto'], number_format($s['media'], 1, ',', ''), $s['total_respostas']], ';');
        }
        fclose($out);
        exit;
    }

    jsonOk($stats);
}

if ($method === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true) ?? [];
    $texto = trim($body['texto'] ?? '');
    if (!$texto) jsonError('O texto da pergunta é obrigatório.', 400);

    $stmt = $db->prepare('INSERT INTO pesquisa_perguntas (texto) VALUES (?)');
    $stmt->execute([$texto]);
    jsonOk(['id' => $db->lastInsertId(), 'texto' => $texto], 201);
}

methodNotAllowed();
