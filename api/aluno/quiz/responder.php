<?php
require_once __DIR__ . '/../../../includes/db.php';
require_once __DIR__ . '/../../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') methodNotAllowed();

$user = requireAuth();
$body = json_decode(file_get_contents('php://input'), true) ?? [];

$aulaId      = (int)($body['aula_id']      ?? 0);
$matriculaId = (int)($body['matricula_id'] ?? 0);
$respostas   = $body['respostas'] ?? []; // [pergunta_id => opcao_id]
$bypassAvançar = (bool)($body['avancar']   ?? false); // bypass para avançar sem tentar novamente

if (!$aulaId || !$matriculaId) {
    jsonError('aula_id e matricula_id são obrigatórios.', 400);
}

$db = getDB();

// 1. Verifica matrícula do usuário
$stmt = $db->prepare('SELECT id FROM matriculas WHERE id = ? AND aluno_id = ? LIMIT 1');
$stmt->execute([$matriculaId, $user['id']]);
if (!$stmt->fetch()) {
    jsonError('Matrícula não encontrada.', 403);
}

// 2. Busca tentativas anteriores
$stmt = $db->prepare('SELECT id, aprovado, tentativas_restantes FROM quiz_resposta WHERE matricula_id = ? AND aula_id = ? LIMIT 1');
$stmt->execute([$matriculaId, $aulaId]);
$respostaSalva = $stmt->fetch();

$tentativasRestantes = $respostaSalva ? (int)$respostaSalva['tentativas_restantes'] : 5;
$jaAprovado = $respostaSalva ? (bool)$respostaSalva['aprovado'] : false;

if ($jaAprovado) {
    jsonError('Você já foi aprovado neste quizz.', 400);
}

