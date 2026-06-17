<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAdmin();
$method = $_SERVER['REQUEST_METHOD'];
$db = getDB();

if ($method === 'GET') {
    // Retorna a média de cada pergunta e o total de votos
    $stmt = $db->query('
        SELECT pp.id, pp.texto, 
               COALESCE(AVG(pr.nota), 0) AS media, 
               COUNT(pr.id) AS total_respostas
        FROM pesquisa_perguntas pp
        LEFT JOIN pesquisa_respostas pr ON pp.id = pr.pergunta_id
        GROUP BY pp.id, pp.texto
        ORDER BY pp.id
    ');
    
    $stats = $stmt->fetchAll();
    
    // Formata médias como floats de precisão 1 casa decimal
    foreach ($stats as &$s) {
        $s['media'] = round((float)$s['media'], 1);
        $s['total_respostas'] = (int)$s['total_respostas'];
    }

    jsonOk($stats);
}

methodNotAllowed();
