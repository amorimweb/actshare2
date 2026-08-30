<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAdmin();
$method = $_SERVER['REQUEST_METHOD'];
$id     = (int)($GLOBALS['_ROUTE']['id'] ?? 0);
if (!$id) jsonError('ID inválido.', 400);

$db = getDB();

if ($method === 'PUT') {
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
        jsonError('A vinculação a uma aula é obrigatória.', 400);
    }
    if (count($opcoes) < 2) {
        jsonError('A pergunta deve conter pelo menos duas alternativas.', 400);
    }
    if (count($opcoes) > 5) {
        jsonError('A pergunta pode ter no máximo 5 alternativas.', 400);
    }

    // curso_id/modulo_id sempre derivados da aula no servidor (ver perguntas.php)
    $stmtAula = $db->prepare('SELECT m.curso_id, a.modulo_id FROM aulas a JOIN modulos m ON m.id = a.modulo_id WHERE a.id = ?');
    $stmtAula->execute([$aulaId]);
    $aulaInfo = $stmtAula->fetch();
    if (!$aulaInfo) {
        jsonError('Aula não encontrada.', 400);
    }
    $cursoId  = (int)$aulaInfo['curso_id'];
    $moduloId = (int)$aulaInfo['modulo_id'];

    // Verifica existência da pergunta
    $stmt = $db->prepare('SELECT id FROM perguntas WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    if (!$stmt->fetch()) {
        jsonError('Pergunta não encontrada.', 404);
    }

    $db->beginTransaction();
    try {
        // Atualiza a pergunta
        $stmt = $db->prepare('
            UPDATE perguntas
            SET aula_id = ?, curso_id = ?, modulo_id = ?, texto = ?, imagem_url = ?, justificativa = ?
            WHERE id = ?
        ');
        $stmt->execute([$aulaId, $cursoId, $moduloId, $texto, $imagemUrl, $justificativa, $id]);

        // Remove opções antigas
        $stmtDel = $db->prepare('DELETE FROM opcoes WHERE pergunta_id = ?');
        $stmtDel->execute([$id]);

        // Insere as novas opções
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

            $stmtOpcao->execute([$id, $txtOpt, $correta]);
        }

        if (!$temCorreta) {
            throw new Exception('Pelo menos uma alternativa deve ser marcada como correta.');
        }

        $db->commit();
        jsonOk(['success' => true]);
    } catch (Exception $e) {
        $db->rollBack();
        jsonError($e->getMessage(), 400);
    }
}

if ($method === 'DELETE') {
    $stmt = $db->prepare('SELECT id FROM perguntas WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    if (!$stmt->fetch()) {
        jsonError('Pergunta não encontrada.', 404);
    }

    $stmt = $db->prepare('DELETE FROM perguntas WHERE id = ?');
    $stmt->execute([$id]);
    jsonOk(['success' => true]);
}

methodNotAllowed();