// Se o usuário clicou em "Avançar" sem tentativas restantes, ou quer apenas forçar a conclusão da aula mesmo reprovado
if ($bypassAvançar) {
    if ($tentativasRestantes > 0) {
        jsonError('Você ainda possui tentativas restantes e não pode pular o quizz.', 400);
    }
    
    // Marca aula como concluída no progresso
    $stmt = $db->prepare('
        INSERT INTO progresso_aula (matricula_id, aula_id, concluida, data_conclusao)
        VALUES (?, ?, 1, NOW())
        ON DUPLICATE KEY UPDATE concluida = 1, data_conclusao = COALESCE(data_conclusao, NOW()), updated_at = NOW()
    ');
    $stmt->execute([$matriculaId, $aulaId]);

    // Recalcula progresso geral do curso e atualiza a matrícula
    $stmtMat = $db->prepare('SELECT curso_id FROM matriculas WHERE id = ? LIMIT 1');
    $stmtMat->execute([$matriculaId]);
    $cursoId = (int)$stmtMat->fetchColumn();

    $stmtAulas = $db->prepare('
        SELECT COUNT(*) AS total 
        FROM aulas a
        JOIN modulos m ON a.modulo_id = m.id
        WHERE m.curso_id = ?
    ');
    $stmtAulas->execute([$cursoId]);
    $totalAulas = (int)$stmtAulas->fetchColumn();

    $stmtProg = $db->prepare('
        SELECT COUNT(*) AS concluidas 
        FROM progresso_aula 
        WHERE matricula_id = ? AND concluida = 1
    ');
    $stmtProg->execute([$matriculaId]);
    $concluidas = (int)$stmtProg->fetchColumn();

    $percentual = $totalAulas > 0 ? (int)round(($concluidas / $totalAulas) * 100) : 100;
    if ($percentual > 100) $percentual = 100;

    $concluido = ($percentual >= 100) ? 1 : 0;
    $dataConclusaoMat = $concluido ? date('Y-m-d H:i:s') : null;

    $stmtUpdateMat = $db->prepare('
        UPDATE matriculas
        SET progresso_total = ?, concluido = ?, data_conclusao = COALESCE(data_conclusao, ?)
        WHERE id = ?
    ');
    $stmtUpdateMat->execute([$percentual, $concluido, $dataConclusaoMat, $matriculaId]);
    
    jsonOk([
        'skipped' => true,
        'message' => 'Aula concluída. Você avançou sem aprovação no quizz.'
    ]);
}

// Caso normal: processando respostas
if (empty($respostas) && empty($body['tempo_esgotado'])) {
    jsonError('Respostas do quizz são obrigatórias.', 400);
}

if ($tentativasRestantes <= 0) {
    jsonError('Você esgotou suas tentativas neste quizz. Clique em Avançar.', 400);
}

// 3. Busca apenas as perguntas sorteadas para este aluno/aula
$stmt = $db->prepare('
    SELECT p.id, p.texto, p.justificativa 
    FROM quiz_perguntas_sorteadas qps 
    JOIN perguntas p ON qps.pergunta_id = p.id
    WHERE qps.matricula_id = ? AND qps.aula_id = ?
');
$stmt->execute([$matriculaId, $aulaId]);
$perguntasSorteadas = $stmt->fetchAll();

if (empty($perguntasSorteadas)) {
    jsonError('Nenhuma questão sorteada para este quizz foi localizada.', 400);
}

$acertos = 0;
$total = count($perguntasSorteadas);
$detalhesCorrecao = [];

foreach ($perguntasSorteadas as $p) {
    $opcaoEscolhida = (int)($respostas[$p['id']] ?? 0);
    
    // Busca opção correta para a pergunta
    $stmt = $db->prepare('SELECT id, texto FROM opcoes WHERE pergunta_id = ? AND correta = 1 LIMIT 1');
    $stmt->execute([$p['id']]);
    $opcaoCorreta = $stmt->fetch();
    
    // Verifica se acertou
    $acertou = ($opcaoEscolhida === (int)($opcaoCorreta['id'] ?? 0));
    if ($acertou) {
        $acertos++;
    }
    
    $detalhesCorrecao[] = [
        'pergunta_id'        => $p['id'],
        'texto_pergunta'     => $p['texto'],
        'opcao_escolhida_id' => $opcaoEscolhida,
        'opcao_correta_id'   => (int)($opcaoCorreta['id'] ?? 0),
        'texto_correta'      => $opcaoCorreta['texto'] ?? '',
        'acertou'            => $acertou,
        'justificativa'      => $p['justificativa'] ?: 'Resposta correta: ' . ($opcaoCorreta['texto'] ?? '')
    ];
}

$nota = $total > 0 ? (int)round(($acertos / $total) * 100) : 0;
$aprovado = $nota >= 70;

// Decrementa tentativas se falhar
if (!$aprovado) {
    $tentativasRestantes--;
}

// 4. Salva ou atualiza resultado
if ($respostaSalva) {
    $stmt = $db->prepare('
        UPDATE quiz_resposta 
        SET nota = ?, aprovado = ?, acertos = ?, total_perguntas = ?, tentativas_restantes = ?, updated_at = NOW()
        WHERE id = ?
    ');
    $stmt->execute([$nota, $aprovado ? 1 : 0, $acertos, $total, $tentativasRestantes, $respostaSalva['id']]);
} else {
    $stmt = $db->prepare('
        INSERT INTO quiz_resposta (matricula_id, aula_id, nota, aprovado, acertos, total_perguntas, tentativas_restantes)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ');
    $stmt->execute([$matriculaId, $aulaId, $nota, $aprovado ? 1 : 0, $acertos, $total, $tentativasRestantes]);
}

// Se for prova/exame oficial, registra na tabela avaliacao_tentativas
$stmtAula = $db->prepare('SELECT e_prova FROM aulas WHERE id = ? LIMIT 1');
$stmtAula->execute([$aulaId]);
$eProva = (int)$stmtAula->fetchColumn();

if ($eProva === 1) {
    $resultado = $aprovado ? 'aprovado' : 'reprovado';
    $erros = $total - $acertos;
    $respostasJson = json_encode($detalhesCorrecao, JSON_UNESCAPED_UNICODE);
    
    $stmtLog = $db->prepare('
        INSERT INTO avaliacao_tentativas (matricula_id, aula_id, total_questoes, acertos, erros, nao_respondidas, nota, resultado, respostas_json)
        VALUES (?, ?, ?, ?, ?, 0, ?, ?, ?)
    ');
    $stmtLog->execute([$matriculaId, $aulaId, $total, $acertos, $erros, $nota, $resultado, $respostasJson]);
}

// 5. Se aprovado, marca aula como concluída no progresso
if ($aprovado) {
    $stmt = $db->prepare('
        INSERT INTO progresso_aula (matricula_id, aula_id, concluida, data_conclusao)
        VALUES (?, ?, 1, NOW())
        ON DUPLICATE KEY UPDATE concluida = 1, data_conclusao = COALESCE(data_conclusao, NOW()), updated_at = NOW()
    ');
    $stmt->execute([$matriculaId, $aulaId]);

    // Recalcula progresso geral do curso e atualiza a matrícula
    $stmtMat = $db->prepare('SELECT curso_id FROM matriculas WHERE id = ? LIMIT 1');
    $stmtMat->execute([$matriculaId]);
    $cursoId = (int)$stmtMat->fetchColumn();

    $stmtAulas = $db->prepare('
        SELECT COUNT(*) AS total 
        FROM aulas a
        JOIN modulos m ON a.modulo_id = m.id
        WHERE m.curso_id = ?
    ');
    $stmtAulas->execute([$cursoId]);
    $totalAulas = (int)$stmtAulas->fetchColumn();

    $stmtProg = $db->prepare('
        SELECT COUNT(*) AS concluidas 
        FROM progresso_aula 
        WHERE matricula_id = ? AND concluida = 1
    ');
    $stmtProg->execute([$matriculaId]);
    $concluidas = (int)$stmtProg->fetchColumn();

    $percentual = $totalAulas > 0 ? (int)round(($concluidas / $totalAulas) * 100) : 100;
    if ($percentual > 100) $percentual = 100;

    $concluido = ($percentual >= 100) ? 1 : 0;
    $dataConclusaoMat = $concluido ? date('Y-m-d H:i:s') : null;

    $stmtUpdateMat = $db->prepare('
        UPDATE matriculas
        SET progresso_total = ?, concluido = ?, data_conclusao = COALESCE(data_conclusao, ?)
        WHERE id = ?
    ');
    $stmtUpdateMat->execute([$percentual, $concluido, $dataConclusaoMat, $matriculaId]);
}

jsonOk([
    'nota'                 => $nota,
    'aprovado'             => $aprovado,
    'acertos'              => $acertos,
    'total'                => $total,
    'tentativas_restantes' => $tentativasRestantes,
    'correcao'             => $detalhesCorrecao
]);
