<?php $pageTitle = 'Meu Painel — ActShare'; ?>
<?php require __DIR__ . '/layout/header.php'; ?>

<div class="max-w-6xl mx-auto px-4 py-10">
  <!-- Cabeçalho do painel -->
  <div class="flex items-center justify-between mb-8">
    <div>
      <h1 class="text-2xl font-bold text-gray-800">Meu Painel</h1>
      <p id="painel-saudacao" class="text-gray-500 text-sm mt-1"></p>
    </div>
    <div class="flex gap-2">
      <div id="painel-admin-link" class="hidden">
        <a href="<?= BASE_PATH ?>/admin" class="bg-primary text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-blue-900 transition-colors">
          Painel Admin
        </a>
      </div>
      <div id="painel-gestor-link" class="hidden">
        <a href="<?= BASE_PATH ?>/gestor" class="bg-primary text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-blue-900 transition-colors">
          Painel do Gestor
        </a>
      </div>
    </div>
  </div>

  <!-- Tabs (Segmented Control style matching the mockup) -->
  <div class="mb-6 overflow-x-auto">
    <div class="inline-flex p-1 bg-gray-100 rounded-xl whitespace-nowrap">
      <button onclick="switchTab('cursos')" id="tab-cursos" 
        class="px-5 py-2 text-xs sm:text-sm font-bold uppercase rounded-lg transition-all focus:outline-none bg-white text-secondary shadow-sm">
        Cursos
      </button>
      <button onclick="switchTab('conteudos')" id="tab-conteudos" 
        class="px-5 py-2 text-xs sm:text-sm font-bold uppercase rounded-lg transition-all focus:outline-none text-gray-500 hover:text-gray-700">
        Conteúdos
      </button>
      <button onclick="switchTab('certificados')" id="tab-certificados" 
        class="px-5 py-2 text-xs sm:text-sm font-bold uppercase rounded-lg transition-all focus:outline-none text-gray-500 hover:text-gray-700">
        Meus certificados
      </button>
      <button onclick="switchTab('desejos')" id="tab-desejos" 
        class="px-5 py-2 text-xs sm:text-sm font-bold uppercase rounded-lg transition-all focus:outline-none text-gray-500 hover:text-gray-700">
        Lista de desejos
      </button>
    </div>
  </div>

  <!-- Barra de Filtros e Busca -->
  <div class="flex flex-col sm:flex-row gap-3 items-center justify-between mb-6" id="painel-filters">
    <!-- Busca (Lupa na direita) -->
    <div class="relative w-full sm:max-w-md">
      <input type="text" id="search-input" oninput="filtrarMatriculasLocal()" placeholder="Pesquisar..." 
        class="w-full bg-white border border-gray-300 rounded-lg pl-4 pr-10 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent transition-shadow">
      <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 pointer-events-none">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
      </span>
    </div>

    <!-- Filtros Dropdowns (Cores do padrão ActShare: bg-primary) -->
    <div class="flex gap-2 w-full sm:w-auto">
      <div class="relative w-1/2 sm:w-36">
        <select id="status-select" onchange="filtrarMatriculasLocal()" 
          class="w-full bg-primary hover:bg-[#15203b] text-white font-semibold text-xs tracking-wide uppercase px-4 py-2.5 rounded-lg appearance-none cursor-pointer focus:outline-none transition-colors pr-8">
          <option value="todos" class="bg-white text-gray-800">Status</option>
          <option value="andamento" class="bg-white text-gray-800">Em Andamento</option>
          <option value="concluido" class="bg-white text-gray-800">Concluídos</option>
        </select>
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-white">
          <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
        </div>
      </div>

      <div class="relative w-1/2 sm:w-36">
        <select id="categoria-select" onchange="filtrarMatriculasLocal()" 
          class="w-full bg-primary hover:bg-[#15203b] text-white font-semibold text-xs tracking-wide uppercase px-4 py-2.5 rounded-lg appearance-none cursor-pointer focus:outline-none transition-colors pr-8">
          <option value="todos" class="bg-white text-gray-800">Categorias</option>
          <!-- Preenchido via JS -->
        </select>
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-white">
          <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
        </div>
      </div>
    </div>
  </div>

  <!-- Meus Cursos (Lista Horizontal) -->
  <section id="aba-cursos-conteudo">
    <div id="matriculas-list" class="space-y-4">
      <div class="text-center py-12 text-gray-400">
        <div class="inline-block w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin mb-3"></div>
        <p>Carregando seus treinamentos...</p>
      </div>
    </div>
    <div id="matriculas-empty" class="hidden text-center py-12">
      <p class="text-gray-450 mb-4">Você ainda não está matriculado em nenhum treinamento.</p>
      <a href="<?= BASE_PATH ?>/cursos" class="bg-primary text-white font-medium px-6 py-2.5 rounded-lg hover:bg-blue-900 transition-colors inline-block">
        Explorar treinamentos
      </a>
    </div>
  </section>

  <!-- Outras Abas (Placeholders) -->
  <section id="aba-placeholders" class="hidden">
    <div class="bg-white border border-gray-200 rounded-2xl p-12 text-center shadow-sm">
      <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
      <h3 class="text-gray-700 font-bold text-lg mb-2" id="placeholder-titulo">Sem conteúdo</h3>
      <p class="text-sm text-gray-400" id="placeholder-desc">Nenhum item encontrado nesta seção.</p>
    </div>
  </section>
</div>

<script src="<?= BASE_PATH ?>/assets/js/painel.js?v=3"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const user = authGetUser();
    if (!user) { window.location.href = BASE + '/login'; return; }

    document.getElementById('painel-saudacao').textContent = `Bem-vindo(a), ${user.nome}`;
    if (user.role === 'admin') document.getElementById('painel-admin-link').classList.remove('hidden');
    if (user.role === 'gestor') document.getElementById('painel-gestor-link').classList.remove('hidden');

    carregarMatriculas();
  });
</script>

<?php require __DIR__ . '/layout/footer.php'; ?>
