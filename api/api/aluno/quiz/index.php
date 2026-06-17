<?php
require_once __DIR__ . '/../../../includes/db.php';
require_once __DIR__ . '/../../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') methodNotAllowed();

$user = requireAuth();
$id = (int)($GLOBALS['_ROUTE']['id'] ?? 0);
if (!$id) jsonError('ID da aula inválido.', 400);

$db = getDB();

// 1. Busca matrícula correspondente ao aluno logado e à aula do curso
$stmt = $db->prepare('
    SELECT m.id, m.com_prova, a.e_prova 
    FROM matriculas m 
    JOIN modulos mo ON m.curso_id = mo.curso_id 
    JOIN aulas a ON mo.id = a.modulo_id 
    WHERE m.aluno_id = ? AND a.id = ? 
    LIMIT 1
');
$stmt->execute([$user['id'], $id]);
$matricula = $stmt->fetch();
if (!$matricula) {
    jsonError('Matrícula não encontrada ou aluno não matriculado neste curso.', 403);
}
$matriculaId = (int)$matricula['id'];

// 2. Busca se o aluno já respondeu ou possui tentativas salvas
$stmt = $db->prepare('SELECT aprovado, tentativas_restantes FROM quiz_resposta WHERE matricula_id = ? AND aula_id = ? LIMIT 1');
$stmt->execute([$matriculaId, $id]);
$resposta = $stmt->fetch();

$aprovado = $resposta ? (bool)$resposta['aprovado'] : false;
$tentativasRestantes = $resposta ? (int)$resposta['tentativas_restantes'] : 5;
$finalizado = $aprovado || ($tentativasRestantes <= 0);

// 3. Busca se já há perguntas sorteadas para esta matrícula/aula
$stmt = $db->prepare('
    SELECT p.id, p.texto, p.justificativa 
    FROM quiz_perguntas_sorteadas qps 
    JOIN perguntas p ON qps.pergunta_id = p.id 
    WHERE qps.matricula_id = ? AND qps.aula_id = ? 
    ORDER BY p.id
');
$stmt->execute([$matriculaId, $id]);
$perguntas = $stmt->fetchAll();

// 4. Caso não haja perguntas sorteadas, realiza o sorteio aleatório
if (empty($perguntas)) {
    // Busca a quantidade a sortear configurada na aula
    $stmt = $db->prepare('SELECT quizz_qtd_perguntas FROM aulas WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $aula = $stmt->fetch();
    $qtdSortear = $aula ? (int)$aula['quizz_qtd_perguntas'] : 1;
    if ($qtdSortear <= 0) $qtdSortear = 1;

    // Busca todas as perguntas disponíveis no pool da aula
    $stmt = $db->prepare('SELECT id, texto, justificativa FROM perguntas WHERE aula_id = ?');
    $stmt->execute([$id]);
    $pool = $stmt->fetchAll();

    if (!empty($pool)) {
        shuffle($pool);
        $selected = array_slice($pool, 0, $qtdSortear);

        $db->beginTransaction();
        try {
            $ins = $db->prepare('INSERT INTO quiz_perguntas_sorteadas (matricula_id, aula_id, pergunta_id) VALUES (?, ?, ?)');
            foreach ($selected as $p) {
                $ins->execute([$matriculaId, $id, $p['id']]);
            }
            $db->commit();
            $perguntas = $selected;
        } catch (Exception $e) {
            $db->rollBack();
            jsonError('Erro ao registrar sorteio de perguntas.', 500);
        }
    }
}

// 5. Formatar retorno com embaralhamento de opções e trava de gabarito/justificativa
foreach ($perguntas as &$p) {
    $stmt2 = $db->prepare('SELECT id, texto, correta FROM opcoes WHERE pergunta_id = ?');
    $stmt2->execute([$p['id']]);
    $opcoes = $stmt2->fetchAll();

    $standard = [];
    $suffix = [];
    
    // Divide para fixar "todas" ou "nenhuma" no final
    foreach ($opcoes as $opt) {
        $txtLower = mb_strtolower($opt['texto']);
        if (str_contains($txtLower, 'nenhuma') || str_contains($txtLower, 'todas')) {
            $suffix[] = $opt;
        } else {
            $standard[] = $opt;
        }
    }
    
    shuffle($standard);
    $opcoesEmbaralhadas = array_merge($standard, $suffix);

    $p['opcoes'] = [];
    foreach ($opcoesEmbaralhadas as $opt) {
        $item = [
            'id' => $opt['id'],
            'texto' => $opt['texto']
        ];
        // O gabarito só é enviado ao frontend se o aluno já finalizou o quizz
        if ($finalizado) {
            $item['correta'] = (bool)$opt['correta'];
        }
        $p['opcoes'][] = $item;
    }

    // A justificativa pedagógica é oculta até que o aluno conclua ou esgote as tentativas
    if (!$finalizado) {
        unset($p['justificativa']);
    }
}

jsonOk([
    'perguntas'            => $perguntas,
    'aprovado'             => $aprovado,
    'tentativas_restantes' => $tentativasRestantes,
    'finalizado'           => $finalizado,
    'com_prova'            => (bool)$matricula['com_prova'],
    'e_prova'              => (bool)$matricula['e_prova']
]);
