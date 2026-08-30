<?php
/**
 * Templates de e-mail configuráveis pelo Admin (tabela email_templates).
 * Cada template tem placeholders {nome}, {curso}, {total}, etc. substituídos
 * na hora do envio. Usa o mail() nativo do PHP — funciona de verdade assim
 * que o servidor de produção tiver SMTP configurado (mesmo caso do ASAAS:
 * a estrutura já está pronta, só depende do ambiente).
 */
function enviarEmailTemplate(PDO $db, string $chave, string $destinatarioEmail, array $variaveis = []): bool {
    $stmt = $db->prepare('SELECT assunto, corpo FROM email_templates WHERE chave = ? AND ativo = 1 LIMIT 1');
    $stmt->execute([$chave]);
    $tpl = $stmt->fetch();
    if (!$tpl) return false;

    $assunto = $tpl['assunto'];
    $corpo = $tpl['corpo'];
    foreach ($variaveis as $k => $v) {
        $assunto = str_replace('{' . $k . '}', (string)$v, $assunto);
        $corpo = str_replace('{' . $k . '}', (string)$v, $corpo);
    }

    $headers = "From: ActShare <no-reply@actshare.com.br>\r\nContent-Type: text/plain; charset=UTF-8";
    return @mail($destinatarioEmail, $assunto, $corpo, $headers);
}
