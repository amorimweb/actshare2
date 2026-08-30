<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$id     = (int)($GLOBALS['_ROUTE']['id'] ?? 0);
if (!$id) jsonError('ID inválido.', 400);

// GET — detalhes públicos do curso com módulos e aulas
if ($method === 'GET') {
    $db   = getDB();
    $stmt = $db->prepare('
        SELECT c.*, cat.nome AS categoria_nome, cat.slug AS categoria_slug,
               i.nome AS instrutor_nome, i.qualificacao1, i.qualificacao2, i.avatar_url, i.assinatura_url
        FROM cursos c
        LEFT JOIN categorias cat ON c.categoria_id = cat.id
        LEFT JOIN instrutores i  ON c.instrutor_id  = i.id
        WHERE c.id = ?
    ');
    $stmt->execute([$id]);
    $curso = $stmt->fetch();
    if (!$curso) jsonError('Curso não encontrado.', 404);

    // Módulos
    $stmt = $db->prepare('SELECT * FROM modulos WHERE curso_id = ? ORDER BY ordem');
    $stmt->execute([$id]);
    $modulos = $stmt->fetchAll();

    foreach ($modulos as &$mod) {
        $stmt2 = $db->prepare('SELECT * FROM aulas WHERE modulo_id = ? ORDER BY ordem');
        $stmt2->execute([$mod['id']]);
        $mod['aulas'] = $stmt2->fetchAll();
    }

    $curso['categoria']  = $curso['categoria_nome'] ? ['nome' => $curso['categoria_nome'], 'slug' => $curso['categoria_slug']] : null;
    $curso['instrutor']  = $curso['instrutor_nome']  ? [
        'nome'           => $curso['instrutor_nome'],
        'qualificacao1'  => $curso['qualificacao1'],
        'qualificacao2'  => $curso['qualificacao2'],
        'avatar_url'     => $curso['avatar_url'],
        'assinatura_url' => $curso['assinatura_url'],
    ] : null;
    $curso['modulos'] = $modulos;

    // Pré-requisitos: cursos que precisam ser concluídos antes deste
    $stmtPre = $db->prepare('
        SELECT cp.prerequisito_curso_id AS id, c2.titulo
        FROM curso_prerequisitos cp
        JOIN cursos c2 ON cp.prerequisito_curso_id = c2.id
        WHERE cp.curso_id = ?
    ');
    $stmtPre->execute([$id]);
    $curso['prerequisitos'] = $stmtPre->fetchAll();

    // Exame Exemplar Global (QM/AU/TL) disponíveis para este curso
    $stmtEx = $db->prepare('SELECT id, tipo, preco, ativo FROM exames_curso WHERE curso_id = ? AND ativo = 1');
    $stmtEx->execute([$id]);
    $curso['exames'] = $stmtEx->fetchAll();

    jsonOk($curso);
}

// PUT — atualiza curso (admin)
if ($method === 'PUT') {
    requireAdmin();
    $body   = json_decode(file_get_contents('php://input'), true) ?? [];
    $fields = [
        'titulo', 'nome_certificado', 'codigo', 'descricao', 'thumb_url',
        'ativo', 'publico', 'categoria_id', 'instrutor_id', 'preco',
        'carga_horaria_horas', 'prazo_acesso_dias', 'disponivel_loja',
        'certificado_template_url', 'certificado_config', 'certificado_liberacao',
        'exibir_instrutor',
        // Descrição estendida da página do curso (loja) + toggles de visibilidade
        'video_url_explicativo', 'diferencial', 'conteudo_programatico', 'publico_alvo', 'condicoes',
        'vis_nome', 'vis_breve_descricao', 'vis_carga_horaria', 'vis_valor', 'vis_descricao',
        'vis_video', 'vis_diferencial', 'vis_conteudo', 'vis_publico_alvo', 'vis_condicoes',
    ];

    $set    = [];
    $params = [];
    foreach ($fields as $f) {
        if (array_key_exists($f, $body)) {
            $set[]    = "$f = ?";
            $params[] = $body[$f];
        }
    }
    if ($set) {
        $params[] = $id;
        getDB()->prepare('UPDATE cursos SET ' . implode(', ', $set) . ' WHERE id = ?')->execute($params);
    }

    // Pré-requisitos (lista completa substitui a anterior)
    if (array_key_exists('prerequisitos', $body)) {
        $db = getDB();
        $db->prepare('DELETE FROM curso_prerequisitos WHERE curso_id = ?')->execute([$id]);
        $stmtIns = $db->prepare('INSERT IGNORE INTO curso_prerequisitos (curso_id, prerequisito_curso_id) VALUES (?, ?)');
        foreach ((array)$body['prerequisitos'] as $preId) {
            $preId = (int)$preId;
            if ($preId && $preId !== $id) $stmtIns->execute([$id, $preId]);
        }
    }

    if (!$set && !array_key_exists('prerequisitos', $body)) jsonError('Nenhum campo para atualizar.', 400);

    $stmt = getDB()->prepare('SELECT * FROM cursos WHERE id = ?');
    $stmt->execute([$id]);
    jsonOk($stmt->fetch());
}

// DELETE — remove curso (admin)
if ($method === 'DELETE') {
    requireAdmin();
    $db   = getDB();
    $stmt = $db->prepare('SELECT id FROM cursos WHERE id = ?');
    $stmt->execute([$id]);
    if (!$stmt->fetch()) jsonError('Curso não encontrado.', 404);

    $db->prepare('DELETE FROM cursos WHERE id = ?')->execute([$id]);
    jsonOk(['success' => true]);
}

methodNotAllowed();
