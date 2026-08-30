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

    // Exames Exemplar Global ativos, agrupados por curso
    $examesPorCurso = [];
    if ($rows) {
        $ids = array_column($rows, 'id');
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmtEx = $db->prepare("SELECT curso_id, tipo, preco FROM exames_curso WHERE ativo = 1 AND curso_id IN ($placeholders)");
        $stmtEx->execute($ids);
        foreach ($stmtEx->fetchAll() as $ex) {
            $examesPorCurso[$ex['curso_id']][] = ['tipo' => $ex['tipo'], 'preco' => $ex['preco']];
        }
    }

    $result = array_map(fn($r) => array_merge($r, [
        'categoria' => $r['categoria_nome'] ? ['nome' => $r['categoria_nome'], 'slug' => $r['categoria_slug']] : null,
        'instrutor' => $r['instrutor_nome']  ? ['nome' => $r['instrutor_nome']]  : null,
        'exames'    => $examesPorCurso[$r['id']] ?? [],
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
    // Todo curso precisa de um código: o certificado usa [CODIGO]-[ID] como
    // código de autenticidade, e sem ele a validação pública nunca encontra
    // o certificado. Se o admin não informar um, gera um a partir do próximo id.
    $codigo = trim($body['codigo'] ?? '') ?: null;
    if (!$codigo) {
        $proximoId = (int)$db->query('SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = "cursos"')->fetchColumn();
        $codigo = 'C' . str_pad((string)$proximoId, 6, '0', STR_PAD_LEFT);
    }

    $stmt->execute([
        $body['titulo']                  ?? '',
        $body['nome_certificado']        ?? null,
        $codigo,
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
