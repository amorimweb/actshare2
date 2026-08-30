<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAdmin();
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $exameCursoId = isset($_GET['exame_curso_id']) ? (int)$_GET['exame_curso_id'] : 0;
    if (!$exameCursoId) jsonError('Informe exame_curso_id.', 400);

    $stmt = $db->prepare('SELECT * FROM exame_perguntas WHERE exame_curso_id = ? ORDER BY id DESC');
    $stmt->execute([$exameCursoId]);
    $perguntas = $stmt->fetchAll();

    foreach ($perguntas as &$p) {
        $stmt2 = $db->prepare('SELECT * FROM exame_opcoes WHERE pergunta_id = ? ORDER BY id');
        $stmt2->execute([$p['id']]);
        $p['opcoes'] = $stmt2->fetchAll();
    }
    jsonOk($perguntas);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true) ?? [];
    $exameCursoId = (int)($body['exame_curso_id'] ?? 0);
    $texto = trim($body['texto'] ?? '');
    $justificativa = trim($body['justificativa'] ?? '') ?: null;
    $opcoes = $body['opcoes'] ?? [];

    if (!$exameCursoId) jsonError('Informe exame_curso_id.', 400);
    if (!$texto) jsonError('O texto da pergunta é obrigatório.', 400);
    if (count($opcoes) < 2 || count($opcoes) > 5) jsonError('A pergunta deve ter entre 2 e 5 alternativas.', 400);

    $db->beginTransaction();
    try {
        $stmt = $db->prepare('INSERT INTO exame_perguntas (exame_curso_id, texto, justificativa) VALUES (?, ?, ?)');
        $stmt->execute([$exameCursoId, $texto, $justificativa]);
        $perguntaId = (int)$db->lastInsertId();

        $stmtOpcao = $db->prepare('INSERT INTO exame_opcoes (pergunta_id, texto, correta) VALUES (?, ?, ?)');
        $temCorreta = false;
        foreach ($opcoes as $opt) {
            $txt = trim($opt['texto'] ?? '');
            if (!$txt) throw new Exception('O texto de todas as alternativas deve ser preenchido.');
            $correta = !empty($opt['correta']) ? 1 : 0;
            if ($correta) $temCorreta = true;
            $stmtOpcao->execute([$perguntaId, $txt, $correta]);
        }
        if (!$temCorreta) throw new Exception('Pelo menos uma alternativa deve ser marcada como correta.');

        $db->commit();
        jsonOk(['success' => true, 'id' => $perguntaId], 201);
    } catch (Exception $e) {
        $db->rollBack();
        jsonError($e->getMessage(), 400);
    }
}

methodNotAllowed();
