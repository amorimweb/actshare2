<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

$gestor = requireMasterOrAdmin();
$cursoId = (int)($GLOBALS['_ROUTE']['id'] ?? 0);

if (!$cursoId) {
    jsonError('ID do curso inválido.', 400);
}

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // 1. Busca a organização do gestor
    $stmt = $db->prepare('SELECT id FROM organizacoes WHERE gestor_id = ? AND ativo = 1 LIMIT 1');
    $stmt->execute([$gestor['id']]);
    $org = $stmt->fetch();
    
    $participantes = [];
    
    // 2. Se a organização existe, busca os membros matriculados no curso
    if ($org) {
        $stmt = $db->prepare('
            SELECT u.id, u.nome, u.email, u.role, 
                   m.id AS matricula_id, m.progresso_total, m.concluido, m.data_inicio, m.data_fim_acesso
            FROM membros_organizacao mo
            JOIN usuarios u ON mo.usuario_id = u.id
            JOIN matriculas m ON u.id = m.aluno_id
            WHERE mo.organizacao_id = ? AND m.curso_id = ?
            ORDER BY u.nome
        ');
        $stmt->execute([$org['id'], $cursoId]);
        $participantes = $stmt->fetchAll();
    }
    
    // 3. Verifica se o próprio gestor está participando do curso
    $stmt = $db->prepare('
        SELECT m.id AS matricula_id, m.progresso_total, m.concluido, m.data_inicio, m.data_fim_acesso, m.participante
        FROM matriculas m
        WHERE m.aluno_id = ? AND m.curso_id = ? AND m.vagas_totais > 0
        LIMIT 1
    ');
    $stmt->execute([$gestor['id'], $cursoId]);
    $contract = $stmt->fetch();
    
    if ($contract && $contract['participante'] == 1) {
        // Insere o gestor no topo ou final da lista
        array_unshift($participantes, [
            'id'               => $gestor['id'],
            'nome'             => $gestor['nome'] . ' (Você/Gestor)',
            'email'            => $gestor['email'],
            'role'             => $gestor['role'],
            'matricula_id'     => $contract['matricula_id'],
            'progresso_total'  => $contract['progresso_total'],
            'concluido'        => $contract['concluido'],
            'data_inicio'      => $contract['data_inicio'],
            'data_fim_acesso'  => $contract['data_fim_acesso'],
            'is_gestor_self'   => true
        ]);
    }
    
    jsonOk($participantes);

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Busca contrato B2B do gestor
    $stmt = $db->prepare('SELECT id, vagas_totais, vagas_usadas, created_at, com_prova FROM matriculas WHERE aluno_id = ? AND curso_id = ? AND vagas_totais > 0 LIMIT 1');
    $stmt->execute([$gestor['id'], $cursoId]);
    $contract = $stmt->fetch();
    
    if (!$contract) {
        jsonError('Você não possui contrato B2B para este curso.', 404);
    }
    
    // 2. Valida limite de vagas
    if ($contract['vagas_usadas'] >= $contract['vagas_totais']) {
        jsonError('Limite de vagas deste curso foi atingido.', 400);
    }
    
    // 3. Valida período de alocação de 45 dias
    $daysDiff = (time() - strtotime($contract['created_at'])) / (3600 * 24);
    if ($daysDiff > 45) {
        jsonError('O prazo limite de 45 dias para alocar vagas deste contrato expirou.', 400);
    }
    
    $body = json_decode(file_get_contents('php://input'), true) ?? [];
    $email = trim($body['email'] ?? '');
    
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonError('Um endereço de e-mail válido é obrigatório.', 400);
    }
    
    // Busca prazo padrão do curso
    $stmt = $db->prepare('SELECT prazo_conclusao_dias FROM cursos WHERE id = ? LIMIT 1');
    $stmt->execute([$cursoId]);
    $curso = $stmt->fetch();
    $prazoDias = $curso ? (int)$curso['prazo_conclusao_dias'] : 180;
    if (!$prazoDias) $prazoDias = 180;
    
    $db->beginTransaction();
    try {
        // 4. Busca ou cria usuário aluno
        $stmt = $db->prepare('SELECT id FROM usuarios WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $aluno = $stmt->fetch();
        
        if (!$aluno) {
            $defaultPassword = password_hash('actshare123', PASSWORD_DEFAULT);
            $stmt = $db->prepare('INSERT INTO usuarios (nome, email, senha_hash, role) VALUES (?, ?, ?, "aluno")');
            $stmt->execute(['Participante', $email, $defaultPassword]);
            $alunoId = (int)$db->lastInsertId();
        } else {
            $alunoId = (int)$aluno['id'];
        }
        
        // 5. Obtém ou cria organização do gestor
        $stmt = $db->prepare('SELECT id FROM organizacoes WHERE gestor_id = ? AND ativo = 1 LIMIT 1');
        $stmt->execute([$gestor['id']]);
        $org = $stmt->fetch();
        
        if (!$org) {
            $stmt = $db->prepare('INSERT INTO organizacoes (gestor_id, ativo) VALUES (?, 1)');
            $stmt->execute([$gestor['id']]);
            $orgId = (int)$db->lastInsertId();
        } else {
            $orgId = (int)$org['id'];
        }
        
        // 6. Vincula aluno à organização se necessário
        $stmt = $db->prepare('SELECT id FROM membros_organizacao WHERE organizacao_id = ? AND usuario_id = ? LIMIT 1');
        $stmt->execute([$orgId, $alunoId]);
        if (!$stmt->fetch()) {
            $stmt = $db->prepare('INSERT INTO membros_organizacao (organizacao_id, usuario_id) VALUES (?, ?)');
            $stmt->execute([$orgId, $alunoId]);
        }
        
        // 7. Verifica se aluno já está matriculado neste curso
        $stmt = $db->prepare('SELECT id FROM matriculas WHERE aluno_id = ? AND curso_id = ? LIMIT 1');
        $stmt->execute([$alunoId, $cursoId]);
        if ($stmt->fetch()) {
            jsonError('Este participante já está matriculado neste curso.', 400);
        }
        
        // 8. Cria matrícula do aluno
        $dataFimAcesso = date('Y-m-d H:i:s', time() + ($prazoDias * 24 * 3600));
        $stmt = $db->prepare('
            INSERT INTO matriculas (aluno_id, curso_id, data_fim_acesso, vagas_usadas, vagas_totais, com_prova)
            VALUES (?, ?, ?, 1, NULL, ?)
        ');
        $stmt->execute([$alunoId, $cursoId, $dataFimAcesso, (int)$contract['com_prova']]);
        
        // 9. Incrementa vagas_usadas do gestor
        $stmt = $db->prepare('UPDATE matriculas SET vagas_usadas = vagas_usadas + 1 WHERE id = ?');
        $stmt->execute([$contract['id']]);
        
        $db->commit();
        
        jsonOk(['success' => true, 'message' => 'Participante adicionado com sucesso!']);
    } catch (Exception $e) {
        $db->rollBack();
        jsonError($e->getMessage(), 500);
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $alunoId = (int)($GLOBALS['_ROUTE']['aluno_id'] ?? 0);
    if (!$alunoId) {
        jsonError('ID do aluno inválido.', 400);
    }
    
    // Gestor não pode excluir a si mesmo dessa forma
    if ($alunoId === (int)$gestor['id']) {
        jsonError('Operação não permitida.', 400);
    }

    // 1. Busca matrícula do aluno
    $stmt = $db->prepare('SELECT id, progresso_total FROM matriculas WHERE aluno_id = ? AND curso_id = ? LIMIT 1');
    $stmt->execute([$alunoId, $cursoId]);
    $matricula = $stmt->fetch();
    
    if (!$matricula) {
        jsonError('Matrícula do aluno não encontrada.', 404);
    }
    
    // 2. Valida progresso zero
    if ($matricula['progresso_total'] > 0) {
        jsonError('Não é possível remover participantes que já iniciaram o curso (progresso > 0%).', 400);
    }
    
    // 3. Busca contrato B2B do gestor
    $stmt = $db->prepare('SELECT id, vagas_usadas FROM matriculas WHERE aluno_id = ? AND curso_id = ? AND vagas_totais > 0 LIMIT 1');
    $stmt->execute([$gestor['id'], $cursoId]);
    $contract = $stmt->fetch();
    
    if (!$contract) {
        jsonError('Contrato B2B do gestor não encontrado.', 404);
    }
    
    $db->beginTransaction();
    try {
        // 4. Deleta matrícula do aluno
        $stmt = $db->prepare('DELETE FROM matriculas WHERE id = ?');
        $stmt->execute([$matricula['id']]);
        
        // 5. Decrementa vagas_usadas do gestor
        $stmt = $db->prepare('UPDATE matriculas SET vagas_usadas = GREATEST(0, vagas_usadas - 1) WHERE id = ?');
        $stmt->execute([$contract['id']]);
        
        $db->commit();
        jsonOk(['success' => true, 'message' => 'Participante removido com sucesso!']);
    } catch (Exception $e) {
        $db->rollBack();
        jsonError($e->getMessage(), 500);
    }
} else {
    methodNotAllowed();
}
