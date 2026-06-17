<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') methodNotAllowed();

$user = getAuthUser();
if (!$user) jsonOk(['user' => null]);

$stmt = getDB()->prepare('
    SELECT id, email, role, nome, documento, telefone, tipo_pessoa, data_nascimento,
           razao_social, inscricao_estadual, cep, endereco, numero, complemento, bairro, cidade, estado, pais
    FROM usuarios
    WHERE id = ? AND ativo = 1
    LIMIT 1
');
$stmt->execute([$user['id']]);
$freshUser = $stmt->fetch();

jsonOk(['user' => $freshUser ?: null]);
