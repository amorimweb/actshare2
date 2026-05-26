<?php
$pageTitle = 'Painel do Gestor — ActShare';
require __DIR__ . '/../layout/header.php';
?>

<div class="max-w-7xl mx-auto px-4 py-8">
  <!-- Cabeçalho do dashboard -->
  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <div>
      <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">Painel Corporativo</h1>
      <p class="text-sm text-gray-500 mt-1">Gerencie os treinamentos, vagas e o progresso da sua equipe.</p>
    </div>
    <a href="<?= BASE_PATH ?>/painel" class="bg-gray-100 text-gray-700 font-semibold px-4 py-2 rounded-xl hover:bg-gray-200 transition-colors text-sm shadow-sm">
      ← Área do Participante
    </a>
  </div>

  <!-- Abas do Dashboard -->
  <div class="border-b border-gray-200 mb-8 flex gap-4">
    <button onclick="switchTab('produtos')" id="tab-produtos-btn" class="border-b-2 border-primary pb-3 text-sm font-bold text-primary transition-all focus:outline-none">
      Meus Produtos (Cursos)
    </button>
    <button onclick="switchTab('usuarios')" id="tab-usuarios-btn" class="border-b-2 border-transparent pb-3 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 transition-all focus:outline-none">
      Usuários Alocados (Relatório)
    </button>
  </div>

  <!-- Conteúdo da Aba 1: Meus Produtos -->
  <div id="tab-produtos" class="space-y-6">
    <div id="loading-produtos" class="text-center py-16 text-gray-400">
      <div class="inline-block w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin mb-3"></div>
      <p>Carregando seus produtos...</p>
    </div>
    
    <div id="produtos-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 hidden"></div>
    <p id="produtos-empty" class="hidden text-center py-16 text-gray-400 bg-gray-50 border border-gray-100 rounded-2xl">
      Nenhum curso B2B adquirido foi localizado para a sua conta.
    </p>
  </div>

  <!-- Conteúdo da Aba 2: Usuários Alocados -->
  <div id="tab-usuarios" class="space-y-6 hidden">
    <!-- Filtros/Busca -->
    <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm flex flex-col md:flex-row gap-4 items-center justify-between">
      <div class="w-full md:w-72 relative">
        <input type="text" id="search-alunos" oninput="filtrarAlunos()"
          placeholder="Buscar por nome ou e-mail..."
          class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-300 rounded-xl text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-shadow">
        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
      </div>
      <div class="text-xs text-gray-400 font-medium" id="alunos-count"></div>
    </div>

    <!-- Tabela Geral -->
    <div id="loading-usuarios" class="text-center py-16 text-gray-400">
      <div class="inline-block w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin mb-3"></div>
      <p>Carregando relatórios dos alunos...</p>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hidden" id="table-wrapper">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
          <thead class="bg-gray-50 text-gray-500 font-semibold text-xs uppercase tracking-wider">
            <tr>
              <th class="px-6 py-4">Pedido / Data</th>
              <th class="px-6 py-4">Nome do Aluno</th>
              <th class="px-6 py-4">Treinamento</th>
              <th class="px-6 py-4 text-center">Progresso</th>
              <th class="px-6 py-4 text-center">Nota (Quiz)</th>
              <th class="px-6 py-4">Fim do Acesso</th>
              <th class="px-6 py-4 text-center">Certificado</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 text-gray-700" id="alunos-table-body"></tbody>
        </table>
      </div>
    </div>
    <p id="usuarios-empty" class="hidden text-center py-16 text-gray-400 bg-gray-50 border border-gray-100 rounded-2xl">
      Nenhum aluno foi alocado nos cursos da sua empresa ainda.
    </p>
  </div>
</div>

