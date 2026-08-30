<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

$user = requireAuth();
$exameCursoId = (int)($GLOBALS['_ROUTE']['exame_curso_id'] ?? 0);
if (!$exameCursoId) jsonError('Exame inválido.', 400);
if ($_SERVER['REQUEST_METHOD'] !== 'GET') methodNotAllowed();

$db = getDB();

$stmt = $db->prepare('SELECT * FROM exames_curso WHERE id = ? AND ativo = 1 LIMIT 1');
$stmt->execute([$exameCursoId]);
$exame = $stmt->fetch();
if (!$exame) jsonError('Exame não encontrado.', 404);

// Confirma que o aluno está matriculado no curso e optou por esse exame
$stmt = $db->prepare('SELECT * FROM matriculas WHERE aluno_id = ? AND curso_id = ? LIMIT 1');
$stmt->execute([$user['id'], $exame['curso_id']]);
$matricula = $stmt->fetch();
if (!$matricula) jsonError('Você não está matriculado neste treinamento.', 403);

$selecionados = array_filter(explode(',', $matricula['exames_selecionados'] ?? ''));
if (!in_array($exame['tipo'], $selecionados)) {
    jsonError('Você não optou por este exame na compra do treinamento.', 403);
}

// Já existe tentativa em aberto? Retoma. Já existe tentativa finalizada?
// Só permite nova se reprovado E dentro da janela de retake de 1 ano.
$stmt = $db->prepare('SELECT * FROM exame_tentativas WHERE matricula_id = ? AND exame_curso_id = ? ORDER BY id DESC LIMIT 1');
$stmt->execute([$matricula['id'], $exameCursoId]);
$tentativaExistente = $stmt->fetch();

if ($tentativaExistente && $tentativaExistente['finalizado_em']) {
    if ($tentativaExistente['resultado'] === 'aprovado') {
        jsonError('Você já foi aprovado neste exame.', 400);
    }
    if ($tentativaExistente['prazo_retake_ate'] && strtotime($tentativaExistente['prazo_retake_ate']) < time()) {
        jsonError('O prazo para refazer este exame (1 ano após a reprovação) expirou.', 400);
    }
}

if ($tentativaExistente && !$tentativaExistente['finalizado_em']) {
    // Retoma a tentativa em andamento com as mesmas perguntas já sorteadas
    $tentativaId = $tentativaExistente['id'];
    $perguntaIds = json_decode($tentativaExistente['respostas_json'] ?? '[]', true)['sorteio'] ?? [];
} else {
    // Sorteia N perguntas do banco do exame
    $stmt = $db->prepare('SELECT id FROM exame_perguntas WHERE exame_curso_id = ? ORDER BY RAND() LIMIT ?');
    $stmt->bindValue(1, $exameCursoId, PDO::PARAM_INT);
    $stmt->bindValue(2, (int)$exame['numero_questoes'], PDO::PARAM_INT);
    $stmt->execute();
    $perguntaIds = array_column($stmt->fetchAll(), 'id');

    if (count($perguntaIds) < 1) {
        jsonError('Este exame ainda não tem perguntas cadastradas. Fale com o administrador.', 400);
    }

    $stmt = $db->prepare('INSERT INTO exame_tentativas (matricula_id, exame_curso_id, respostas_json) VALUES (?, ?, ?)');
    $stmt->execute([$matricula['id'], $exameCursoId, json_encode(['sorteio' => $perguntaIds])]);
    $tentativaId = (int)$db->lastInsertId();
}

// Monta as perguntas sorteadas (sem revelar qual alternativa é a correta)
$placeholders = implode(',', array_fill(0, count($perguntaIds), '?'));
$stmt = $db->prepare("SELECT id, texto, imagem_url FROM exame_perguntas WHERE id IN ($placeholders)");
$stmt->execute($perguntaIds);
$perguntasPorId = [];
foreach ($stmt->fetchAll() as $p) { $perguntasPorId[$p['id']] = $p; }

$perguntas = [];
foreach ($perguntaIds as $pid) {
    if (!isset($perguntasPorId[$pid])) continue;
    $p = $perguntasPorId[$pid];
    $stmtOp = $db->prepare('SELECT id, texto FROM exame_opcoes WHERE pergunta_id = ? ORDER BY RAND()');
    $stmtOp->execute([$pid]);
    $p['opcoes'] = $stmtOp->fetchAll();
    $perguntas[] = $p;
}

// Tempo restante calculado no servidor (evita qualquer problema de fuso
// horário entre o horário gravado no banco e o relógio do navegador do aluno).
$iniciadoEm = $tentativaExistente['iniciado_em'] ?? date('Y-m-d H:i:s');
$segundosDecorridos = time() - strtotime($iniciadoEm);
$segundosRestantes = max(0, ((int)$exame['tempo_limite_minutos'] * 60) - $segundosDecorridos);

jsonOk([
    'tentativa_id' => $tentativaId,
    'exame' => ['tipo' => $exame['tipo'], 'nome' => $exame['nome'], 'tempo_limite_minutos' => (int)$exame['tempo_limite_minutos']],
    'segundos_restantes' => $segundosRestantes,
    'perguntas' => $perguntas,
]);
