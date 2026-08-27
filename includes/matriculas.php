<?php
/**
 * Verifica se o aluno já concluiu todos os pré-requisitos cadastrados para
 * o curso. Retorna [] se não há pendência, ou a lista de cursos pendentes
 * (id + titulo) caso contrário.
 */
function cursosPrerequisitosPendentes(PDO $db, int $alunoId, int $cursoId): array {
    $stmt = $db->prepare('
        SELECT c.id, c.titulo
        FROM curso_prerequisitos cp
        JOIN cursos c ON cp.prerequisito_curso_id = c.id
        WHERE cp.curso_id = ?
          AND NOT EXISTS (
              SELECT 1 FROM matriculas m
              WHERE m.aluno_id = ? AND m.curso_id = cp.prerequisito_curso_id AND m.concluido = 1
          )
    ');
    $stmt->execute([$cursoId, $alunoId]);
    return $stmt->fetchAll();
}

/**
 * Recalcula progresso_total e concluido de uma matrícula e grava no banco.
 *
 * `concluido` só vira 1 quando 100% das aulas foram concluídas E (havendo
 * perguntas cadastradas em pesquisa_perguntas) a pesquisa de satisfação já
 * foi respondida por essa matrícula — o certificado depende de `concluido`,
 * então esse gate é o que garante "certificado só após a pesquisa".
 *
 * Centraliza uma lógica que antes estava duplicada em progresso.php,
 * quiz/responder.php e satisfacao.php.
 */
function recalcularConclusaoMatricula(PDO $db, int $matriculaId): array {
    $stmtMat = $db->prepare('SELECT curso_id, data_conclusao FROM matriculas WHERE id = ? LIMIT 1');
    $stmtMat->execute([$matriculaId]);
    $mat = $stmtMat->fetch();
    if (!$mat) return ['percentual' => 0, 'concluido' => 0, 'aulas_completas' => false, 'pesquisa_ok' => false];

    $cursoId = (int)$mat['curso_id'];

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

    $aulasCompletas = ($percentual >= 100);

    $pesquisaOk = true;
    $totalPerguntasPesquisa = (int)$db->query('SELECT COUNT(*) FROM pesquisa_perguntas')->fetchColumn();
    if ($totalPerguntasPesquisa > 0) {
        $stmtRespondidas = $db->prepare('SELECT COUNT(*) FROM pesquisa_respostas WHERE matricula_id = ?');
        $stmtRespondidas->execute([$matriculaId]);
        $totalRespondidas = (int)$stmtRespondidas->fetchColumn();
        $pesquisaOk = ($totalRespondidas >= $totalPerguntasPesquisa);
    }

    $concluido = ($aulasCompletas && $pesquisaOk) ? 1 : 0;
    $dataConclusao = $concluido ? ($mat['data_conclusao'] ?: date('Y-m-d H:i:s')) : $mat['data_conclusao'];

    $stmtUpdateMat = $db->prepare('
        UPDATE matriculas
        SET progresso_total = ?, concluido = ?, data_conclusao = ?
        WHERE id = ?
    ');
    $stmtUpdateMat->execute([$percentual, $concluido, $dataConclusao, $matriculaId]);

    return [
        'percentual'      => $percentual,
        'concluido'       => $concluido,
        'aulas_completas' => $aulasCompletas,
        'pesquisa_ok'     => $pesquisaOk,
    ];
}
