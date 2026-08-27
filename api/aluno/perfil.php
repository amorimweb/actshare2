<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/jwt.php';
require_once __DIR__ . '/../../includes/auth.php';

$user = requireAuth();
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $db->prepare('
        SELECT id, nome, email, role, created_at, documento, telefone, tipo_pessoa, data_nascimento,
               razao_social, inscricao_estadual, cep, endereco, numero, complemento, bairro, cidade, estado, pais
        FROM usuarios WHERE id = ? LIMIT 1
    ');
    $stmt->execute([$user['id']]);
    $dbUser = $stmt->fetch();

    if (!$dbUser) {
        jsonError('Usuário não encontrado.', 404);
    }

    jsonOk($dbUser);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true) ?? [];
    $nome  = trim($body['nome'] ?? '');
    $email = trim($body['email'] ?? '');

    if (!$nome || !$email) {
        jsonError('Nome e E-mail são campos obrigatórios.', 400);
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonError('E-mail inválido.', 400);
    }

    // Verifica se e-mail já existe em outro usuário
    $stmt = $db->prepare('SELECT id FROM usuarios WHERE email = ? AND id != ? LIMIT 1');
    $stmt->execute([$email, $user['id']]);
    if ($stmt->fetch()) {
        jsonError('Este e-mail já está sendo utilizado por outro usuário.', 400);
    }

    // Campos cadastrais/fiscais — já existiam na tabela e no cadastro inicial
    // (views/registro.php), mas até agora não podiam ser revistos depois.
    $camposOpcionais = [
        'documento', 'telefone', 'tipo_pessoa', 'data_nascimento', 'razao_social',
        'inscricao_estadual', 'cep', 'endereco', 'numero', 'complemento', 'bairro', 'cidade', 'estado',
    ];
    $sets = ['nome = ?', 'email = ?'];
    $params = [$nome, $email];
    foreach ($camposOpcionais as $campo) {
        if (array_key_exists($campo, $body)) {
            $sets[] = "$campo = ?";
            $params[] = trim((string)$body[$campo]) ?: null;
        }
    }
    $params[] = $user['id'];

    // Atualiza
    $stmt = $db->prepare('UPDATE usuarios SET ' . implode(', ', $sets) . ' WHERE id = ?');
    $stmt->execute($params);

    // Busca dados atualizados para atualizar o token/cookie
    $stmt = $db->prepare('SELECT id, nome, email, role FROM usuarios WHERE id = ? LIMIT 1');
    $stmt->execute([$user['id']]);
    $updatedUser = $stmt->fetch();

    $newToken = signToken($updatedUser);
    setAuthCookie($newToken);

    jsonOk([
        'success' => true,
        'user' => [
            'id'    => $updatedUser['id'],
            'email' => $updatedUser['email'],
            'role'  => $updatedUser['role'],
            'nome'  => $updatedUser['nome'],
        ]
    ]);
} else {
    methodNotAllowed();
}
