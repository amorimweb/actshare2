<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/response.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAdmin();

try {
    $db = getDB();
    // Try to describe to check if column already exists
    $stmt = $db->query("SHOW COLUMNS FROM organizacoes LIKE 'certificado_acesso'");
    if ($stmt->fetch()) {
        echo "Column certificado_acesso already exists.\n";
    } else {
        $db->exec("ALTER TABLE organizacoes ADD COLUMN certificado_acesso ENUM('empresa', 'aluno', 'ambos') DEFAULT NULL AFTER ativo");
        echo "Migration successful! Column certificado_acesso added.\n";
    }
} catch (Exception $e) {
    echo "Migration error: " . $e->getMessage() . "\n";
}
