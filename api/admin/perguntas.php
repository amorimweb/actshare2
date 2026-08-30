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

    // Resolve curso/módulo sempre pela aula (fonte da verdade), não pela coluna
    // p.curso_id/p.modulo_id isolada — perguntas antigas podem ter ficado com
    // esses campos nulos mesmo pertencendo a um curso/aula válidos.
    $query = "
        SELECT p.*, rc.id AS resolved_curso_id, rc.titulo AS curso_titulo,
               rm.id AS resolved_modulo_id, rm.titulo AS modulo_titulo,
               a.titulo AS aula_titulo, a.e_prova
        FROM perguntas p
        LEFT JOIN aulas a ON p.aula_id = a.id
        LEFT JOIN modulos rm ON rm.id = a.modulo_id
        LEFT JOIN cursos rc ON rc.id = rm.curso_id
    ";

    $where = [];
    $params = [];
    if ($cursoId) {
        $where[] = "rc.id = ?";
        $params[] = $cursoId;
    }
    if ($moduloId) {
        $where[] = "rm.id = ?";
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

    // curso_id/modulo_id sempre derivados da aula no servidor — nunca confia no
    // que o cliente mandar, pra esses campos nunca ficarem dessincronizados
    // (foi exatamente isso que quebrou o filtro do Banco de Questões antes).
    $stmtAula = $db->prepare('SELECT m.curso_id, a.modulo_id FROM aulas a JOIN modulos m ON m.id = a.modulo_id WHERE a.id = ?');
    $stmtAula->execute([$aulaId]);
    $aulaInfo = $stmtAula->fetch();
    if (!$aulaInfo) {
        jsonError('Aula não encontrada.', 400);
    }
    $cursoId  = (int)$aulaInfo['curso_id'];
    $moduloId = (int)$aulaInfo['modulo_id'];

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
