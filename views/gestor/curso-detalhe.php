<?php
$pageTitle = 'Gerenciar Vagas — ActShare';
require __DIR__ . '/../layout/header.php';
$cursoId = (int)($_GET['id'] ?? 0);
?>

<div class="max-w-7xl mx-auto px-4 py-8">
  <!-- Breadcrumbs & Voltar -->
  <div class="mb-6">
    <a href="<?= BASE_PATH ?>/gestor" class="text-sm font-semibold text-primary hover:underline flex items-center gap-1">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
      Voltar para o Painel do Gestor
    </a>
  </div>

  <div id="detail-loading" class="text-center py-16 text-gray-400">
    <div class="inline-block w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin mb-3"></div>
    <p>Carregando dados da compra...</p>
  </div>

  <div id="detail-content" class="hidden space-y-8">
    <!-- Bloco de Título e Informações Gerais -->
    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
      <div>
        <span class="text-xs font-bold text-secondary uppercase tracking-wider">Gestão de Vagas B2B</span>
        <h1 id="curso-titulo" class="text-2xl font-bold text-gray-800 mt-1"></h1>
        <p class="text-sm text-gray-400 mt-1" id="info-compra"></p>
      </div>
      <div id="prazo-alocacao" class="px-4 py-2 rounded-xl text-sm font-semibold shadow-sm"></div>
    </div>

    <!-- Cards de Vagas -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
      <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm text-center">
        <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Vagas Contratadas</h4>
        <div id="vagas-totais" class="text-3xl font-extrabold text-gray-800">0</div>
      </div>
      <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm text-center">
        <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Vagas Utilizadas</h4>
        <div id="vagas-usadas" class="text-3xl font-extrabold text-primary">0</div>
      </div>
      <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm text-center">
        <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Vagas Disponíveis</h4>
        <div id="vagas-disponiveis" class="text-3xl font-extrabold text-secondary">0</div>
      </div>
    </div>

    <!-- Lado a Lado: Formulário de Inclusão e Painel de Participação do Gestor -->
    <div class="grid lg:grid-cols-3 gap-8">
      <!-- Coluna Formulário de Inclusão -->
      <div class="lg:col-span-2 space-y-6">
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
          <h3 class="font-bold text-gray-800 text-lg mb-2">Incluir Novo Aluno</h3>
          <p class="text-xs text-gray-400 mb-4">Adicione participantes usando e-mail. Se ele não possuir conta, ela será gerada automaticamente.</p>
          
          <div id="form-alert-msg" class="hidden mb-4 rounded-xl p-4 border text-sm"></div>

          <form id="form-aluno" onsubmit="adicionarAluno(event)" class="flex flex-col sm:flex-row gap-3">
            <input type="email" id="input-email" required
              placeholder="Digite o e-mail do participante..."
              class="flex-1 px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-shadow">
            <button type="submit" id="btn-incluir"
              class="bg-primary text-white font-semibold py-2.5 px-6 rounded-xl hover:bg-blue-900 transition-colors text-sm shadow-sm disabled:opacity-60">
              Incluir Aluno
            </button>
          </form>
        </div>

        <!-- Tabela de Alocados -->
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
          <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-800 text-lg">Participantes Matriculados</h3>
            <span class="text-xs font-semibold text-gray-400" id="participantes-count">0 matriculados</span>
          </div>
          
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
              <thead class="bg-gray-50 text-gray-500 font-semibold text-xs uppercase tracking-wider">
                <tr>
                  <th class="px-6 py-4">Nome / Email</th>
                  <th class="px-6 py-4 text-center">Progresso</th>
                  <th class="px-6 py-4">Fim do Acesso</th>
                  <th class="px-6 py-4 text-right">Ações</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 text-gray-700" id="participantes-table-body">
                <tr>
                  <td colspan="4" class="px-6 py-8 text-center text-gray-400">Nenhum participante matriculado.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Coluna Painel de Autocadastro / Configurações adicionais -->
      <div class="space-y-6">
        <!-- Você é participante? -->
        <div id="gestor-participa-card" class="bg-gradient-to-br from-primary to-blue-800 text-white rounded-2xl p-6 shadow-sm space-y-4 hidden">
          <h3 class="font-bold text-lg">Você também é participante?</h3>
          <p class="text-xs text-blue-200 leading-relaxed">
            Caso você mesmo pretenda realizar o treinamento, cadastre-se como participante para poder assistir as aulas e obter o seu certificado.
          </p>
          <div class="text-xs bg-white/10 px-3 py-2 rounded-lg border border-white/10 text-center font-medium">
            Consome 1 vaga do seu contrato B2B.
          </div>
          <button onclick="autocadastrarGestor()" id="btn-gestor-participar"
            class="w-full bg-secondary hover:bg-green-600 text-white font-bold py-3 rounded-xl transition-colors text-sm shadow-md">
            Quero Participar do Curso!
          </button>
        </div>

        <div id="gestor-participa-active" class="bg-green-50 border border-green-200 text-green-800 rounded-2xl p-6 shadow-sm space-y-3 hidden">
          <div class="flex items-center gap-2">
            <svg class="w-6 h-6 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <h3 class="font-bold text-sm">Você está participando!</h3>
          </div>
          <p class="text-xs text-green-600 leading-relaxed">
            Você está cadastrado como participante deste curso e seu progresso está sendo contabilizado nas estatísticas.
          </p>
          <a href="<?= BASE_PATH ?>/painel/curso/<?= $cursoId ?>" class="block w-full text-center bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 rounded-xl transition-colors text-sm shadow-sm">
            Ir para o Player de Aula
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  const cursoId = <?= $cursoId ?>;
  let contractData = null;
  let hasExpired = false;

  document.addEventListener('DOMContentLoaded', () => {
    const user = authGetUser();
    if (!user || user.role !== 'gestor') { window.location.href = BASE + '/login'; return; }
    
    carregarDadosCurso();
  });

  async function carregarDadosCurso() {
    const loading = document.getElementById('detail-loading');
    const content = document.getElementById('detail-content');
    
    try {
      contractData = await apiFetch(BASE + `/api/master/cursos/${cursoId}`);
      
      document.getElementById('curso-titulo').textContent = contractData.curso_titulo;
      
      const dataCompra = new Date(contractData.data_compra);
      document.getElementById('info-compra').textContent = `Adquirido em ${dataCompra.toLocaleDateString('pt-BR')} · Carga Horária: ${contractData.carga_horaria_horas}h`;

      // Estatísticas
      const total = parseInt(contractData.vagas_totais);
      const usadas = parseInt(contractData.vagas_usadas);
      const disponiveis = Math.max(0, total - usadas);

      document.getElementById('vagas-totais').textContent = total;
      document.getElementById('vagas-usadas').textContent = usadas;
      document.getElementById('vagas-disponiveis').textContent = disponiveis;

      // Prazo de alocação de 45 dias
      const dataLimite = new Date(dataCompra.getTime() + (45 * 24 * 3600 * 1000));
      const diffTime = dataLimite.getTime() - new Date().getTime();
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
      
      const prazoEl = document.getElementById('prazo-alocacao');
      if (diffDays <= 0) {
        hasExpired = true;
        prazoEl.textContent = 'Prazo de Alocação Expirado';
        prazoEl.className = 'px-4 py-2 rounded-xl text-sm font-semibold bg-red-50 text-red-600 border border-red-100 shadow-sm';
      } else {
        hasExpired = false;
        prazoEl.textContent = `Alocar em até ${diffDays} dias`;
        prazoEl.className = 'px-4 py-2 rounded-xl text-sm font-semibold bg-orange-50 text-orange-600 border border-orange-100 shadow-sm';
      }

      // Painel de participação do gestor
      const selfCard = document.getElementById('gestor-participa-card');
      const selfActive = document.getElementById('gestor-participa-active');
      if (contractData.participante == 1) {
        selfCard.classList.add('hidden');
        selfActive.classList.remove('hidden');
      } else {
        selfActive.classList.add('hidden');
        if (disponiveis > 0 && !hasExpired) {
          selfCard.classList.remove('hidden');
        } else {
          selfCard.classList.add('hidden');
        }
      }

      // Habilitar/Desabilitar formulário de alocação
      const emailInput = document.getElementById('input-email');
      const btnIncluir = document.getElementById('btn-incluir');
      if (disponiveis <= 0 || hasExpired) {
        emailInput.disabled = true;
        btnIncluir.disabled = true;
        if (disponiveis <= 0) emailInput.placeholder = 'Todas as vagas já foram utilizadas.';
        if (hasExpired) emailInput.placeholder = 'Prazo de 45 dias expirou.';
      } else {
        emailInput.disabled = false;
        btnIncluir.disabled = false;
        emailInput.placeholder = 'Digite o e-mail do participante...';
      }

      await carregarParticipantes(disponiveis);

      loading.classList.add('hidden');
      content.classList.remove('hidden');

    } catch (err) {
      loading.innerHTML = `<p class="text-red-500 font-semibold py-8">Erro: ${err.message}</p>`;
    }
  }

  async function carregarParticipantes(vagasDisponiveis) {
    const tbody = document.getElementById('participantes-table-body');
    const countEl = document.getElementById('participantes-count');
    
    try {
      const participantes = await apiFetch(BASE + `/api/master/cursos/${cursoId}/participantes`);
      countEl.textContent = `${participantes.length} matriculados`;
      
      if (participantes.length === 0) {
        tbody.innerHTML = `
          <tr>
            <td colspan="4" class="px-6 py-8 text-center text-gray-400">Nenhum participante matriculado.</td>
          </tr>
        `;
        return;
      }
      
      tbody.innerHTML = participantes.map(p => {
        const prog = Math.round(p.progresso_total || 0);
        const dataFim = p.data_fim_acesso ? new Date(p.data_fim_acesso).toLocaleDateString('pt-BR') : 'N/A';
        
        // Exclusão permitida apenas se o progresso for 0 e não for o próprio gestor alocado como participante (o gestor não se exclui pelo botão de aluno comum)
        const canDelete = prog === 0 && !p.is_gestor_self;
        
        return `
          <tr class="hover:bg-gray-50/50">
            <td class="px-6 py-4">
              <div class="font-semibold text-gray-800 text-sm">${p.nome}</div>
              <div class="text-xs text-gray-400">${p.email}</div>
            </td>
            <td class="px-6 py-4 text-center">
              <div class="flex flex-col items-center">
                <span class="text-xs font-bold text-gray-600 mb-1">${prog}%</span>
                <div class="w-16 bg-gray-100 rounded-full h-1.5">
                  <div class="bg-secondary rounded-full h-1.5" style="width: ${prog}%"></div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-xs text-gray-500 whitespace-nowrap">
              ${dataFim}
            </td>
            <td class="px-6 py-4 text-right whitespace-nowrap">
              ${canDelete
                ? `<button onclick="removerAluno(${p.id})" class="text-xs font-bold text-red-600 hover:text-red-800 hover:underline">Remover</button>`
                : p.is_gestor_self
                  ? `<span class="text-xs text-primary font-bold">Gestor</span>`
                  : `<span class="text-xs text-gray-400 cursor-not-allowed" title="Aluno já iniciou o curso">Não removível</span>`
              }
            </td>
          </tr>
        `;
      }).join('');

    } catch (err) {
      tbody.innerHTML = `
        <tr>
          <td colspan="4" class="px-6 py-8 text-center text-red-500">Erro ao carregar participantes.</td>
        </tr>
      `;
    }
  }

  async function adicionarAluno(event) {
    event.preventDefault();
    const btn = document.getElementById('btn-incluir');
    const alertEl = document.getElementById('form-alert-msg');
    const emailInput = document.getElementById('input-email');
    
    alertEl.classList.add('hidden');
    btn.disabled = true;
    btn.textContent = 'Adicionando...';

    const email = emailInput.value.trim();

    try {
      await apiPost(BASE + `/api/master/cursos/${cursoId}/participante`, { email });
      emailInput.value = '';
      alertEl.textContent = 'Aluno cadastrado e matriculado com sucesso!';
      alertEl.className = 'mb-4 rounded-xl p-4 border text-sm bg-green-50 border-green-200 text-green-700 block';
      
      // Recarregar dados
      carregarDadosCurso();
    } catch (err) {
      alertEl.textContent = err.message || 'Erro ao adicionar aluno.';
      alertEl.className = 'mb-4 rounded-xl p-4 border text-sm bg-red-50 border-red-200 text-red-700 block';
      btn.disabled = false;
      btn.textContent = 'Incluir Aluno';
    }
  }

  async function removerAluno(alunoId) {
    if (!confirm('Deseja realmente remover este participante e liberar a vaga dele?')) return;
    
    try {
      await apiDelete(BASE + `/api/master/cursos/${cursoId}/participante/${alunoId}`);
      carregarDadosCurso();
    } catch (err) {
      alert(err.message || 'Erro ao remover aluno.');
    }
  }

  async function autocadastrarGestor() {
    const btn = document.getElementById('btn-gestor-participar');
    btn.disabled = true;
    btn.textContent = 'Registrando...';
    
    try {
      await apiPost(BASE + `/api/master/cursos/${cursoId}/participar`);
      carregarDadosCurso();
    } catch (err) {
      alert(err.message || 'Erro no autocadastro.');
      btn.disabled = false;
      btn.textContent = 'Quero Participar do Curso!';
    }
  }
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
