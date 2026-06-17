<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

$method = $_SERVER['REQUEST_METHOD'];

// GET — lista pública com filtros opcionais
if ($method === 'GET') {
    $db = getDB();
    $conditions = [];
    $params     = [];

    if (($_GET['ativo'] ?? '') === 'true')   { $conditions[] = 'c.ativo = 1'; }
    if (($_GET['publico'] ?? '') === 'true')  { $conditions[] = 'c.publico = 1'; }
    if (!empty($_GET['categoria'])) {
        if (ctype_digit((string) $_GET['categoria'])) {
            $conditions[] = 'c.categoria_id = ?';
        } else {
            $conditions[] = 'cat.slug = ?';
        }
        $params[] = $_GET['categoria'];
    }

    $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
    $stmt  = $db->prepare("
        SELECT c.*, cat.nome AS categoria_nome, cat.slug AS categoria_slug, i.nome AS instrutor_nome
        FROM cursos c
        LEFT JOIN categorias cat ON c.categoria_id = cat.id
        LEFT JOIN instrutores i  ON c.instrutor_id  = i.id
        $where
        ORDER BY c.created_at DESC
    ");
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    $result = array_map(fn($r) => array_merge($r, [
        'categoria' => $r['categoria_nome'] ? ['nome' => $r['categoria_nome'], 'slug' => $r['categoria_slug']] : null,
        'instrutor' => $r['instrutor_nome']  ? ['nome' => $r['instrutor_nome']]  : null,
    ]), $rows);

    jsonOk($result);
}

// POST — cria curso (admin)
if ($method === 'POST') {
    requireAdmin();
    $body = json_decode(file_get_contents('php://input'), true) ?? [];

    $db   = getDB();
    $stmt = $db->prepare('
        INSERT INTO cursos (titulo, nome_certificado, codigo, descricao, thumb_url, ativo, publico, categoria_id, instrutor_id, preco, carga_horaria_horas, prazo_acesso_dias, disponivel_loja, certificado_template_url, certificado_config, certificado_liberacao, exibir_instrutor)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');
    $stmt->execute([
        $body['titulo']                  ?? '',
        $body['nome_certificado']        ?? null,
        $body['codigo']                  ?? null,
        $body['descricao']               ?? null,
        $body['thumb_url']               ?? null,
        $body['ativo']                   ?? 1,
        $body['publico']                 ?? 0,
        $body['categoria_id']            ?? null,
        $body['instrutor_id']            ?? null,
        $body['preco']                   ?? 0,
        $body['carga_horaria_horas']     ?? 0,
        $body['prazo_acesso_dias']       ?? null,
        $body['disponivel_loja']         ?? 1,
        $body['certificado_template_url']?? null,
        $body['certificado_config']      ?? null,
        $body['certificado_liberacao']   ?? 'ambos',
        $body['exibir_instrutor']        ?? 0,
    ]);

    $id   = $db->lastInsertId();
    $stmt = $db->prepare('SELECT * FROM cursos WHERE id = ?');
    $stmt->execute([$id]);
    jsonOk($stmt->fetch(), 201);
}

methodNotAllowed();
