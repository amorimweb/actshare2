<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') methodNotAllowed();

$user = requireAuth();
$body = json_decode(file_get_contents('php://input'), true) ?? [];

$pedidoId = (int)($body['pedido_id'] ?? 0);

if (!$pedidoId) {
    jsonError('pedido_id é obrigatório.', 400);
}

$db = getDB();

// 1. Busca o pedido para verificar se pertence ao usuário ou se é admin
$stmt = $db->prepare('SELECT * FROM pedidos WHERE id = ? AND (usuario_id = ? OR ? = "admin") LIMIT 1');
$stmt->execute([$pedidoId, $user['id'], $user['role']]);
$pedido = $stmt->fetch();

if (!$pedido) {
    jsonError('Pedido não encontrado.', 404);
}

if ($pedido['situacao'] === 'pago') {
    jsonOk(['success' => true, 'message' => 'Este pedido já está pago e as matrículas já foram liberadas.']);
}

$db->beginTransaction();
try {
    // 2. Atualiza situação do pedido
    $stmt = $db->prepare('UPDATE pedidos SET situacao = "pago" WHERE id = ?');
    $stmt->execute([$pedidoId]);
    
    // 3. Busca itens do pedido
    $stmt = $db->prepare('SELECT * FROM itens_pedido WHERE pedido_id = ?');
    $stmt->execute([$pedidoId]);
    $itens = $stmt->fetchAll();
    
    foreach ($itens as $item) {
        $cursoId = (int)$item['curso_id'];
        $vagas = (int)$item['vagas'];
        
        // Busca prazo padrão do curso
        $stmtC = $db->prepare('SELECT prazo_conclusao_dias FROM cursos WHERE id = ? LIMIT 1');
        $stmtC->execute([$cursoId]);
        $curso = $stmtC->fetch();
        $prazoDias = $curso ? (int)$curso['prazo_conclusao_dias'] : 180;
        if (!$prazoDias) $prazoDias = 180;
        
        $dataFimAcesso = date('Y-m-d H:i:s', time() + ($prazoDias * 24 * 3600));

        $comProva  = (int)$item['com_prova'];

        if ($vagas === 1) {
            // A. Fluxo B2C / Aluno Individual
            // Cria matrícula do aluno normal
            $stmtM = $db->prepare('
                INSERT INTO matriculas (aluno_id, curso_id, data_fim_acesso, vagas_usadas, vagas_totais, participante, com_prova)
                VALUES (?, ?, ?, 1, NULL, 0, ?)
                ON DUPLICATE KEY UPDATE data_fim_acesso = VALUES(data_fim_acesso), com_prova = VALUES(com_prova)
            ');
            $stmtM->execute([$pedido['usuario_id'], $cursoId, $dataFimAcesso, $comProva]);
            
            // Gera cupom de indicação B2C (10% de desconto por 30 dias)
            $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            $codigoRef = 'REF-' . substr(str_shuffle($caracteres), 0, 6);
            $validadeCupom = date('Y-m-d H:i:s', time() + (30 * 24 * 3600));
            
            $stmtR = $db->prepare('
                INSERT INTO cupons_indicacao (indicador_id, codigo, percentual, validade, utilizado)
                VALUES (?, ?, 10, ?, 0)
            ');
            $stmtR->execute([$pedido['usuario_id'], $codigoRef, $validadeCupom]);
            
        } else {
            // B. Fluxo B2B / Compra Corporativa (Gestor)
            // Cria matrícula principal do gestor contendo a contagem total de vagas
            $stmtM = $db->prepare('
                INSERT INTO matriculas (aluno_id, curso_id, data_fim_acesso, vagas_usadas, vagas_totais, participante, com_prova)
                VALUES (?, ?, ?, 0, ?, 0, ?)
                ON DUPLICATE KEY UPDATE vagas_totais = VALUES(vagas_totais), data_fim_acesso = VALUES(data_fim_acesso), com_prova = VALUES(com_prova)
            ');
            $stmtM->execute([$pedido['usuario_id'], $cursoId, $dataFimAcesso, $vagas, $comProva]);
            
            // Promove a role do usuário comprador para 'gestor' caso seja 'aluno'
            $stmtU = $db->prepare('UPDATE usuarios SET role = "gestor" WHERE id = ? AND role = "aluno"');
            $stmtU->execute([$pedido['usuario_id']]);
        }
    }
    
    $db->commit();
    
    // Atualiza local storage no front-end caso a role do usuário logado tenha mudado para gestor
    $stmt = $db->prepare('SELECT id, nome, email, role FROM usuarios WHERE id = ? LIMIT 1');
    $stmt->execute([$pedido['usuario_id']]);
    $updatedUser = $stmt->fetch();
    
    jsonOk([
        'success' => true,
        'message' => 'Pagamento simulado com sucesso! Matrículas liberadas.',
        'updated_user' => $updatedUser
    ]);
} catch (Exception $e) {
    $db->rollBack();
    jsonError($e->getMessage(), 500);
}
