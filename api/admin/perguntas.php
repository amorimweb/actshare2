<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAdmin();
$method = $_SERVER['REQUEST_METHOD'];
$db = getDB();

if ($method === 'GET') {
    $cursoId  = isset($_GET['curso_id']) && $_GET['curso_id'] !== '' ? (int)$_GET['curso_id'] : null;
    $moduloId = isset($_GET['modulo_id']) && $_GET['modulo_id'] !== '' ? (int)$_GET['modulo_id'] : null;
    $aulaId   = isset($_GET['aula_id']) && $_GET['aula_id'] !== '' ? (int)$_GET['aula_id'] : null;

    $query = "
        SELECT p.*, c.titulo AS curso_titulo, m.titulo AS modulo_titulo, a.titulo AS aula_titulo, a.e_prova
        FROM perguntas p
        LEFT JOIN cursos c ON p.curso_id = c.id
        LEFT JOIN modulos m ON p.modulo_id = m.id
        LEFT JOIN aulas a ON p.aula_id = a.id
    ";
    
    $where = [];
    $params = [];
    if ($cursoId) {
        $where[] = "p.curso_id = ?";
        $params[] = $cursoId;
    }
    if ($moduloId) {
        $where[] = "p.modulo_id = ?";
        $params[] = $moduloId;
    }
    if ($aulaId) {
        $where[] = "p.aula_id = ?";
        $params[] = $aulaId;
    }

    if ($where) {
        $query .= " WHERE " . implode(" AND ", $where);
    }
    $query .= " ORDER BY p.id DESC";

    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $perguntas = $stmt->fetchAll();

    foreach ($perguntas as &$p) {
        $stmt2 = $db->prepare('SELECT * FROM opcoes WHERE pergunta_id = ? ORDER BY id');
        $stmt2->execute([$p['id']]);
        $p['opcoes'] = $stmt2->fetchAll();
    }

    jsonOk($perguntas);
}

if ($method === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true) ?? [];

    $texto         = trim($body['texto'] ?? '');
    $imagemUrl     = trim($body['imagem_url'] ?? '') ?: null;
    $justificativa = trim($body['justificativa'] ?? '');
    $cursoId       = isset($body['curso_id']) && $body['curso_id'] !== '' ? (int)$body['curso_id'] : null;
    $moduloId      = isset($body['modulo_id']) && $body['modulo_id'] !== '' ? (int)$body['modulo_id'] : null;
    $aulaId        = isset($body['aula_id']) && $body['aula_id'] !== '' ? (int)$body['aula_id'] : null;
    $opcoes        = $body['opcoes'] ?? [];

    if (empty($texto)) {
        jsonError('O texto da pergunta é obrigatório.', 400);
    }
    if (empty($justificativa)) {
        jsonError('A justificativa pedagógica é obrigatória.', 400);
    }
    if (empty($aulaId)) {
        jsonError('A vinculação a uma aula/exame é obrigatória.', 400);
    }
    if (count($opcoes) < 2) {
        jsonError('A pergunta deve conter pelo menos duas alternativas.', 400);
    }
    if (count($opcoes) > 5) {
        jsonError('A pergunta pode ter no máximo 5 alternativas.', 400);
    }

    $db->beginTransaction();
    try {
        // Insere a pergunta
        $stmt = $db->prepare('
            INSERT INTO perguntas (aula_id, curso_id, modulo_id, texto, imagem_url, justificativa)
            VALUES (?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([$aulaId, $cursoId, $moduloId, $texto, $imagemUrl, $justificativa]);
        $perguntaId = $db->lastInsertId();

        // Insere as opções
        $stmtOpcao = $db->prepare('
            INSERT INTO opcoes (pergunta_id, texto, correta)
            VALUES (?, ?, ?)
        ');
        
        $temCorreta = false;
        foreach ($opcoes as $opt) {
            $txtOpt = trim($opt['texto'] ?? '');
            if (empty($txtOpt)) {
                throw new Exception('O texto de todas as alternativas deve ser preenchido.');
            }
            $correta = !empty($opt['correta']) ? 1 : 0;
            if ($correta) $temCorreta = true;

            $stmtOpcao->execute([$perguntaId, $txtOpt, $correta]);
        }

        if (!$temCorreta) {
            throw new Exception('Pelo menos uma alternativa deve ser marcada como correta.');
        }

        $db->commit();
        jsonOk(['success' => true, 'id' => $perguntaId], 201);
    } catch (Exception $e) {
        $db->rollBack();
        jsonError($e->getMessage(), 400);
    }
}

methodNotAllowed();
