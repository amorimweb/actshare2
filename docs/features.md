# Documentação de Funcionalidades — ActShare EAD

Este documento descreve detalhadamente o funcionamento prático das principais ferramentas e fluxos da plataforma ActShare EAD.

---

## 1. Painel Corporativo (Gestão B2B)

O Painel do Gestor centraliza a administração de licenças e treinamentos corporativos de uma organização, organizado em quatro seções principais:

### 1.1. Treinamentos (Licenças Adquiridas)
- **Painel de Vagas**: Lista as compras B2B com contadores de vagas utilizadas em relação às totais contratadas.
- **Alocação de Participantes**: Permite alocar vagas inserindo o e-mail do funcionário. Caso ele não tenha conta, ela é criada com senha provisória `actshare123`.
- **Regras de Alocação**:
  - Limite de 45 dias corridos após a compra para alocação.
  - O gestor pode remover um participante e liberar a vaga somente se o progresso dele for `0%`.
  - **Autocadastro**: O próprio gestor pode usar 1 vaga do contrato para ser aluno clicando em "Quero Participar".
- **Relatório Resumo**: Abre um relatório formatado para impressão (layout A4 limpo) exibindo a média de progresso da equipe, média de aproveitamento nas provas, total de conclusões e tabela detalhada de alunos (início, fim e notas).

### 1.2. Alunos (Acompanhamento Geral)
- **Filtro Dinâmico**: Permite buscar funcionários e filtrar por status.
- **Mapeamento de Status**:
  - **Não Iniciou**: Progresso em 0%.
  - **Em Andamento**: Progresso maior que 0% (exibe badge com porcentagem).
  - **Concluído**: Completou 100% (cursos sem prova).
  - **Aprovado (Exame)**: Completou o curso e passou na prova final (nota >= corte).
  - **Reprovado (Exame)**: Completou o curso, mas reprovou na prova final ou esgotou tentativas.
  - **Prazo Vencido**: Data limite ultrapassada sem conclusão do curso.
- **Visualização de Provas**: Mostra o histórico detalhado de tentativas. Clicando em "Ver Respostas", o gestor abre o gabarito contendo o enunciado da questão, qual alternativa o aluno marcou, a alternativa correta e a justificativa didática cadastrada pelo instrutor.

### 1.3. Gestores (Sub-Administradores)
- Permite que o gestor principal (Master 1) cadastre e exclua outros gestores corporativos na mesma empresa.
- **"Vc é participante?"**: Ao marcar este checkbox no cadastro, o novo gestor é matriculado automaticamente em todos os cursos corporativos ativos da empresa com vagas disponíveis.

### 1.4. Meus Treinamentos
- Direciona o gestor para a sua Área de Aluno (`/painel`) se estiver matriculado em cursos.
- Exibe o aviso *"Você não está matriculado em nenhum curso"* caso não possua matrículas ativas.

### 1.5. Primeiro Acesso (Preferência de Certificado)
- Na primeira vez que entra no painel, o gestor configura quem pode acessar os certificados: **Somente a Empresa** (bloqueia o download pelo aluno, exibindo um aviso na tela dele), **Somente o Aluno** ou **Empresa e Alunos** (ambos).

---

## 2. Área do Aluno & Player de Aulas

### 2.1. Player de Aula Integrado
- Reproduz vídeos, exibe PDFs e textos. Marca automaticamente a conclusão e salva o tempo de parada (`tempo_parada`) do aluno para que ele possa continuar de onde parou.

### 2.2. Proctoring Ativo (Anti-Cheat)
- Lógica de monitoramento durante a realização de provas oficiais (`e_prova = 1`):
  - Detecta perda de foco da janela (`window.blur`).
  - Detecta mudança de aba no navegador (`visibilitychange`).
  - Detecta saída do cursor do mouse do viewport da tela.
  - Bloqueia comandos comuns como PrintScreen, Ctrl+C, Ctrl+V e cliques com botão direito do mouse.
  - Salva todas as ocorrências na tabela `proctoring_logs`.

### 2.3. Resiliência de Conexão
- Em caso de queda de internet durante a prova, o timer é pausado por até 5 minutos para que o aluno se reconecte sem perder tempo de prova. Permite até 5 tentativas de queda de rede por avaliação antes de submeter as respostas automaticamente.

### 2.4. Pesquisa de Satisfação Obrigatória
- O aluno precisa responder à pesquisa de satisfação estruturada (escala de 1 a 5 estrelas) logo após concluir a última aula/prova para que o botão de emissão de certificado seja habilitado.

---

## 3. Emissão e Validação de Certificados

### 3.1. Geração Dinâmica de Certificados
- Monta o certificado sobrepondo dinamicamente os textos (Nome do aluno, Curso, Data e Assinatura) sobre a imagem de fundo utilizando as coordenadas X/Y configuradas em pixels.
- Código de autenticidade sequencial único (`[CODIGO_CURSO]-[ID_MATRICULA]`).

### 3.2. Tela Pública de Validação (`/validar-certificado`)
- Permite que terceiros digitem o código de autenticidade para verificar se o certificado é autêntico.
- Exibe as informações do aluno, curso, carga horária, data e instrutor responsável, permitindo imprimir o comprovante oficial direto do navegador.
