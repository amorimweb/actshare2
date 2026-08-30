<?php $pageTitle = 'Pesquisa de Satisfação — ActShare'; ?>
<?php require __DIR__ . '/../layout/admin-header.php'; ?>

<div class="flex items-start justify-between gap-4 flex-wrap mb-6">
  <div>
    <h1 class="text-2xl font-bold text-slate-800">Pesquisa de Satisfação</h1>
    <p class="text-xs text-slate-400 mt-1">Monitore o feedback dos alunos sobre a didática, o conteúdo, o material e a plataforma.</p>
  </div>
  <div class="flex gap-2">
    <button onclick="abrirModalPerguntasSatisfacao()" class="bg-white border border-slate-200 text-slate-700 text-xs font-semibold px-4 py-2.5 rounded-lg hover:bg-slate-50 transition-colors">
      Gerenciar Perguntas
    </button>
    <button onclick="exportarSatisfacaoCsv()" class="bg-primary text-white text-xs font-semibold px-4 py-2.5 rounded-lg hover:bg-blue-900 transition-colors">
      Exportar CSV
    </button>
  </div>
</div>

<!-- Filtros -->
<div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex flex-wrap gap-3 items-center mb-6">
  <select id="sat-filtro-curso" onchange="carregarRelatorioSatisfacao()" class="border border-slate-200 rounded-lg text-xs px-3 py-2 bg-white">
    <option value="">Todos os Cursos</option>
  </select>
  <select id="sat-filtro-cliente" onchange="carregarRelatorioSatisfacao()" class="border border-slate-200 rounded-lg text-xs px-3 py-2 bg-white">
    <option value="">Todos os Clientes</option>
  </select>
  <select id="sat-filtro-aluno" onchange="carregarRelatorioSatisfacao()" class="border border-slate-200 rounded-lg text-xs px-3 py-2 bg-white">
    <option value="">Todos os Alunos</option>
  </select>

  <div class="ml-auto inline-flex p-0.5 bg-slate-100 rounded-lg">
    <button onclick="alternarFormatoSatisfacao('cards')" id="btn-formato-cards" class="px-3 py-1.5 text-xs font-bold rounded-md bg-white shadow-sm text-slate-700">Lista</button>
    <button onclick="alternarFormatoSatisfacao('tabular')" id="btn-formato-tabular" class="px-3 py-1.5 text-xs font-bold rounded-md text-slate-500">Tabular</button>
  </div>
</div>

<!-- Grid de Indicadores consolidado -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="satisfacao-relatorio-container">
  <div class="col-span-full text-center py-12 text-slate-400 bg-white rounded-xl border border-slate-200 shadow-sm">
    Carregando dados da pesquisa...
  </div>
</div>

<!-- Modal Gerenciar Perguntas -->
<div id="modal-perguntas-satisfacao" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl w-full max-w-xl p-6 max-h-[90vh] overflow-y-auto">
    <div class="flex items-center justify-between mb-5">
      <h2 class="text-lg font-bold text-gray-800">Perguntas da Pesquisa</h2>
      <button onclick="document.getElementById('modal-perguntas-satisfacao').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">✕</button>
    </div>

    <form id="form-nova-pergunta-satisfacao" class="flex gap-2 mb-5">
      <input type="text" id="nova-pergunta-satisfacao-texto" required placeholder="Nova pergunta..." class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm">
      <button type="submit" class="bg-primary text-white text-xs font-semibold px-4 py-2 rounded-lg">Adicionar</button>
    </form>

    <div id="lista-perguntas-satisfacao" class="space-y-2 text-sm">Carregando...</div>
  </div>
</div>

<script src="<?= BASE_PATH ?>/assets/js/satisfacao-admin.js?v=1"></script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
