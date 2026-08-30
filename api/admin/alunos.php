<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAdmin();
if ($_SERVER['REQUEST_METHOD'] !== 'GET') methodNotAllowed();

$db = getDB();

// Mesma visão do Gestor/Alunos, mas para TODOS os alunos da plataforma —
// uma linha por matrícula (o mesmo aluno aparece 2x se está em 2 cursos).
$stmt = $db->query('
    SELECT
        m.id AS matricula_id, m.aluno_id, m.curso_id, m.progresso_total, m.concluido,
        m.data_inicio, m.data_conclusao, m.data_fim_acesso,
        u.nome AS aluno_nome, u.email AS aluno_email,
        c.titulo AS curso_titulo,
        gestorU.nome AS cliente_nome, gestorU.razao_social AS cliente_razao_social,
        (SELECT qr.aprovado FROM quiz_resposta qr JOIN aulas a ON qr.aula_id = a.id WHERE qr.matricula_id = m.id AND a.e_prova = 1 LIMIT 1) AS exam_aprovado,
        (SELECT COUNT(*) FROM exame_tentativas et WHERE et.matricula_id = m.id) AS provas_realizadas
    FROM matriculas m
    JOIN usuarios u ON u.id = m.aluno_id
    JOIN cursos c ON c.id = m.curso_id
    LEFT JOIN membros_organizacao mo ON mo.usuario_id = u.id
    LEFT JOIN organizacoes org ON org.id = mo.organizacao_id
    LEFT JOIN usuarios gestorU ON gestorU.id = org.gestor_id
    ORDER BY u.nome, c.titulo
');
$linhas = $stmt->fetchAll();

foreach ($linhas as &$l) {
    $l['cliente'] = $l['cliente_razao_social'] ?: $l['cliente_nome'];
    unset($l['cliente_nome'], $l['cliente_razao_social']);
}

jsonOk($linhas);
