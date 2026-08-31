-- Lote V16 do novo ciclo de ajustes do cliente

SET NAMES utf8mb4;

-- Item 6: Admin edita status de pagamento do pedido. "Baixa Manual" é
-- distinto de "Pago" (que fica reservado à confirmação automática via
-- Asaas) — só STS=Pago trava a edição do pedido.
ALTER TABLE pedidos
  MODIFY COLUMN situacao ENUM('pendente','pago','baixa_manual','cancelado') NOT NULL DEFAULT 'pendente';

ALTER TABLE pedidos
  ADD COLUMN IF NOT EXISTS observacao_admin TEXT DEFAULT NULL;
