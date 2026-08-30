<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

$user = requireAuth();
$tentativaId = (int)($GLOBALS['_ROUTE']['tentativa_id'] ?? 0);
if (!$tentativaId) jsonError('Tentativa inválida.', 400);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') methodNotAllowed();

$db = getDB();
$body = json_decode(file_get_contents('php://input'), true) ?? [];
$respostas = $body['respostas'] ?? []; // { pergunta_id: opcao_id }

$stmt = $db->prepare('
    SELECT et.*, ec.tipo AS exame_tipo, ec.nota_corte_tipo, ec.nota_corte_valor, ec.tempo_limite_minutos,
           m.aluno_id, m.curso_id, m.id AS matricula_id
    FROM exame_tentativas et
    JOIN exames_curso ec ON ec.id = et.exame_curso_id
    JOIN matriculas m ON m.id = et.matricula_id
    WHERE et.id = ?
    LIMIT 1
');
$stmt->execute([$tentativaId]);
$tentativa = $stmt->fetch();

if (!$tentativa) jsonError('Tentativa não encontrada.', 404);
if ((int)$tentativa['aluno_id'] !== (int)$user['id']) jsonError('Acesso negado.', 403);
if ($tentativa['finalizado_em']) jsonError('Este exame já foi finalizado.', 400);

$sorteio = json_decode($tentativa['respostas_json'] ?? '[]', true)['sorteio'] ?? [];
$totalQuestoes = count($sorteio);

$acertos = 0;
$erros = 0;
$naoRespondidas = 0;
$detalhe = [];

foreach ($sorteio as $perguntaId) {
    $stmtOp = $db->prepare('SELECT id, correta FROM exame_opcoes WHERE pergunta_id = ?');
    $stmtOp->execute([$perguntaId]);
    $opcoes = $stmtOp->fetchAll();
    $corretasIds = array_column(array_filter($opcoes, fn($o) => (int)$o['correta'] === 1), 'id');

    $marcadas = $respostas[$perguntaId] ?? null;
    if ($marcadas === null || (is_array($marcadas) && count($marcadas) === 0)) {
        $naoRespondidas++;
        $detalhe[$perguntaId] = ['marcadas' => [], 'correto' => false];
        continue;
    }

    $marcadasArr = is_array($marcadas) ? array_map('intval', $marcadas) : [(int)$marcadas];
    sort($marcadasArr);
    $corretasArr = array_map('intval', $corretasIds);
    sort($corretasArr);

    $correto = ($marcadasArr === $corretasArr);
    if ($correto) $acertos++; else $erros++;
    $detalhe[$perguntaId] = ['marcadas' => $marcadasArr, 'correto' => $correto];
}

// Aplica a nota de corte configurada no produto (qtd. de acertos ou %)
if ($tentativa['nota_corte_tipo'] === 'questoes') {
    $aprovado = $acertos >= (int)$tentativa['nota_corte_valor'];
} else {
    $percentual = $totalQuestoes > 0 ? ($acertos / $totalQuestoes) * 100 : 0;
    $aprovado = $percentual >= (int)$tentativa['nota_corte_valor'];
}
$resultado = $aprovado ? 'aprovado' : 'reprovado';

$db->beginTransaction();
try {
    $prazoRetakeAte = null;
    if (!$aprovado) {
        // Exame Exemplar Global (QM/AU/TL): reprovado pode refazer em até 1
        // ano, e o prazo do próprio treinamento é adiado junto (o aluno não
        // fica sem acesso ao curso enquanto aguarda a nova tentativa).
        if (in_array($tentativa['exame_tipo'], ['QM', 'AU', 'TL'])) {
            $prazoRetakeAte = date('Y-m-d H:i:s', strtotime('+1 year'));
            $db->prepare('UPDATE matriculas SET data_fim_acesso = ? WHERE id = ?')
               ->execute([$prazoRetakeAte, $tentativa['matricula_id']]);
        }
    }

    $stmtUpd = $db->prepare('
        UPDATE exame_tentativas
        SET finalizado_em = NOW(), total_questoes = ?, acertos = ?, erros = ?, nao_respondidas = ?,
            resultado = ?, respostas_json = ?, prazo_retake_ate = ?
        WHERE id = ?
    ');
    $stmtUpd->execute([
        $totalQuestoes, $acertos, $erros, $naoRespondidas, $resultado,
        json_encode(['sorteio' => $sorteio, 'detalhe' => $detalhe]), $prazoRetakeAte, $tentativaId,
    ]);

    $db->commit();
} catch (Exception $e) {
    $db->rollBack();
    jsonError($e->getMessage(), 500);
}

$percentualFinal = $totalQuestoes > 0 ? round(($acertos / $totalQuestoes) * 100) : 0;

jsonOk([
    'resultado' => $resultado,
    'total_questoes' => $totalQuestoes,
    'acertos' => $acertos,
    'erros' => $erros,
    'nao_respondidas' => $naoRespondidas,
    'percentual' => $percentualFinal,
    'prazo_retake_ate' => $prazoRetakeAte,
]);
