<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAdmin();
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // "Cliente" é quem paga: todo usuário que já fez pelo menos um pedido, é
    // o gestor titular de uma organização (contrato B2B), ou foi cadastrado
    // manualmente pelo Admin (ainda sem pedido/organização).
    $stmt = $db->query('
        SELECT u.id, u.nome, u.email, u.documento, u.tipo_pessoa, u.razao_social,
               u.inscricao_estadual, u.telefone, u.cidade, u.estado, u.observacao_admin,
               u.created_at,
               o.id AS organizacao_id, o.certificado_acesso,
               (SELECT COUNT(*) FROM pedidos WHERE usuario_id = u.id) AS total_pedidos
        FROM usuarios u
        LEFT JOIN organizacoes o ON o.gestor_id = u.id
        WHERE u.id IN (SELECT DISTINCT usuario_id FROM pedidos)
           OR u.id IN (SELECT DISTINCT gestor_id FROM organizacoes)
           OR u.cliente_manual = 1
        ORDER BY u.nome
    ');
    $clientes = $stmt->fetchAll();

    jsonOk($clientes);
}

// POST — cadastro manual de cliente pelo Admin (item 7 do escopo). Cria um
// usuário com senha temporária padrão, igual ao fluxo já existente de
// alocação de participante pelo Gestor (api/master/participantes.php).
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true) ?? [];

    $nome  = trim($body['nome'] ?? '');
    $email = trim($body['email'] ?? '');
    if (!$nome) jsonError('Nome é obrigatório.', 400);
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) jsonError('Um e-mail válido é obrigatório.', 400);

    $stmt = $db->prepare('SELECT id FROM usuarios WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    if ($stmt->fetch()) jsonError('Já existe um usuário cadastrado com esse e-mail.', 400);

    $tipoPessoa = in_array($body['tipo_pessoa'] ?? '', ['fisica', 'juridica'], true) ? $body['tipo_pessoa'] : null;
    $senhaTemp  = password_hash('actshare123', PASSWORD_DEFAULT);

    $stmt = $db->prepare('
        INSERT INTO usuarios (
            nome, email, senha_hash, role, tipo_pessoa, documento, razao_social,
            inscricao_estadual, telefone, cep, endereco, numero, bairro, cidade,
            estado, observacao_admin, cliente_manual
        ) VALUES (?, ?, ?, "aluno", ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
    ');
    $stmt->execute([
        $nome, $email, $senhaTemp, $tipoPessoa,
        trim($body['documento'] ?? '') ?: null,
        trim($body['razao_social'] ?? '') ?: null,
        trim($body['inscricao_estadual'] ?? '') ?: null,
        trim($body['telefone'] ?? '') ?: null,
        trim($body['cep'] ?? '') ?: null,
        trim($body['endereco'] ?? '') ?: null,
        trim($body['numero'] ?? '') ?: null,
        trim($body['bairro'] ?? '') ?: null,
        trim($body['cidade'] ?? '') ?: null,
        trim($body['estado'] ?? '') ?: null,
        trim($body['observacao_admin'] ?? '') ?: null,
    ]);
    $clienteId = (int)$db->lastInsertId();

    if (!empty($body['certificado_acesso']) && in_array($body['certificado_acesso'], ['empresa', 'aluno', 'ambos'], true)) {
        $db->prepare('INSERT INTO organizacoes (gestor_id, ativo, certificado_acesso) VALUES (?, 1, ?)')
           ->execute([$clienteId, $body['certificado_acesso']]);
    }

    jsonOk(['success' => true, 'id' => $clienteId, 'senha_temporaria' => 'actshare123'], 201);
}

methodNotAllowed();