<script>
  let listaProdutos = [];
  let listaAlunos = [];

  document.addEventListener('DOMContentLoaded', () => {
    const user = authGetUser();
    if (!user || user.role !== 'gestor') { window.location.href = BASE + '/login'; return; }
    
    carregarProdutos();
    carregarAlunosRelatorio();
  });

  function switchTab(tab) {
    const tabProd = document.getElementById('tab-produtos');
    const tabUser = document.getElementById('tab-usuarios');
    const btnProd = document.getElementById('tab-produtos-btn');
    const btnUser = document.getElementById('tab-usuarios-btn');

    if (tab === 'produtos') {
      tabProd.classList.remove('hidden');
      tabUser.classList.add('hidden');
      btnProd.className = "border-b-2 border-primary pb-3 text-sm font-bold text-primary transition-all focus:outline-none";
      btnUser.className = "border-b-2 border-transparent pb-3 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 transition-all focus:outline-none";
    } else {
      tabProd.classList.add('hidden');
      tabUser.classList.remove('hidden');
      btnUser.className = "border-b-2 border-primary pb-3 text-sm font-bold text-primary transition-all focus:outline-none";
      btnProd.className = "border-b-2 border-transparent pb-3 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 transition-all focus:outline-none";
    }
  }

  async function carregarProdutos() {
    const grid = document.getElementById('produtos-grid');
    const loading = document.getElementById('loading-produtos');
    const empty = document.getElementById('produtos-empty');
    
    try {
      listaProdutos = await apiFetch(BASE + '/api/master/cursos');
      loading.classList.add('hidden');
      
      if (listaProdutos.length === 0) {
        empty.classList.remove('hidden');
        grid.classList.add('hidden');
        return;
      }
      
      empty.classList.add('hidden');
      grid.classList.remove('hidden');
      
      grid.innerHTML = listaProdutos.map(prod => {
        // Cálculo de dias restantes para alocação (45 dias de prazo)
        const dataCompra = new Date(prod.data_compra);
        const dataLimite = new Date(dataCompra.getTime() + (45 * 24 * 3600 * 1000));
        const diffTime = dataLimite.getTime() - new Date().getTime();
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        let prazoHtml = '';
        if (diffDays <= 0) {
          prazoHtml = `<span class="text-xs font-semibold text-red-500">Prazo de alocação de 45 dias expirado</span>`;
        } else {
          prazoHtml = `<span class="text-xs font-semibold text-orange-500">Alocar em até ${diffDays} dias</span>`;
        }

        const pct = Math.round((prod.vagas_usadas / prod.vagas_totais) * 100);

        return `
          <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm flex flex-col hover:shadow-md transition-shadow">
            ${prod.thumb_url
              ? `<img src="${prod.thumb_url}" alt="" class="w-full h-40 object-cover">`
              : `<div class="w-full h-40 bg-gradient-to-br from-primary to-blue-800 flex items-center justify-center text-white/20 font-bold">ActShare B2B</div>`
            }
            <div class="p-5 flex-1 flex flex-col">
              <h3 class="font-bold text-gray-800 text-lg mb-3 line-clamp-2">${prod.curso_titulo}</h3>
              <div class="space-y-4 mb-6 mt-auto">
                <div>
                  <div class="flex justify-between text-xs text-gray-500 mb-1">
                    <span>Vagas Utilizadas</span>
                    <span class="font-bold">${prod.vagas_usadas} / ${prod.vagas_totais}</span>
                  </div>
                  <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="bg-primary rounded-full h-2 transition-all" style="width: ${pct}%"></div>
                  </div>
                </div>
                <div class="flex items-center gap-1.5">
                  <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  ${prazoHtml}
                </div>
              </div>
              
              <a href="${BASE}/gestor/curso/${prod.curso_id}" class="block w-full text-center bg-primary text-white font-semibold py-2.5 rounded-xl hover:bg-blue-900 transition-colors text-sm shadow-sm">
                Gerenciar Vagas
              </a>
            </div>
          </div>
        `;
      }).join('');
      
    } catch (err) {
      loading.innerHTML = `<p class="text-red-500">Erro: ${err.message}</p>`;
    }
  }

  async function carregarAlunosRelatorio() {
    const tableWrapper = document.getElementById('table-wrapper');
    const loading = document.getElementById('loading-usuarios');
    const empty = document.getElementById('usuarios-empty');
    
    try {
      listaAlunos = await apiFetch(BASE + '/api/master/alunos');
      loading.classList.add('hidden');
      
      // Filtra e reconstrói lista para formato de tabela (onde cada linha é um Aluno-Matricula)
      let tableData = [];
      listaAlunos.forEach(aluno => {
        if (aluno.matriculas && aluno.matriculas.length > 0) {
          aluno.matriculas.forEach(mat => {
            tableData.push({
              aluno_nome: aluno.nome,
              aluno_email: aluno.email,
              matricula_id: mat.id,
              curso_titulo: mat.curso_titulo,
              progresso: mat.progresso_total,
              concluido: mat.concluido,
              data_conclusao: mat.data_conclusao,
              data_inicio: mat.created_at || mat.data_inicio,
              data_fim: mat.data_fim_acesso
            });
          });
        }
      });
      
      window._alunosTableData = tableData;
      renderizarTabelaAlunos(tableData);
      
    } catch (err) {
      loading.innerHTML = `<p class="text-red-500">Erro: ${err.message}</p>`;
    }
  }

  function renderizarTabelaAlunos(data) {
    const tableWrapper = document.getElementById('table-wrapper');
    const empty = document.getElementById('usuarios-empty');
    const tbody = document.getElementById('alunos-table-body');
    const countEl = document.getElementById('alunos-count');
    
    countEl.textContent = `${data.length} matrícula(s) alocada(s)`;

    if (data.length === 0) {
      tableWrapper.classList.add('hidden');
      empty.classList.remove('hidden');
      return;
    }
    
    empty.classList.add('hidden');
    tableWrapper.classList.remove('hidden');
    
    tbody.innerHTML = data.map(row => {
      const prog = Math.round(row.progresso || 0);
      const dataInicio = new Date(row.data_inicio).toLocaleDateString('pt-BR');
      const dataFim = row.data_fim ? new Date(row.data_fim).toLocaleDateString('pt-BR') : 'N/A';
      
      return `
        <tr class="hover:bg-gray-50/50">
          <td class="px-6 py-4 whitespace-nowrap">
            <span class="font-bold text-gray-800 text-xs">#${row.matricula_id}</span>
            <div class="text-[10px] text-gray-400 mt-0.5">${dataInicio}</div>
          </td>
          <td class="px-6 py-4">
            <div class="font-semibold text-gray-800 text-sm">${row.aluno_nome}</div>
            <div class="text-xs text-gray-400">${row.aluno_email}</div>
          </td>
          <td class="px-6 py-4 max-w-xs truncate font-medium text-gray-700">
            ${row.curso_titulo}
          </td>
          <td class="px-6 py-4 text-center">
            <div class="flex flex-col items-center">
              <span class="text-xs font-bold text-gray-600 mb-1">${prog}%</span>
              <div class="w-16 bg-gray-100 rounded-full h-1.5">
                <div class="bg-secondary rounded-full h-1.5" style="width: ${prog}%"></div>
              </div>
            </div>
          </td>
          <td class="px-6 py-4 text-center whitespace-nowrap text-xs font-medium text-gray-600">
            ${row.concluido ? '100 / 100' : '--'}
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
            ${dataFim}
          </td>
          <td class="px-6 py-4 text-center whitespace-nowrap">
            ${row.concluido
              ? `<a href="${BASE}/certificado?curso=${row.matricula_id}" target="_blank" class="inline-flex items-center gap-1 text-xs text-secondary font-bold hover:underline">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"/></svg>
                  Ver PDF
                 </a>`
              : `<span class="text-xs text-gray-400 font-medium">Em andamento</span>`
            }
          </td>
        </tr>
      `;
    }).join('');
  }

  function filtrarAlunos() {
    const query = document.getElementById('search-alunos').value.toLowerCase().trim();
    if (!window._alunosTableData) return;
    
    const filtrados = window._alunosTableData.filter(row => 
      row.aluno_nome.toLowerCase().includes(query) || 
      row.aluno_email.toLowerCase().includes(query) || 
      row.curso_titulo.toLowerCase().includes(query)
    );
    
    renderizarTabelaAlunos(filtrados);
  }
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
