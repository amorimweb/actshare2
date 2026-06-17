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
        'exibir_instrutor'
    ];

    $set    = [];
    $params = [];
    foreach ($fields as $f) {
        if (array_key_exists($f, $body)) {
            $set[]    = "$f = ?";
            $params[] = $body[$f];
        }
    }
    if (!$set) jsonError('Nenhum campo para atualizar.', 400);

    $params[] = $id;
    getDB()->prepare('UPDATE cursos SET ' . implode(', ', $set) . ' WHERE id = ?')->execute($params);

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
