# Modelo de Banco de Dados — ActShare EAD

Este documento detalha o modelo relacional de banco de dados do ActShare, mapeando as principais tabelas, colunas, chaves estrangeiras e explicando a lógica por trás do sistema de vagas B2B.

---

## 1. Usuários e Organizações

### Tabela `usuarios`
Armazena todos os cadastros do sistema, diferenciando permissões por papel.
- **`role`**: `ENUM('admin', 'gestor', 'aluno', 'instrutor')`. Define o tipo de acesso.
- **Dados Pessoais/Corporativos**: CPF/CNPJ (`documento`), Razão Social (`razao_social`), endereço completo e data de nascimento.

### Tabela `organizacoes`
Representa uma empresa no sistema.
- **`gestor_id`**: Chave estrangeira ligando ao usuário administrador/dono da empresa (`usuarios.id`).
- **`certificado_acesso`**: `ENUM('empresa', 'aluno', 'ambos')`. Define quem pode baixar certificados (modal de primeiro acesso).

### Tabela `membros_organizacao`
Tabela pivô que vincula funcionários (alunos) e sub-gestores a uma organização corporativa.
- **`organizacao_id`**: Referencia `organizacoes.id`.
- **`usuario_id`**: Referencia `usuarios.id` (pode ter role `aluno` ou `gestor`).

---

## 2. Estrutura Pedagógica (Cursos e Aulas)

```
cursos ──► modulos ──► aulas
```

### Tabela `cursos`
Configuração principal dos treinamentos da loja e do EAD.
- **`certificado_liberacao`**: `ENUM('ambos', 'empresa', 'aluno')`. Define controle do certificado por curso.
- **`certificado_config`**: Armazena JSON com as coordenadas (x, y), fontes e cores dos textos sobre o certificado padrão.

### Tabela `aulas`
Aulas do curso. Podem ser do tipo `video`, `texto`, `quiz` ou `pdf`.
- **`e_prova`**: Flag booleana (`TINYINT(1)`). Indica se a aula é o exame final do curso.
- **Configuração de Prova**: Nota de corte (`nota_corte_valor`), tempo limite em minutos (`tempo_limite_minutos`) e monitoramento de cola (`bloquear_proctoring`).

### Tabela `perguntas` e `opcoes`
Banco de questões estruturado para quizzes e provas. Cada pergunta pertence a uma aula do tipo `quiz`.

---

## 3. Matrículas e Progresso

### Tabela `matriculas`
Esta é a tabela principal de controle acadêmico e comercial. Ela desempenha papel duplo:

1. **Matrícula de Aluno/Participante Comum**:
   - `vagas_totais` é `NULL`.
   - Representa que o aluno está cursando individualmente.
   
2. **Contrato de Compra B2B (Gestor Corporativo)**:
   - `aluno_id` referencia o ID do gestor corporativo.
   - `vagas_totais` contém a quantidade de vagas adquiridas (ex: 5).
   - `vagas_usadas` rastreia quantas licenças já foram alocadas para funcionários.
   - `participante`: Flag (`TINYINT(1)`). Se `1`, o gestor também se cadastrou como aluno desse curso (consumindo 1 vaga do total).

### Tabela `progresso_aula`
Rastreia a conclusão individual de cada vídeo ou leitura pelo estudante.

---

## 4. Avaliações e Tentativas

### Tabela `quiz_resposta`
Guarda a situação atual de aprovação de um aluno em um quiz ou prova.
- **`tentativas_restantes`**: Controla o limite (padrão 5).
- **`aprovado`**: Flag que libera a progressão ou certificado.

### Tabela `avaliacao_tentativas`
Histórico detalhado de exames oficiais realizados por participantes.
- **`respostas_json`**: Texto JSON contendo a cópia exata de cada pergunta respondida, alternativa marcada pelo aluno, opção correta e a justificativa didática (gabarito).
- Permite que o gestor examine detalhadamente o desempenho do funcionário nas provas.

### Tabela `proctoring_logs`
Registra incidentes detectados de possíveis trapaças durante as avaliações (ex: Alt+Tab, sair do mouse, etc).

---

## 5. Cupons e Vendas

- **`cupons`**: Armazena cupons de desconto fixo ou percentual com data de validade.
- **`pedidos`** e **`itens_pedido`**: Registro comercial de transações do carrinho de compras.
- **`certificados_manuais`**: Registro de certificados importados manualmente via planilha por administradores.
