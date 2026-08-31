-- Lote V16 do novo ciclo de ajustes do cliente

SET NAMES utf8mb4;

-- Item 6: Admin edita status de pagamento do pedido. "Baixa Manual" é
-- distinto de "Pago" (que fica reservado à confirmação automática via
-- Asaas) — só STS=Pago trava a edição do pedido.
ALTER TABLE pedidos
  MODIFY COLUMN situacao ENUM('pendente','pago','baixa_manual','cancelado') NOT NULL DEFAULT 'pendente';

ALTER TABLE pedidos
  ADD COLUMN IF NOT EXISTS observacao_admin TEXT DEFAULT NULL;

-- Item 7: cliente cadastrado manualmente pelo Admin, sem pedido ou
-- organização ainda — precisa desse marcador pra aparecer em Admin/Clientes
-- (hoje a lista só mostra quem já tem pedido ou é gestor de organização).
ALTER TABLE usuarios
  ADD COLUMN IF NOT EXISTS cliente_manual TINYINT(1) NOT NULL DEFAULT 0;
