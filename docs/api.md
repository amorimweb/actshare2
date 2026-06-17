# Guia de Referência da API — ActShare EAD

Este documento cataloga os principais endpoints da API backend do ActShare, descrevendo os métodos HTTP, as estruturas de envio (Request Payload) e de resposta (Response JSON).

---

## 1. Autenticação (`api/auth/`)

### `POST /api/auth/login`
Autentica um usuário e inicia a sessão PHP/JWT.
- **Payload**:
  ```json
  { "email": "gestor@empresa.com", "senha": "senha_secreta" }
  ```
- **Resposta**:
  ```json
  { "success": true, "user": { "id": 10, "nome": "Sigrid Sand", "role": "gestor" } }
  ```

### `GET /api/auth/me`
Retorna as informações completas do perfil do usuário logado na sessão ativa.

---

## 2. Endpoints Corporativos B2B (`api/master/`)
*Todos os endpoints abaixo exigem cabeçalho de autenticação e papel do usuário logado como `'gestor'` ou `'admin'`.*

### `GET /api/master/cursos`
Lista todos os contratos B2B de treinamentos contratados pelo gestor logado.
- **Campos Retornados**: Título do curso, vagas totais contratadas, vagas usadas, data de compra, carga horária e se o gestor é participante.

### `POST /api/master/salvar-certificado-acesso`
Define a preferência de liberação dos certificados da organização.
- **Payload**:
  ```json
  { "certificado_acesso": "empresa" } // valores: "empresa", "aluno", "ambos"
  ```

### `GET /api/master/cursos/:id/participantes`
Retorna todos os alunos matriculados nas vagas do contrato B2B especificado por `:id` (curso).

### `POST /api/master/cursos/:id/participante`
Aloca uma vaga para um participante por e-mail. Se o e-mail não possuir conta cadastrada, ela é criada automaticamente com a senha provisória `actshare123`.
- **Payload**:
  ```json
  { "email": "funcionario@empresa.com" }
  ```

### `DELETE /api/master/cursos/:id/participante/:aluno_id`
Remove um aluno da vaga corporativa, liberando a vaga no contrato B2B. A exclusão só é permitida se o progresso do aluno no curso for igual a `0%`.

### `POST /api/master/cursos/:id/participar`
Autocadastra o próprio gestor logado como participante no curso, consumindo 1 vaga do contrato B2B.

### `GET /api/master/relatorio-curso/:id`
Gera estatísticas resumidas de progresso da equipe e notas em avaliações para o curso `:id`.
- **Resposta**: Retorna médias de progresso, aproveitamento (notas) em exames, quantidade de conclusões e a listagem de todos os participantes com data de início e término.

### `GET /api/master/alunos/:aluno_id/cursos/:curso_id/avaliacoes`
Busca todo o histórico de tentativas detalhadas de exames realizadas pelo aluno `:aluno_id` no curso `:curso_id`. Retorna o gabarito das perguntas, respostas marcadas e justificativas.

### `POST /api/master/aluno`
Cadastra um novo participante (aluno) ou sub-gestor corporativo associado à organização.
- **Payload**:
  ```json
  {
    "nome": "João Silva",
    "email": "joao@empresa.com",
    "password": "senha_provisoria",
    "role": "gestor", // ou "aluno"
    "is_participante": true // se true, auto-matricula em todos os cursos ativos com vagas
  }
  ```

---

## 3. Endpoints Acadêmicos (`api/aluno/`)

### `GET /api/aluno/matriculas`
Retorna os treinamentos do aluno logado. Para gestores, oculta os contratos B2B comerciais da lista de aprendizado, a menos que ele seja um participante matriculado (`participante = 1`).

### `GET /api/aluno/curso/:id`
Retorna a grade curricular do curso com aulas, progresso e a flag `bloqueado_empresa` caso as regras de visualização do certificado impeçam o aluno de baixá-lo.

### `POST /api/aluno/quiz/responder`
Corrige o quiz ou prova enviado pelo aluno.
- **Processamento**: Se a aula for prova oficial (`e_prova = 1`), grava uma tentativa na tabela `avaliacao_tentativas` contendo a nota final, acertos e o JSON de gabarito correspondente.
