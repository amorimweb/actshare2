<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

$gestor = requireMasterOrAdmin();
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true) ?? [];

    $nome           = trim($body['nome']           ?? '');
    $email          = trim($body['email']          ?? '');
    $password       = $body['password']       ?? '';
    $role           = trim($body['role']           ?? 'aluno');
    $isParticipante = (bool)($body['is_participante'] ?? false);

    if (!$nome || !$email || !$password) {
        jsonError('nome, email e password são obrigatórios.', 400);
    }

    if (!in_array($role, ['aluno', 'gestor'])) {
        $role = 'aluno';
    }

    // Verifica e-mail duplicado
    $stmt = $db->prepare('SELECT id FROM usuarios WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        jsonError('Este e-mail já está cadastrado.', 400);
    }

    $db->beginTransaction();
    try {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
        $stmt = $db->prepare('INSERT INTO usuarios (nome, email, senha_hash, role, criado_por_id) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$nome, $email, $hash, $role, $gestor['id']]);
        $novoId = (int)$db->lastInsertId();

        $context = getGestorContext($gestor, $db);
        $mainGestorId = $context['id'];
        $orgId = $context['org_id'];

        if (!$orgId) {
            $stmt = $db->prepare('INSERT INTO organizacoes (gestor_id, ativo) VALUES (?, 1)');
            $stmt->execute([$mainGestorId]);
            $orgId = (int)$db->lastInsertId();
        }

        $stmt = $db->prepare('INSERT INTO membros_organizacao (organizacao_id, usuario_id) VALUES (?, ?)');
        $stmt->execute([$orgId, $novoId]);

        // Se is_participante for verdadeiro, auto-matricula nos cursos B2B com vagas disponíveis
        if ($isParticipante) {
            // Busca todos os contratos B2B ativos do gestor com vagas disponíveis
            $stmt = $db->prepare('
                SELECT m.id, m.curso_id, m.vagas_totais, m.vagas_usadas, m.com_prova 
                FROM matriculas m
                WHERE m.aluno_id = ? AND m.vagas_totais > 0 AND m.vagas_usadas < m.vagas_totais
            ');
            $stmt->execute([$mainGestorId]);
            $contratos = $stmt->fetchAll();

            foreach ($contratos as $contract) {
                // Busca prazo padrão do curso
                $stmtCurso = $db->prepare('SELECT prazo_conclusao_dias FROM cursos WHERE id = ? LIMIT 1');
                $stmtCurso->execute([$contract['curso_id']]);
                $curso = $stmtCurso->fetch();
                $prazoDias = $curso ? (int)$curso['prazo_conclusao_dias'] : 180;
                if (!$prazoDias) $prazoDias = 180;

                $dataFimAcesso = date('Y-m-d H:i:s', time() + ($prazoDias * 24 * 3600));

                // Cria matrícula do novo participante
                $stmtMat = $db->prepare('
                    INSERT INTO matriculas (aluno_id, curso_id, data_fim_acesso, vagas_usadas, vagas_totais, com_prova)
                    VALUES (?, ?, ?, 1, NULL, ?)
                ');
                $stmtMat->execute([$novoId, $contract['curso_id'], $dataFimAcesso, (int)$contract['com_prova']]);

                // Incrementa vagas_usadas no contrato do gestor
                $stmtInc = $db->prepare('UPDATE matriculas SET vagas_usadas = vagas_usadas + 1 WHERE id = ?');
                $stmtInc->execute([$contract['id']]);
            }
        }

        $db->commit();

        $stmt = $db->prepare('SELECT id, nome, email, role FROM usuarios WHERE id = ?');
        $stmt->execute([$novoId]);
        jsonOk($stmt->fetch(), 201);
    } catch (Exception $e) {
        $db->rollBack();
        jsonError($e->getMessage(), 500);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = (int)($GLOBALS['_ROUTE']['id'] ?? 0);
    if (!$id) {
        jsonError('ID do usuário inválido.', 400);
    }
    
    // Gestor não pode excluir a si mesmo
    if ($id === (int)$gestor['id']) {
        jsonError('Operação não permitida.', 400);
    }
    
    $context = getGestorContext($gestor, $db);
    $orgId = $context['org_id'];
    if (!$orgId) {
        jsonError('Organização não encontrada.', 404);
    }
    $org = ['id' => $orgId];
    
    $db->beginTransaction();
    try {
        // Remove da organização
        $stmt = $db->prepare('DELETE FROM membros_organizacao WHERE usuario_id = ? AND organizacao_id = ?');
        $stmt->execute([$id, $org['id']]);
        
        // Remove da tabela de usuários
        $stmt = $db->prepare('DELETE FROM usuarios WHERE id = ?');
        $stmt->execute([$id]);
        
        $db->commit();
        jsonOk(['success' => true]);
    } catch (Exception $e) {
        $db->rollBack();
        jsonError($e->getMessage(), 500);
    }
}

methodNotAllowed();
