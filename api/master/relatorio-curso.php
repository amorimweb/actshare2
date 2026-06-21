<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') methodNotAllowed();

$gestor = requireMasterOrAdmin();
$cursoId = (int)($GLOBALS['_ROUTE']['id'] ?? 0);

if (!$cursoId) {
    jsonError('ID do curso inválido.', 400);
}

$db = getDB();
$context = getGestorContext($gestor, $db);
$mainGestorId = $context['id'];
$orgId = $context['org_id'];

if (!$orgId) {
    jsonError('Organização não encontrada.', 404);
}
$org = ['id' => $orgId];

// 2. Busca o contrato B2B (matrícula do gestor)
$stmt = $db->prepare('
    SELECT m.id AS matricula_id, m.vagas_totais, m.vagas_usadas, m.participante, m.created_at AS data_compra,
           c.titulo AS curso_titulo, c.carga_horaria_horas
    FROM matriculas m
    JOIN cursos c ON m.curso_id = c.id
    WHERE m.aluno_id = ? AND m.curso_id = ? AND m.vagas_totais > 0
    LIMIT 1
');
$stmt->execute([$mainGestorId, $cursoId]);
$contract = $stmt->fetch();

if (!$contract) {
    jsonError('Contrato B2B não encontrado para este curso.', 404);
}

// 3. Busca participantes da organização matriculados no curso
$stmt = $db->prepare('
    SELECT u.id AS aluno_id, u.nome, u.email,
           m.id AS matricula_id, m.progresso_total, m.concluido, m.data_inicio, m.data_conclusao, m.data_fim_acesso
    FROM membros_organizacao mo
    JOIN usuarios u ON mo.usuario_id = u.id
    JOIN matriculas m ON u.id = m.aluno_id
    WHERE mo.organizacao_id = ? AND m.curso_id = ?
    ORDER BY u.nome
');
$stmt->execute([$org['id'], $cursoId]);
$participantes = $stmt->fetchAll();

// 4. Se o próprio gestor é participante, adiciona-o à lista
$stmt = $db->prepare('
    SELECT m.id AS matricula_id, m.progresso_total, m.concluido, m.data_inicio, m.data_conclusao, m.data_fim_acesso
    FROM matriculas m
    WHERE m.aluno_id = ? AND m.curso_id = ? AND m.vagas_totais > 0 AND m.participante = 1
    LIMIT 1
');
$stmt->execute([$mainGestorId, $cursoId]);
$selfContract = $stmt->fetch();

if ($selfContract) {
    array_unshift($participantes, [
        'aluno_id'        => $mainGestorId,
        'nome'            => $gestor['nome'] . ' (Você/Gestor)',
        'email'           => $gestor['email'],
        'matricula_id'    => $selfContract['matricula_id'],
        'progresso_total' => $selfContract['progresso_total'],
        'concluido'       => $selfContract['concluido'],
        'data_inicio'     => $selfContract['data_inicio'],
        'data_conclusao'  => $selfContract['data_conclusao'],
        'data_fim_acesso' => $selfContract['data_fim_acesso'],
        'is_gestor_self'  => true
    ]);
}

// 5. Para cada participante, busca nota máxima em prova se houver
$somaProgresso = 0;
$somaNotas = 0;
$participantesComNota = 0;
$concluidos = 0;

foreach ($participantes as &$p) {
    // Progresso e conclusão
    $somaProgresso += (int)$p['progresso_total'];
    if ((int)$p['concluido'] === 1) {
        $concluidos++;
    }

    // Busca nota do exame
    $stmtNota = $db->prepare('
        SELECT MAX(qr.nota) AS nota_maxima
        FROM quiz_resposta qr
        JOIN aulas a ON qr.aula_id = a.id
        WHERE qr.matricula_id = ? AND a.e_prova = 1
    ');
    $stmtNota->execute([$p['matricula_id']]);
    $notaVal = $stmtNota->fetchColumn();

    if ($notaVal !== null) {
        $p['nota_exame'] = (int)$notaVal;
        $somaNotas += (int)$notaVal;
        $participantesComNota++;
    } else {
        $p['nota_exame'] = null;
    }
}

$qtdParticipantes = count($participantes);
$mediaProgress = $qtdParticipantes > 0 ? round($somaProgresso / $qtdParticipantes, 1) : 0;
$mediaNotas = $participantesComNota > 0 ? round($somaNotas / $participantesComNota, 1) : null;

jsonOk([
    'curso_titulo'          => $contract['curso_titulo'],
    'carga_horaria'         => $contract['carga_horaria_horas'],
    'data_compra'           => $contract['data_compra'],
    'vagas_totais'          => (int)$contract['vagas_totais'],
    'vagas_usadas'          => (int)$contract['vagas_usadas'],
    'participantes_total'   => $qtdParticipantes,
    'participantes_concluidos' => $concluidos,
    'media_progresso'       => $mediaProgress,
    'media_nota'            => $mediaNotas,
    'alunos'                => $participantes
]);
