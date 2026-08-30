<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAdmin();
$cursoId = (int)($GLOBALS['_ROUTE']['id'] ?? 0);
if (!$cursoId) jsonError('ID do curso inválido.', 400);

$db = getDB();
$tipos = ['AVALIACAO', 'QM', 'AU', 'TL'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $db->prepare('SELECT * FROM exames_curso WHERE curso_id = ?');
    $stmt->execute([$cursoId]);
    $existentes = [];
    foreach ($stmt->fetchAll() as $row) {
        $existentes[$row['tipo']] = $row;
    }
    $out = [];
    foreach ($tipos as $tipo) {
        $out[] = $existentes[$tipo] ?? [
            'id' => null, 'tipo' => $tipo, 'nome' => null, 'preco' => 0, 'ativo' => 0,
            'prazo_dias' => 180, 'numero_questoes' => 10, 'nota_corte_tipo' => 'percentual',
            'nota_corte_valor' => 70, 'tempo_limite_minutos' => 60,
        ];
    }
    jsonOk($out);
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $body = json_decode(file_get_contents('php://input'), true) ?? [];
    $exames = $body['exames'] ?? [];

    $stmt = $db->prepare('
        INSERT INTO exames_curso (curso_id, tipo, nome, preco, ativo, prazo_dias, numero_questoes, nota_corte_tipo, nota_corte_valor, tempo_limite_minutos)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE nome = VALUES(nome), preco = VALUES(preco), ativo = VALUES(ativo),
            prazo_dias = VALUES(prazo_dias), numero_questoes = VALUES(numero_questoes),
            nota_corte_tipo = VALUES(nota_corte_tipo), nota_corte_valor = VALUES(nota_corte_valor),
            tempo_limite_minutos = VALUES(tempo_limite_minutos)
    ');
    foreach ($exames as $ex) {
        $tipo = strtoupper(trim($ex['tipo'] ?? ''));
        if (!in_array($tipo, $tipos)) continue;
        $stmt->execute([
            $cursoId, $tipo, trim($ex['nome'] ?? '') ?: null, (float)($ex['preco'] ?? 0), !empty($ex['ativo']) ? 1 : 0,
            (int)($ex['prazo_dias'] ?? 180), (int)($ex['numero_questoes'] ?? 10),
            in_array($ex['nota_corte_tipo'] ?? '', ['questoes', 'percentual']) ? $ex['nota_corte_tipo'] : 'percentual',
            (int)($ex['nota_corte_valor'] ?? 70), (int)($ex['tempo_limite_minutos'] ?? 60),
        ]);
    }

    $stmt2 = $db->prepare('SELECT id, tipo FROM exames_curso WHERE curso_id = ?');
    $stmt2->execute([$cursoId]);
    jsonOk(['success' => true, 'exames' => $stmt2->fetchAll()]);
}

methodNotAllowed();
