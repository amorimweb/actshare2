<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

$gestor = requireMasterOrAdmin();
$cursoId = (int)($GLOBALS['_ROUTE']['id'] ?? 0);
if (!$cursoId) {
    jsonError('ID do curso inválido.', 400);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    methodNotAllowed();
}

$db = getDB();
$context = getGestorContext($gestor, $db);
$orgId = $context['org_id'];

if (!$orgId) {
    jsonOk(['enviados' => 0, 'total' => 0]);
}

$stmt = $db->prepare('
    SELECT u.email, u.nome, c.titulo AS curso_titulo
    FROM membros_organizacao mo
    JOIN usuarios u ON mo.usuario_id = u.id
    JOIN matriculas m ON u.id = m.aluno_id
    JOIN cursos c ON c.id = m.curso_id
    WHERE mo.organizacao_id = ? AND m.curso_id = ?
');
$stmt->execute([$orgId, $cursoId]);
$alunos = $stmt->fetchAll();

$total = count($alunos);
$enviados = 0;

foreach ($alunos as $aluno) {
    $assunto = 'Seu treinamento já está disponível: ' . $aluno['curso_titulo'];
    $corpo = "Olá, " . $aluno['nome'] . "!\r\n\r\n"
        . "O treinamento \"" . $aluno['curso_titulo'] . "\" já está disponível na plataforma ActShare.\r\n"
        . "Acesse sua Área do Aluno para começar.\r\n\r\nEquipe ActShare";
    $headers = "From: ActShare <no-reply@actshare.com.br>\r\nContent-Type: text/plain; charset=UTF-8";

    // Usa o mail() nativo do PHP — depende de SMTP configurado no servidor de
    // produção (igual à integração ASAAS: funciona de verdade assim que o
    // ambiente tiver um transporte de e-mail configurado).
    if (@mail($aluno['email'], $assunto, $corpo, $headers)) {
        $enviados++;
    }
}

jsonOk(['enviados' => $enviados, 'total' => $total]);
