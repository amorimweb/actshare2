<?php
require_once __DIR__ . '/../../../includes/db.php';
require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../includes/matriculas.php';

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
$stmt = $db->prepare('SELECT id, aprovado, tentativas_restantes, iniciado_em FROM quiz_resposta WHERE matricula_id = ? AND aula_id = ? LIMIT 1');
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

    recalcularConclusaoMatricula($db, $matriculaId);

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

// Configuração de nota de corte e tipo de avaliação da aula
$stmtAula = $db->prepare('SELECT e_prova, nota_corte_tipo, nota_corte_valor, tempo_limite_minutos FROM aulas WHERE id = ? LIMIT 1');
$stmtAula->execute([$aulaId]);
$aulaConfig = $stmtAula->fetch() ?: [];
$eProva         = (int)($aulaConfig['e_prova'] ?? 0);
$notaCorteTipo  = $aulaConfig['nota_corte_tipo']  ?? 'percentual';
$notaCorteValor = (int)($aulaConfig['nota_corte_valor'] ?? 70);
$tempoLimiteMin = (int)($aulaConfig['tempo_limite_minutos'] ?? 0);

// Tempo controlado pelo servidor: mesmo que o cronômetro do navegador seja
// manipulado, o tempo real decorrido desde "iniciado_em" (gravado quando a
// prova foi aberta, em api/aluno/quiz/index.php) é quem manda. Passado o
// limite (+ margem de tolerância pra reconexões), força o fim da prova.
$tempoEsgotadoServidor = false;
if ($eProva === 1 && $tempoLimiteMin > 0 && !empty($respostaSalva['iniciado_em'])) {
    $margemToleranciaMin = 30; // cobre as pausas de reconexão já tratadas no cliente
    $minutosDecorridos = (time() - strtotime($respostaSalva['iniciado_em'])) / 60;
    if ($minutosDecorridos > ($tempoLimiteMin + $margemToleranciaMin)) {
        $tempoEsgotadoServidor = true;
    }
}

$acertos = 0;
$naoRespondidas = 0;
$total = count($perguntasSorteadas);
$detalhesCorrecao = [];

foreach ($perguntasSorteadas as $p) {
    // O front envia um array de ids marcados por pergunta (uma pergunta pode
    // ter mais de uma alternativa correta). Aceita também um valor único
    // (compatibilidade com integrações antigas que mandavam 1 id só).
    $respostaBruta = $respostas[$p['id']] ?? [];
    $opcoesEscolhidas = array_values(array_unique(array_map('intval', (array)$respostaBruta)));
    if (empty($opcoesEscolhidas)) $naoRespondidas++;

    // Busca todas as opções corretas para a pergunta
    $stmt = $db->prepare('SELECT id, texto FROM opcoes WHERE pergunta_id = ? AND correta = 1');
    $stmt->execute([$p['id']]);
    $opcoesCorretas = $stmt->fetchAll();
    $idsCorretos = array_map(fn($o) => (int)$o['id'], $opcoesCorretas);

    // Acerta só quem marcou exatamente o mesmo conjunto de alternativas corretas
    sort($opcoesEscolhidas);
    $idsCorretosOrdenados = $idsCorretos;
    sort($idsCorretosOrdenados);
    $acertou = ($opcoesEscolhidas === $idsCorretosOrdenados);
    if ($acertou) {
        $acertos++;
    }

    $detalhesCorrecao[] = [
        'pergunta_id'         => $p['id'],
        'texto_pergunta'      => $p['texto'],
        'opcoes_escolhidas'   => $opcoesEscolhidas,
        'opcoes_corretas'     => $idsCorretos,
        'textos_corretos'     => array_map(fn($o) => $o['texto'], $opcoesCorretas),
        'acertou'             => $acertou,
        'justificativa'       => $eProva ? null : ($p['justificativa'] ?: null),
    ];
}

$nota = $total > 0 ? (int)round(($acertos / $total) * 100) : 0;

// Nota de corte configurada pelo admin na aula: por qtd. de questões certas
// ou por percentual — antes disso ficava fixo em 70%, ignorando o cadastro.
$aprovado = ($notaCorteTipo === 'questoes')
    ? ($acertos >= $notaCorteValor)
    : ($nota >= $notaCorteValor);

// Decrementa tentativas se falhar
if (!$aprovado) {
    $tentativasRestantes--;
}

// Tempo esgotado de verdade (validado pelo servidor): não há mais direito a
// refazer, a prova encerrou.
if ($tempoEsgotadoServidor && !$aprovado) {
    $tentativasRestantes = 0;
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
if ($eProva === 1) {
    $resultado = $aprovado ? 'aprovado' : 'reprovado';
    $erros = $total - $acertos - $naoRespondidas; // respondidas errado (exclui as não respondidas)
    $respostasJson = json_encode($detalhesCorrecao, JSON_UNESCAPED_UNICODE);

    $stmtLog = $db->prepare('
        INSERT INTO avaliacao_tentativas (matricula_id, aula_id, total_questoes, acertos, erros, nao_respondidas, nota, resultado, respostas_json)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');
    $stmtLog->execute([$matriculaId, $aulaId, $total, $acertos, $erros, $naoRespondidas, $nota, $resultado, $respostasJson]);
}

// 5. Se aprovado, marca aula como concluída no progresso
if ($aprovado) {
    $stmt = $db->prepare('
        INSERT INTO progresso_aula (matricula_id, aula_id, concluida, data_conclusao)
        VALUES (?, ?, 1, NOW())
        ON DUPLICATE KEY UPDATE concluida = 1, data_conclusao = COALESCE(data_conclusao, NOW()), updated_at = NOW()
    ');
    $stmt->execute([$matriculaId, $aulaId]);

    recalcularConclusaoMatricula($db, $matriculaId);
}

jsonOk([
    'nota'                 => $nota,
    'aprovado'             => $aprovado,
    'acertos'              => $acertos,
    'total'                => $total,
    'tentativas_restantes' => $tentativasRestantes,
    'correcao'             => $detalhesCorrecao
]);
