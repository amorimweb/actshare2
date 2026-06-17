<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') methodNotAllowed();

$codigo = trim($GLOBALS['_ROUTE']['codigo'] ?? '');
if (empty($codigo)) {
    jsonError('Código do certificado é obrigatório.', 400);
}

$db = getDB();

// 1. Tenta decodificar o código no padrão [CÓDIGO_CURSO]-[SEQ]
$partes = explode('-', $codigo);
if (count($partes) >= 2) {
    $seq = (int)end($partes);
    // Remove o último elemento (seq) para juntar o resto caso o código do curso contenha hífens
    array_pop($partes);
    $cursoCodigo = implode('-', $partes);

    if ($seq > 0 && !empty($cursoCodigo)) {
        // Busca na tabela matriculas
        $stmt = $db->prepare("
            SELECT m.id, m.concluido, m.data_conclusao, 
                   u.nome AS aluno_nome, u.documento AS aluno_documento,
                   c.titulo AS curso_nome, c.carga_horaria_horas, c.nome_certificado,
                   i.nome AS instrutor_nome, i.assinatura_url
            FROM matriculas m
            JOIN usuarios u ON m.aluno_id = u.id
            JOIN cursos c ON m.curso_id = c.id
            LEFT JOIN instrutores i ON c.instrutor_id = i.id
            WHERE m.id = ? AND c.codigo = ? AND m.concluido = 1
            LIMIT 1
        ");
        $stmt->execute([$seq, $cursoCodigo]);
        $cert = $stmt->fetch();

        if ($cert) {
            jsonOk([
                'tipo'                 => 'sistema',
                'cliente_nome'         => $cert['aluno_nome'],
                'documento'            => $cert['aluno_documento'] ?: '—',
                'curso_nome'           => $cert['nome_certificado'] ?: $cert['curso_nome'],
                'carga_horaria'        => (int)$cert['carga_horaria_horas'],
                'data_conclusao'       => $cert['data_conclusao'],
                'tipo_texto'           => 'aprovacao', // Por padrão, os concluidos no sistema são certificados de aprovação/conclusão
                'instrutor_nome'       => $cert['instrutor_nome'] ?: 'ActShare',
                'assinatura_url'       => $cert['assinatura_url'] ?: '',
                'codigo_autenticidade' => $codigo,
                'valido'               => true
            ]);
        }
    }
}

// 2. Se não localizou, tenta buscar na tabela de certificados manuais
$stmt = $db->prepare('SELECT * FROM certificados_manuais WHERE codigo_autenticidade = ? LIMIT 1');
$stmt->execute([$codigo]);
$certManual = $stmt->fetch();

if ($certManual) {
    jsonOk([
        'tipo'                 => 'manual',
        'cliente_nome'         => $certManual['cliente_nome'],
        'documento'            => '—',
        'curso_nome'           => $certManual['curso_nome'],
        'carga_horaria'        => (int)$certManual['carga_horaria'],
        'data_conclusao'       => $certManual['data_conclusao'] . ' 00:00:00',
        'tipo_texto'           => $certManual['tipo_texto'],
        'instrutor_nome'       => $certManual['instrutor_nome'],
        'assinatura_url'       => $certManual['assinatura_url'],
        'codigo_autenticidade' => $certManual['codigo_autenticidade'],
        'valido'               => true
    ]);
}

jsonError('Certificado não localizado ou inválido.', 404);
