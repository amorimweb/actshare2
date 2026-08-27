<?php
$pageTitle = 'Painel do Gestor — ActShare';
require __DIR__ . '/../layout/header.php';
?>

<div class="flex flex-col md:flex-row min-h-screen bg-slate-50 pt-16">
  <!-- Sidebar Menu (Padrão de Cores ActShare: bg-primary) -->
  <aside class="w-full md:w-64 bg-primary text-slate-300 flex-shrink-0 border-r border-slate-700/30">
    <div class="p-6">
      <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Painel Gestor</h2>
      <p class="text-[10px] text-secondary font-semibold mt-1">Área Corporativa</p>
    </div>
    
    <nav class="px-4 py-2 space-y-1">
      <button onclick="switchTab('treinamentos')" id="menu-treinamentos" 
        class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all focus:outline-none bg-slate-800 text-white shadow-inner">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        Treinamentos
      </button>

      <button onclick="switchTab('alunos')" id="menu-alunos" 
        class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all focus:outline-none hover:bg-slate-800/50 hover:text-white">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        Alunos (Relatório)
      </button>

      <button onclick="switchTab('gestores')" id="menu-gestores" 
        class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all focus:outline-none hover:bg-slate-800/50 hover:text-white">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        Gestores
      </button>

      <button onclick="switchTab('meus-treinamentos')" id="menu-meus-treinamentos" 
        class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all focus:outline-none hover:bg-slate-800/50 hover:text-white">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg>
        Meus Treinamentos
      </button>
    </nav>
    
    <div class="p-6 mt-8 border-t border-slate-700/20">
      <a href="<?= BASE_PATH ?>/painel" class="block w-full text-center bg-slate-800 hover:bg-slate-700 text-white font-semibold py-2 px-4 rounded-xl transition-colors text-xs shadow-sm">
        ← Área do Participante
      </a>
    </div>
  </aside>

  <!-- Main Content Area -->
  <main class="flex-1 p-8 min-w-0">
    
    <!-- Tab 1: Treinamentos -->
    <section id="section-treinamentos" class="space-y-6">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
        <div>
          <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Treinamentos Contratados (B2B)</h1>
          <p class="text-xs text-slate-400 mt-1">Monitore suas licenças e aloque novos alunos nos cursos comprados.</p>
        </div>
      </div>

      <div id="loading-treinamentos" class="text-center py-16 text-slate-400">
        <div class="inline-block w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin mb-3"></div>
        <p class="text-xs">Carregando treinamentos...</p>
      </div>

      <div id="treinamentos-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 hidden"></div>
      
      <p id="treinamentos-empty" class="hidden text-center py-16 text-slate-400 bg-white border border-slate-200 rounded-2xl shadow-sm">
        Nenhum treinamento B2B adquirido foi localizado para esta conta.
      </p>
    </section>

    <!-- Tab 2: Alunos -->
    <section id="section-alunos" class="space-y-6 hidden">
      <div>
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Usuários Alocados</h1>
        <p class="text-xs text-slate-400 mt-1">Acompanhe o andamento geral e visualize as avaliações de cada funcionário.</p>
      </div>

      <!-- Filtros e Busca -->
      <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex flex-col md:flex-row gap-4 items-center justify-between">
        <div class="w-full md:w-72 relative">
          <input type="text" id="search-alunos" oninput="filtrarAlunosDashboard()"
            placeholder="Buscar por nome, e-mail ou curso..."
            class="w-full pl-10 pr-4 py-2.5 bg-slate-55 border border-slate-250 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-shadow">
          <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
        <div class="text-[11px] text-slate-450 font-bold" id="alunos-count-info">0 alunos localizados</div>
      </div>

      <div id="loading-alunos" class="text-center py-16 text-slate-400">
        <div class="inline-block w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin mb-3"></div>
        <p class="text-xs">Carregando relatório dos alunos...</p>
      </div>

      <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm hidden" id="alunos-table-wrapper">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
            <thead class="bg-slate-50 text-slate-500 font-semibold uppercase tracking-wider">
              <tr>
                <th class="px-6 py-4">Nome do Aluno</th>
                <th class="px-6 py-4">Treinamento</th>
                <th class="px-6 py-4 text-center">Status</th>
                <th class="px-6 py-4 text-center">Progresso</th>
                <th class="px-6 py-4">Prazos e Datas</th>
                <th class="px-6 py-4 text-center">Certificado</th>
                <th class="px-6 py-4 text-right">Avaliação</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700" id="alunos-table-body"></tbody>
          </table>
        </div>
      </div>

      <p id="alunos-empty" class="hidden text-center py-16 text-slate-400 bg-white border border-slate-200 rounded-2xl shadow-sm">
        Nenhum participante alocado foi localizado no momento.
      </p>
    </section>

    <!-- Tab 3: Gestores -->
    <section id="section-gestores" class="space-y-6 hidden">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
        <div>
          <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Gestores Associados</h1>
          <p class="text-xs text-slate-400 mt-1">Cadastre e remova outros administradores para a sua organização.</p>
        </div>
        <button onclick="abrirModalGestor()" class="bg-secondary hover:bg-emerald-600 text-white font-bold text-xs uppercase tracking-wider px-5 py-3 rounded-xl transition-all shadow-md">
          + Novo Gestor
        </button>
      </div>

      <div id="loading-gestores" class="text-center py-16 text-slate-400">
        <div class="inline-block w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin mb-3"></div>
        <p class="text-xs">Carregando gestores...</p>
      </div>

      <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm hidden" id="gestores-table-wrapper">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
            <thead class="bg-slate-50 text-slate-500 font-semibold uppercase tracking-wider">
              <tr>
                <th class="px-6 py-4">Nome</th>
                <th class="px-6 py-4">E-mail</th>
                <th class="px-6 py-4">Data Cadastro</th>
                <th class="px-6 py-4 text-right">Ações</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700" id="gestores-table-body"></tbody>
          </table>
        </div>
      </div>

      <p id="gestores-empty" class="hidden text-center py-16 text-slate-400 bg-white border border-slate-200 rounded-2xl shadow-sm">
        Nenhum outro gestor associado.
      </p>
    </section>

    <!-- Tab 4: Meus Treinamentos -->
    <section id="section-meus-treinamentos" class="space-y-6 hidden">
      <div>
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Meus Treinamentos</h1>
        <p class="text-xs text-slate-400 mt-1">Minhas aulas e progressos individuais como aluno na plataforma.</p>
      </div>

      <div id="meus-treinamentos-status" class="bg-white border border-slate-200 rounded-2xl p-12 text-center shadow-sm">
        <div class="inline-block w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin mb-3"></div>
        <p class="text-xs text-slate-400">Verificando suas matrículas...</p>
      </div>
    </section>

  </main>
</div>

<!-- ========================================== MODALS ========================================== -->

<!-- 1. Modal Configuração Certificado Acesso Primeiro Acesso -->
<div id="modal-primeiro-acesso" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl animate-scaleUp">
    <div class="text-center mb-6">
      <div class="w-12 h-12 bg-emerald-50 text-secondary rounded-full flex items-center justify-center mb-4 mx-auto border border-emerald-150">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
      </div>
      <h3 class="font-bold text-slate-800 text-lg">Liberação dos Certificados</h3>
      <p class="text-xs text-slate-400 mt-2">Escolha abaixo como os certificados serão liberados para os seus participantes contratados:</p>
    </div>

    <form id="form-primeiro-acesso" onsubmit="salvarCertificadoAcesso(event)" class="space-y-4">
      <label class="flex items-start gap-3 p-3 bg-slate-50 hover:bg-slate-100/70 border border-slate-200 rounded-2xl cursor-pointer transition-colors">
        <input type="radio" name="primeiro_acesso_opt" value="ambos" checked class="mt-1 text-primary focus:ring-0">
        <div>
          <span class="text-xs font-bold text-slate-800 block">Empresa e Alunos</span>
          <span class="text-[10px] text-slate-400 block mt-0.5">Tanto o gestor corporativo quanto o participante podem visualizar e baixar o PDF.</span>
        </div>
      </label>

      <label class="flex items-start gap-3 p-3 bg-slate-50 hover:bg-slate-100/70 border border-slate-200 rounded-2xl cursor-pointer transition-colors">
        <input type="radio" name="primeiro_acesso_opt" value="empresa" class="mt-1 text-primary focus:ring-0">
        <div>
          <span class="text-xs font-bold text-slate-800 block">Somente a Empresa</span>
          <span class="text-[10px] text-slate-400 block mt-0.5">Apenas você/gestores poderão baixar os certificados. O aluno verá um aviso para solicitá-lo à empresa.</span>
        </div>
      </label>

      <label class="flex items-start gap-3 p-3 bg-slate-50 hover:bg-slate-100/70 border border-slate-200 rounded-2xl cursor-pointer transition-colors">
        <input type="radio" name="primeiro_acesso_opt" value="aluno" class="mt-1 text-primary focus:ring-0">
        <div>
          <span class="text-xs font-bold text-slate-800 block">Somente o Aluno</span>
          <span class="text-[10px] text-slate-400 block mt-0.5">Apenas o participante poderá gerar e fazer o download do certificado ao concluir o treinamento.</span>
        </div>
      </label>

      <button type="submit" class="w-full mt-4 bg-primary hover:bg-slate-900 text-white font-bold text-xs uppercase tracking-wider py-3.5 rounded-xl transition-all shadow-md">
        Confirmar Preferência
      </button>
    </form>
  </div>
</div>

<!-- 2. Modal Incluir Aluno B2B -->
<div id="modal-incluir-aluno" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl max-w-2xl w-full p-8 shadow-2xl relative animate-scaleUp">
    <button onclick="fecharModalAluno()" class="absolute right-5 top-5 text-slate-400 hover:text-slate-600">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>

    <div class="mb-6">
      <span class="text-[10px] font-bold text-secondary uppercase tracking-wider">Gestão de Participantes B2B</span>
      <h3 id="modal-curso-titulo" class="font-extrabold text-slate-800 text-lg mt-0.5"></h3>
      <p class="text-xs text-slate-400 mt-1" id="modal-vagas-info">Vagas: 0 / 0</p>
    </div>

    <!-- Seção: Você também é participante? -->
    <div id="modal-gestor-participa" class="hidden bg-slate-50 border border-slate-200 rounded-2xl p-4 mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h4 class="text-xs font-bold text-slate-700">Você também fará este treinamento?</h4>
        <p class="text-[10px] text-slate-400 mt-0.5">Você pode consumir 1 vaga do contrato para assistir as aulas e receber o certificado.</p>
      </div>
      <button onclick="registrarAutocadastroGestor()" id="btn-modal-autocadastro" class="bg-primary hover:bg-slate-900 text-white font-semibold text-xs px-4 py-2 rounded-xl transition-colors shrink-0">
        Quero Participar
      </button>
    </div>

    <!-- Formulário Inclusão -->
    <div id="container-form-aluno" class="mb-6">
      <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Inserir por E-mail</h4>
      <form id="form-alocar-aluno" onsubmit="alocarAlunoB2B(event)" class="flex gap-2">
        <input type="email" id="modal-email-input" required placeholder="Digite o e-mail do participante..."
          class="flex-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary/20">
        <button type="submit" id="btn-modal-incluir" class="bg-secondary hover:bg-emerald-600 text-white font-bold text-xs uppercase tracking-wider px-6 py-2.5 rounded-xl transition-colors">
          Alocar Vaga
        </button>
      </form>
      <div id="modal-aluno-alerta" class="hidden mt-2 p-3 text-xs rounded-xl border"></div>
    </div>

    <!-- Tabela Alunos Matriculados -->
    <div>
      <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2" id="modal-matriculados-title">Participantes Matriculados (0)</h4>
      <div class="border border-slate-200 rounded-2xl overflow-hidden max-h-60 overflow-y-auto shadow-inner bg-slate-50/50">
        <table class="min-w-full divide-y divide-slate-100 text-xs text-left">
          <thead class="bg-slate-50 text-slate-500 font-semibold">
            <tr>
              <th class="px-4 py-2.5">Nome / Email</th>
              <th class="px-4 py-2.5 text-center">Progresso</th>
              <th class="px-4 py-2.5 text-right">Ação</th>
            </tr>
          </thead>
          <tbody id="modal-matriculados-table-body" class="divide-y divide-slate-100 text-slate-700 bg-white">
            <tr>
              <td colspan="3" class="px-4 py-6 text-center text-slate-400">Nenhum participante matriculado.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- 3. Modal Novo Gestor -->
<div id="modal-novo-gestor" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl relative animate-scaleUp">
    <button onclick="fecharModalGestor()" class="absolute right-5 top-5 text-slate-400 hover:text-slate-600">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>

    <div class="mb-6">
      <h3 class="font-extrabold text-slate-800 text-lg">Adicionar Novo Gestor</h3>
      <p class="text-xs text-slate-400 mt-1">Cadastre um novo usuário com permissões administrativas de organização.</p>
    </div>

    <form id="form-cadastrar-gestor" onsubmit="cadastrarNovoGestor(event)" class="space-y-4">
      <div id="modal-gestor-alerta" class="hidden p-3 text-xs rounded-xl border"></div>
      
      <div>
        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Nome Completo</label>
        <input type="text" id="gestor-nome" required placeholder="Digite o nome..."
          class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary/20">
      </div>

      <div>
        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Endereço de E-mail</label>
        <input type="email" id="gestor-email" required placeholder="Ex: gestor@empresa.com"
          class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary/20">
      </div>

      <div>
        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Senha de Acesso</label>
        <input type="password" id="gestor-senha" required placeholder="Digite uma senha provisória..."
          class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary/20">
      </div>

      <label class="flex items-start gap-3 p-3 bg-slate-50 border border-slate-100 rounded-2xl cursor-pointer">
        <input type="checkbox" id="gestor-is-participante" class="mt-1 text-primary focus:ring-0">
        <div>
          <span class="text-xs font-bold text-slate-700 block">Vc é participante?</span>
          <span class="text-[10px] text-slate-400 block mt-0.5">Matricular automaticamente este gestor em todos os cursos comprados que tiverem vagas disponíveis.</span>
        </div>
      </label>

      <button type="submit" id="btn-gestor-submit" class="w-full bg-primary hover:bg-slate-900 text-white font-bold text-xs uppercase tracking-wider py-3.5 rounded-xl transition-all shadow-md">
        Cadastrar Gestor
      </button>
    </form>
  </div>
</div>

<!-- 4. Modal Relatório Resumo (Print-Friendly) -->
<div id="modal-relatorio-resumo" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4 overflow-y-auto">
  <div class="bg-white rounded-3xl max-w-4xl w-full p-8 shadow-2xl relative my-8 animate-scaleUp print:p-0 print:shadow-none print:my-0">
    <button onclick="fecharModalRelatorio()" class="absolute right-5 top-5 text-slate-400 hover:text-slate-600 print:hidden">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>

    <div class="flex items-center gap-3 mb-6 print:mb-4">
      <img src="<?= BASE_PATH ?>/assets/img/logo-act2.png" alt="ActShare" class="h-10">
      <div>
        <h3 class="font-extrabold text-slate-800 text-lg">Resumo do Treinamento Corporativo</h3>
        <p class="text-xs text-slate-400">Gerado em <span id="rel-data-geracao"></span></p>
      </div>
    </div>

    <div id="relatorio-content" class="space-y-6">
      <!-- Info Geral -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 bg-slate-50 border border-slate-200 rounded-2xl print:bg-white print:border-slate-300">
        <div>
          <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Curso</span>
          <span class="text-xs font-bold text-slate-800 block truncate" id="rel-curso-titulo"></span>
        </div>
        <div>
          <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Carga Horária</span>
          <span class="text-xs font-bold text-slate-800 block" id="rel-curso-carga"></span>
        </div>
        <div>
          <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Vagas Usadas</span>
          <span class="text-xs font-bold text-slate-800 block" id="rel-curso-vagas"></span>
        </div>
        <div>
          <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Conclusões</span>
          <span class="text-xs font-bold text-slate-800 block" id="rel-curso-conclusoes"></span>
        </div>
      </div>

      <!-- Médias -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="p-4 border border-slate-200 rounded-2xl text-center print:border-slate-300">
          <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Progresso Médio da Equipe</span>
          <span class="text-2xl font-extrabold text-primary block mt-1" id="rel-media-progresso">0%</span>
        </div>
        <div class="p-4 border border-slate-200 rounded-2xl text-center print:border-slate-300">
          <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Aproveitamento Médio (Provas)</span>
          <span class="text-2xl font-extrabold text-secondary block mt-1" id="rel-media-nota">--</span>
        </div>
      </div>

      <!-- Tabela Participantes -->
      <div>
        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Desempenho dos Alunos</h4>
        <div class="border border-slate-200 rounded-2xl overflow-hidden print:border-slate-300">
          <table class="min-w-full divide-y divide-slate-200 text-xs text-left">
            <thead class="bg-slate-50 text-slate-500 font-bold">
              <tr>
                <th class="px-4 py-2.5">Nome</th>
                <th class="px-4 py-2.5">Início</th>
                <th class="px-4 py-2.5">Conclusão</th>
                <th class="px-4 py-2.5 text-center">Progresso</th>
                <th class="px-4 py-2.5 text-center">Nota Exame</th>
              </tr>
            </thead>
            <tbody id="rel-alunos-tbody" class="divide-y divide-slate-100 text-slate-700"></tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="mt-8 flex justify-end gap-3 print:hidden">
      <button onclick="window.print()" class="bg-primary hover:bg-slate-900 text-white font-bold text-xs uppercase tracking-wider px-6 py-3 rounded-xl transition-all shadow-md">
        Imprimir Relatório
      </button>
      <button onclick="fecharModalRelatorio()" class="border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-xs uppercase tracking-wider px-6 py-3 rounded-xl transition-all">
        Fechar
      </button>
    </div>
  </div>
</div>

<!-- 5. Modal Visualizar Detalhes da Avaliação do Aluno -->
<div id="modal-ver-avaliacao" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4 overflow-y-auto">
  <div class="bg-white rounded-3xl max-w-3xl w-full p-8 shadow-2xl relative my-8 animate-scaleUp">
    <button onclick="fecharModalAvaliacao()" class="absolute right-5 top-5 text-slate-400 hover:text-slate-600">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>

    <div class="mb-6">
      <span class="text-[10px] font-bold text-primary uppercase tracking-wider block">Histórico de Exames</span>
      <h3 id="modal-av-aluno-nome" class="font-extrabold text-slate-800 text-lg mt-0.5"></h3>
      <p class="text-xs text-slate-400 mt-1" id="modal-av-aluno-email"></p>
    </div>

    <!-- Tabela Tentativas -->
    <div class="space-y-6">
      <div>
        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tentativas de Prova Realizadas</h4>
        <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-inner bg-slate-55/50">
          <table class="min-w-full divide-y divide-slate-100 text-xs text-left">
            <thead class="bg-slate-50 text-slate-500 font-semibold">
              <tr>
                <th class="px-4 py-2.5">Data / Hora</th>
                <th class="px-4 py-2.5 text-center">Acertos</th>
                <th class="px-4 py-2.5 text-center">Nota final</th>
                <th class="px-4 py-2.5 text-center">Resultado</th>
                <th class="px-4 py-2.5 text-right">Gabarito</th>
              </tr>
            </thead>
            <tbody id="modal-av-tentativas-tbody" class="divide-y divide-slate-100 text-slate-700 bg-white">
              <tr>
                <td colspan="5" class="px-4 py-6 text-center text-slate-400">Nenhum exame realizado por este participante.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Gabarito da tentativa selecionada -->
      <div id="modal-av-detalhe-gabarito" class="hidden border border-slate-200 rounded-2xl p-6 bg-slate-50 space-y-4">
        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider border-b border-slate-200 pb-2">Detalhes da Correção (Tentativa Selecionada)</h4>
        <div id="modal-av-perguntas-lista" class="space-y-4 max-h-72 overflow-y-auto pr-2"></div>
      </div>
    </div>
  </div>
</div>

<style>
@media print {
  /* Oculta tudo exceto o modal do relatório */
  body * {
    visibility: hidden;
  }
  #modal-relatorio-resumo, #modal-relatorio-resumo * {
    visibility: visible;
  }
  #modal-relatorio-resumo {
    position: absolute;
    left: 0;
    top: 0;
    margin: 0;
    border: none;
    box-shadow: none;
    width: 100%;
  }
  @page {
    size: portrait;
    margin: 1.5cm;
  }
}
</style>

<script>
  let activeTab = 'treinamentos';
  let listTreinamentos = [];
  let listAlunos = [];
  let listGestores = [];
  let selectedCursoId = 0;

  document.addEventListener('DOMContentLoaded', async () => {
    const user = authGetUser();
    if (!user || user.role !== 'gestor') { window.location.href = BASE + '/login'; return; }
    
    // Verifica primeiro acesso
    await verificarPrimeiroAcesso();

    // Carrega dados iniciais
    carregarTreinamentos();
  });

  async function verificarPrimeiroAcesso() {
    try {
      const res = await apiFetch(BASE + '/api/master/check-role');
      if (res.certificado_acesso === null) {
        document.getElementById('modal-primeiro-acesso').classList.remove('hidden');
      }
    } catch (e) {
      console.error(e);
    }
  }

  async function salvarCertificadoAcesso(event) {
    event.preventDefault();
    const opt = document.querySelector('input[name="primeiro_acesso_opt"]:checked').value;
    
    try {
      await apiPost(BASE + '/api/master/salvar-certificado-acesso', { certificado_acesso: opt });
      document.getElementById('modal-primeiro-acesso').classList.add('hidden');
    } catch(err) {
      alert(err.message || 'Erro ao salvar preferência.');
    }
  }

  function switchTab(tab) {
    activeTab = tab;
    
    // Oculta todas as seções
    document.getElementById('section-treinamentos').classList.add('hidden');
    document.getElementById('section-alunos').classList.add('hidden');
    document.getElementById('section-gestores').classList.add('hidden');
    document.getElementById('section-meus-treinamentos').classList.add('hidden');

    // Desativa botões da sidebar
    const items = ['treinamentos', 'alunos', 'gestores', 'meus-treinamentos'];
    items.forEach(item => {
      const el = document.getElementById('menu-' + item);
      if (el) {
        el.className = "w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all focus:outline-none hover:bg-slate-800/50 hover:text-white text-slate-300";
      }
    });

    // Ativa botão atual e mostra seção correspondente
    document.getElementById('section-' + tab).classList.remove('hidden');
    const activeBtn = document.getElementById('menu-' + tab);
    if (activeBtn) {
      activeBtn.className = "w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all focus:outline-none bg-slate-800 text-white shadow-inner";
    }

    if (tab === 'treinamentos') {
      carregarTreinamentos();
    } else if (tab === 'alunos') {
      carregarAlunos();
    } else if (tab === 'gestores') {
      carregarGestores();
    } else if (tab === 'meus-treinamentos') {
      verificarMeusTreinamentos();
    }
  }

  // ========================================== TAB TREINAMENTOS ==========================================
  async function carregarTreinamentos() {
    const grid = document.getElementById('treinamentos-grid');
    const loading = document.getElementById('loading-treinamentos');
    const empty = document.getElementById('treinamentos-empty');
    
    try {
      listTreinamentos = await apiFetch(BASE + '/api/master/cursos');
      loading.classList.add('hidden');
      
      if (listTreinamentos.length === 0) {
        empty.classList.remove('hidden');
        grid.classList.add('hidden');
        return;
      }
      
      empty.classList.add('hidden');
      grid.classList.remove('hidden');
      
      grid.innerHTML = listTreinamentos.map(prod => {
        const dataCompra = new Date(prod.data_compra);
        // Prazo de alocação de 45 dias
        const dataLimite = new Date(dataCompra.getTime() + (45 * 24 * 3600 * 1000));
        const diffTime = dataLimite.getTime() - new Date().getTime();
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        let prazoHtml = '';
        if (diffDays <= 0) {
          prazoHtml = `<span class="text-[10px] font-bold text-red-500 uppercase">Prazo de alocação expirado</span>`;
        } else {
          prazoHtml = `<span class="text-[10px] font-bold text-orange-500 uppercase">Alocar em até ${diffDays} dias</span>`;
        }

        const pct = Math.round((prod.vagas_usadas / prod.vagas_totais) * 100);

        return `
          <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm flex flex-col hover:shadow-md transition-shadow">
            ${prod.thumb_url
              ? `<img src="${prod.thumb_url}" alt="" class="w-full h-40 object-cover">`
              : `<div class="w-full h-40 bg-gradient-to-br from-primary to-slate-800 flex items-center justify-center text-white/10 font-bold">ActShare B2B</div>`
            }
            <div class="p-5 flex-1 flex flex-col justify-between">
              <div>
                <h3 class="font-bold text-slate-800 text-sm mb-1 leading-snug line-clamp-2">${prod.curso_titulo}</h3>
                <span class="text-[10px] font-bold text-slate-400">Adquirido em ${dataCompra.toLocaleDateString('pt-BR')}</span>
                
                <div class="space-y-1 mb-6 mt-4">
                  <div class="flex justify-between text-[11px] text-slate-500">
                    <span>Vagas Utilizadas</span>
                    <span class="font-bold">${prod.vagas_usadas} / ${prod.vagas_totais}</span>
                  </div>
                  <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-secondary rounded-full h-1.5 transition-all" style="width: ${pct}%"></div>
                  </div>
                  <div class="pt-2">${prazoHtml}</div>
                </div>
              </div>
              
              <div class="grid grid-cols-2 gap-2 mt-auto">
                <button onclick="abrirModalAluno(${prod.curso_id})" 
                  class="bg-primary hover:bg-slate-900 text-white font-bold text-[10px] uppercase py-2.5 rounded-xl transition-colors shadow-sm">
                  Alocar Vagas
                </button>
                <button onclick="abrirModalRelatorio(${prod.curso_id})" 
                  class="border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-[10px] uppercase py-2.5 rounded-xl transition-colors">
                  Relatório Resumo
                </button>
              </div>
            </div>
          </div>
        `;
      }).join('');
      
    } catch (err) {
      loading.innerHTML = `<p class="text-red-500 py-8">Erro: ${err.message}</p>`;
    }
  }

  // ========================================== TAB ALUNOS ==========================================
  async function carregarAlunos() {
    const tableWrapper = document.getElementById('alunos-table-wrapper');
    const loading = document.getElementById('loading-alunos');
    const empty = document.getElementById('alunos-empty');
    
    try {
      const rawAlunos = await apiFetch(BASE + '/api/master/alunos');
      loading.classList.add('hidden');
      
      let tableData = [];
      rawAlunos.forEach(aluno => {
        if (aluno.matriculas && aluno.matriculas.length > 0) {
          aluno.matriculas.forEach(mat => {
            tableData.push({
              aluno_id: aluno.id,
              aluno_nome: aluno.nome,
              aluno_email: aluno.email,
              matricula_id: mat.id,
              curso_id: mat.curso_id,
              curso_titulo: mat.curso_titulo,
              progresso: mat.progresso_total,
              concluido: mat.concluido,
              data_conclusao: mat.data_conclusao,
              data_inicio: mat.created_at || mat.data_inicio,
              data_fim: mat.data_fim_acesso,
              exam_aprovado: mat.exam_aprovado
            });
          });
        }
      });
      
      listAlunos = tableData;
      renderizarTabelaAlunos(listAlunos);
      
    } catch (err) {
      loading.innerHTML = `<p class="text-red-500 py-8">Erro: ${err.message}</p>`;
    }
  }

  function renderizarTabelaAlunos(data) {
    const tableWrapper = document.getElementById('alunos-table-wrapper');
    const empty = document.getElementById('alunos-empty');
    const tbody = document.getElementById('alunos-table-body');
    const countEl = document.getElementById('alunos-count-info');
    
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
      const dataFimVal = row.data_fim ? new Date(row.data_fim) : null;
      const dataFimTxt = dataFimVal ? dataFimVal.toLocaleDateString('pt-BR') : 'N/A';
      const dataConclusao = row.data_conclusao ? new Date(row.data_conclusao).toLocaleDateString('pt-BR') : '--';
      
      // Mapeamento de status badge
      let statusHtml = '';
      const isExpired = dataFimVal && dataFimVal < new Date();
      
      if (isExpired && parseInt(row.concluido) === 0) {
        statusHtml = `<span class="inline-block text-[9px] bg-red-50 text-red-600 border border-red-200 font-bold px-2 py-0.5 rounded-md uppercase">Prazo Vencido</span>`;
      } else if (parseInt(row.concluido) === 1) {
        if (row.exam_aprovado === 1) {
          statusHtml = `<span class="inline-block text-[9px] bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold px-2 py-0.5 rounded-md uppercase">Aprovado (Exame)</span>`;
        } else if (row.exam_aprovado === 0) {
          statusHtml = `<span class="inline-block text-[9px] bg-red-50 text-red-700 border border-red-200 font-bold px-2 py-0.5 rounded-md uppercase">Reprovado (Exame)</span>`;
        } else {
          statusHtml = `<span class="inline-block text-[9px] bg-green-50 text-green-700 border border-green-200 font-bold px-2 py-0.5 rounded-md uppercase">Concluído</span>`;
        }
      } else if (prog > 0) {
        statusHtml = `<span class="inline-block text-[9px] bg-blue-50 text-blue-700 border border-blue-200 font-bold px-2 py-0.5 rounded-md uppercase">Em Andamento</span>`;
      } else {
        statusHtml = `<span class="inline-block text-[9px] bg-slate-50 text-slate-400 border border-slate-200 font-bold px-2 py-0.5 rounded-md uppercase">Não Iniciou</span>`;
      }

      return `
        <tr class="hover:bg-slate-50/50">
          <td class="px-6 py-4">
            <div class="font-bold text-slate-800">${row.aluno_nome}</div>
            <div class="text-[10px] text-slate-400">${row.aluno_email}</div>
          </td>
          <td class="px-6 py-4 max-w-xs truncate font-medium text-slate-700">
            ${row.curso_titulo}
          </td>
          <td class="px-6 py-4 text-center">
            ${statusHtml}
          </td>
          <td class="px-6 py-4">
            <div class="flex flex-col items-center">
              <span class="font-bold text-slate-650 text-[10px] mb-0.5">${prog}%</span>
              <div class="w-14 bg-slate-100 rounded-full h-1 overflow-hidden">
                <div class="bg-secondary rounded-full h-1" style="width: ${prog}%"></div>
              </div>
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-[10px] text-slate-500">
            <div>Início: ${dataInicio}</div>
            <div>Término: ${dataConclusao}</div>
            <div class="text-orange-500 font-medium">Prazo: ${dataFimTxt}</div>
          </td>
          <td class="px-6 py-4 text-center whitespace-nowrap">
            ${parseInt(row.concluido) === 1
              ? `<a href="${BASE}/certificado?curso=${row.curso_id}&aluno_id=${row.aluno_id}" target="_blank" 
                   class="inline-flex items-center gap-1 text-[10px] text-secondary font-bold hover:underline">
                  <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"/></svg>
                  Ver PDF
                 </a>`
              : `<span class="text-[10px] text-slate-400">Pendente</span>`
            }
          </td>
          <td class="px-6 py-4 text-right whitespace-nowrap">
            <button onclick="abrirModalAvaliacao(${row.aluno_id}, ${row.curso_id}, '${row.aluno_nome}', '${row.aluno_email}')"
              class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[10px] px-3.5 py-1.5 rounded-lg transition-colors border border-slate-200">
              Ver Provas
            </button>
          </td>
        </tr>
      `;
    }).join('');
  }

  function filtrarAlunosDashboard() {
    const query = document.getElementById('search-alunos').value.toLowerCase().trim();
    if (!listAlunos) return;
    
    const filtrados = listAlunos.filter(row => 
      row.aluno_nome.toLowerCase().includes(query) || 
      row.aluno_email.toLowerCase().includes(query) || 
      row.curso_titulo.toLowerCase().includes(query)
    );
    
    renderizarTabelaAlunos(filtrados);
  }

  // ========================================== TAB GESTORES ==========================================
  async function carregarGestores() {
    const tableWrapper = document.getElementById('gestores-table-wrapper');
    const loading = document.getElementById('loading-gestores');
    const empty = document.getElementById('gestores-empty');
    
    try {
      const rawAlunos = await apiFetch(BASE + '/api/master/alunos');
      loading.classList.add('hidden');
      
      // Filtra apenas usuários com role gestor
      listGestores = rawAlunos.filter(u => u.role === 'gestor');
      
      if (listGestores.length === 0) {
        tableWrapper.classList.add('hidden');
        empty.classList.remove('hidden');
        return;
      }
      
      empty.classList.add('hidden');
      tableWrapper.classList.remove('hidden');
      
      const tbody = document.getElementById('gestores-table-body');
      tbody.innerHTML = listGestores.map(g => {
        const dtCadastro = new Date(g.created_at).toLocaleDateString('pt-BR');
        
        return `
          <tr class="hover:bg-slate-50/50">
            <td class="px-6 py-4 font-bold text-slate-800">
              ${g.nome}
            </td>
            <td class="px-6 py-4 text-slate-600">
              ${g.email}
            </td>
            <td class="px-6 py-4 text-slate-400">
              ${dtCadastro}
            </td>
            <td class="px-6 py-4 text-right">
              <button onclick="removerGestor(${g.id})" class="text-red-500 hover:text-red-700 font-bold text-[10px] uppercase">
                Excluir
              </button>
            </td>
          </tr>
        `;
      }).join('');
      
    } catch (err) {
      loading.innerHTML = `<p class="text-red-500 py-8">Erro: ${err.message}</p>`;
    }
  }

  async function removerGestor(id) {
    if (!confirm('Deseja realmente remover este gestor corporativo?')) return;
    try {
      await apiDelete(BASE + '/api/master/aluno/' + id);
      carregarGestores();
    } catch(err) {
      alert(err.message || 'Erro ao remover gestor.');
    }
  }

  function abrirModalGestor() {
    document.getElementById('gestor-nome').value = '';
    document.getElementById('gestor-email').value = '';
    document.getElementById('gestor-senha').value = '';
    document.getElementById('gestor-is-participante').checked = false;
    document.getElementById('modal-gestor-alerta').className = 'hidden p-3 text-xs rounded-xl border';
    document.getElementById('modal-novo-gestor').classList.remove('hidden');
  }

  function fecharModalGestor() {
    document.getElementById('modal-novo-gestor').classList.add('hidden');
  }

  async function cadastrarNovoGestor(event) {
    event.preventDefault();
    const btn = document.getElementById('btn-gestor-submit');
    const alerta = document.getElementById('modal-gestor-alerta');
    
    btn.disabled = true;
    btn.textContent = 'Gravando...';
    alerta.classList.add('hidden');

    const nome = document.getElementById('gestor-nome').value.trim();
    const email = document.getElementById('gestor-email').value.trim();
    const password = document.getElementById('gestor-senha').value;
    const isParticipante = document.getElementById('gestor-is-participante').checked;

    try {
      await apiPost(BASE + '/api/master/aluno', { nome, email, password, role: 'gestor', is_participante: isParticipante });
      
      alerta.className = 'p-3 text-xs rounded-xl border bg-green-50 border-green-200 text-green-700 block';
      alerta.textContent = 'Gestor corporativo cadastrado com sucesso!';
      
      setTimeout(() => {
        fecharModalGestor();
        carregarGestores();
      }, 1500);

    } catch (err) {
      alerta.className = 'p-3 text-xs rounded-xl border bg-red-50 border-red-200 text-red-700 block';
      alerta.textContent = err.message || 'Erro ao criar gestor.';
    } finally {
      btn.disabled = false;
      btn.textContent = 'Cadastrar Gestor';
    }
  }

  // ========================================== TAB MEUS TREINAMENTOS ==========================================
  async function verificarMeusTreinamentos() {
    const container = document.getElementById('meus-treinamentos-status');
    try {
      const matriculas = await apiFetch(BASE + '/api/aluno/matriculas');
      if (matriculas && matriculas.length > 0) {
        window.location.href = BASE + '/painel';
      } else {
        container.innerHTML = `
          <div class="w-16 h-16 bg-slate-50 text-slate-350 rounded-full flex items-center justify-center mb-4 mx-auto border border-slate-200">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
          </div>
          <h3 class="text-slate-700 font-bold text-base mb-1">Você não está matriculado em nenhum curso</h3>
          <p class="text-xs text-slate-400 leading-relaxed max-w-sm mx-auto">
            Para se autocadastrar e realizar as aulas, acesse a aba "Treinamentos" e clique em "Alocar Vagas" > "Quero Participar" no curso correspondente.
          </p>
        `;
      }
    } catch (e) {
      container.innerHTML = `<p class="text-red-550 text-xs">Erro ao verificar treinamentos.</p>`;
    }
  }

  // ========================================== MODAL INCLUIR ALUNO B2B ==========================================
  async function abrirModalAluno(cursoId) {
    selectedCursoId = cursoId;
    document.getElementById('modal-email-input').value = '';
    document.getElementById('modal-aluno-alerta').className = 'hidden mt-2 p-3 text-xs rounded-xl border';
    
    const cursoObj = listTreinamentos.find(c => c.curso_id === cursoId);
    if (!cursoObj) return;

    document.getElementById('modal-curso-titulo').textContent = cursoObj.curso_titulo;
    document.getElementById('modal-vagas-info').textContent = `Vagas Utilizadas: ${cursoObj.vagas_usadas} / ${cursoObj.vagas_totais}`;

    // Painel de autocadastro do gestor
    const isParticipante = parseInt(cursoObj.participante) === 1;
    const autocadastroPanel = document.getElementById('modal-gestor-participa');
    if (isParticipante) {
      autocadastroPanel.classList.add('hidden');
    } else {
      if (parseInt(cursoObj.vagas_usadas) < parseInt(cursoObj.vagas_totais)) {
        autocadastroPanel.classList.remove('hidden');
      } else {
        autocadastroPanel.classList.add('hidden');
      }
    }

    // Carrega matriculados
    await atualizarListaParticipantesModal();

    document.getElementById('modal-incluir-aluno').classList.remove('hidden');
  }

  function fecharModalAluno() {
    document.getElementById('modal-incluir-aluno').classList.add('hidden');
  }

  async function atualizarListaParticipantesModal() {
    const tbody = document.getElementById('modal-matriculados-table-body');
    const title = document.getElementById('modal-matriculados-title');
    
    try {
      const participantes = await apiFetch(BASE + `/api/master/cursos/${selectedCursoId}/participantes`);
      title.textContent = `Participantes Matriculados (${participantes.length})`;

      if (participantes.length === 0) {
        tbody.innerHTML = `
          <tr>
            <td colspan="3" class="px-4 py-6 text-center text-slate-400">Nenhum participante matriculado neste curso.</td>
          </tr>
        `;
        return;
      }

      tbody.innerHTML = participantes.map(p => {
        const prog = Math.round(p.progresso_total || 0);
        // Pode remover apenas se o progresso for 0 e não for o próprio gestor
        const canDelete = prog === 0 && !p.is_gestor_self;

        return `
          <tr class="hover:bg-slate-50/50">
            <td class="px-4 py-2.5">
              <div class="font-bold text-slate-800">${p.nome}</div>
              <div class="text-[10px] text-slate-400">${p.email}</div>
            </td>
            <td class="px-4 py-2.5 text-center font-bold text-slate-650">${prog}%</td>
            <td class="px-4 py-2.5 text-right whitespace-nowrap">
              ${canDelete
                ? `<button onclick="removerAlunoB2B(${p.id})" class="text-red-500 hover:text-red-700 font-bold">Remover</button>`
                : p.is_gestor_self
                  ? `<span class="text-primary font-bold">Você</span>`
                  : `<span class="text-slate-400 cursor-not-allowed" title="Aluno já iniciou o curso">Bloqueado</span>`
              }
            </td>
          </tr>
        `;
      }).join('');
    } catch(err) {
      tbody.innerHTML = `<tr><td colspan="3" class="px-4 py-6 text-center text-red-550">Erro ao carregar participantes.</td></tr>`;
    }
  }

  async function alocarAlunoB2B(event) {
    event.preventDefault();
    const btn = document.getElementById('btn-modal-incluir');
    const alerta = document.getElementById('modal-aluno-alerta');
    const emailInput = document.getElementById('modal-email-input');

    btn.disabled = true;
    btn.textContent = 'Gravando...';
    alerta.classList.add('hidden');

    const email = emailInput.value.trim();

    try {
      await apiPost(BASE + `/api/master/cursos/${selectedCursoId}/participante`, { email });
      
      alerta.className = 'mt-2 p-3 text-xs rounded-xl border bg-green-50 border-green-200 text-green-700 block';
      alerta.textContent = 'Participante alocado com sucesso! Senha padrão temporária: actshare123';
      
      emailInput.value = '';
      await carregarTreinamentos(); // Recarrega vagas
      
      // Atualiza modal
      const cursoObj = listTreinamentos.find(c => c.curso_id === selectedCursoId);
      document.getElementById('modal-vagas-info').textContent = `Vagas Utilizadas: ${cursoObj.vagas_usadas} / ${cursoObj.vagas_totais}`;
      
      await atualizarListaParticipantesModal();

    } catch (err) {
      alerta.className = 'mt-2 p-3 text-xs rounded-xl border bg-red-50 border-red-200 text-red-700 block';
      alerta.textContent = err.message || 'Erro ao adicionar aluno.';
    } finally {
      btn.disabled = false;
      btn.textContent = 'Alocar Vaga';
    }
  }

  async function removerAlunoB2B(alunoId) {
    if (!confirm('Deseja realmente remover este participante e liberar a vaga dele?')) return;
    try {
      await apiDelete(BASE + `/api/master/cursos/${selectedCursoId}/participante/${alunoId}`);
      await carregarTreinamentos();
      
      // Atualiza modal
      const cursoObj = listTreinamentos.find(c => c.curso_id === selectedCursoId);
      document.getElementById('modal-vagas-info').textContent = `Vagas Utilizadas: ${cursoObj.vagas_usadas} / ${cursoObj.vagas_totais}`;
      
      await atualizarListaParticipantesModal();
    } catch(err) {
      alert(err.message || 'Erro ao remover aluno.');
    }
  }

  async function registrarAutocadastroGestor() {
    const btn = document.getElementById('btn-modal-autocadastro');
    btn.disabled = true;
    btn.textContent = 'Gravando...';
    
    try {
      await apiPost(BASE + `/api/master/cursos/${selectedCursoId}/participar`);
      await carregarTreinamentos();
      
      // Esconde painel
      document.getElementById('modal-gestor-participa').classList.add('hidden');
      
      const cursoObj = listTreinamentos.find(c => c.curso_id === selectedCursoId);
      document.getElementById('modal-vagas-info').textContent = `Vagas Utilizadas: ${cursoObj.vagas_usadas} / ${cursoObj.vagas_totais}`;

      await atualizarListaParticipantesModal();
    } catch(err) {
      alert(err.message || 'Erro ao registrar autocadastro.');
    } finally {
      btn.disabled = false;
      btn.textContent = 'Quero Participar';
    }
  }

  // ========================================== MODAL RELATÓRIO RESUMO ==========================================
  async function abrirModalRelatorio(cursoId) {
    document.getElementById('rel-data-geracao').textContent = new Date().toLocaleDateString('pt-BR', {hour: '2-digit', minute: '2-digit'});
    
    try {
      const data = await apiFetch(BASE + `/api/master/relatorio-curso/${cursoId}`);
      
      document.getElementById('rel-curso-titulo').textContent = data.curso_titulo;
      document.getElementById('rel-curso-carga').textContent = data.carga_horaria + ' horas';
      document.getElementById('rel-curso-vagas').textContent = `${data.vagas_usadas} / ${data.vagas_totais}`;
      document.getElementById('rel-curso-conclusoes').textContent = `${data.participantes_concluidos} / ${data.participantes_total}`;
      
      document.getElementById('rel-media-progresso').textContent = data.media_progresso + '%';
      document.getElementById('rel-media-nota').textContent = data.media_nota !== null ? data.media_nota + '%' : '--';

      const tbody = document.getElementById('rel-alunos-tbody');
      if (data.alunos.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" class="px-4 py-6 text-center text-slate-400">Nenhum participante alocado neste curso.</td></tr>`;
      } else {
        tbody.innerHTML = data.alunos.map(al => {
          const dtInicio = al.data_inicio ? new Date(al.data_inicio).toLocaleDateString('pt-BR') : '--';
          const dtFim = al.data_conclusao ? new Date(al.data_conclusao).toLocaleDateString('pt-BR') : 'Em andamento';
          const nota = al.nota_exame !== null ? al.nota_exame + '%' : '--';
          
          return `
            <tr class="hover:bg-slate-50/50">
              <td class="px-4 py-2.5">
                <div class="font-bold text-slate-800">${al.nome}</div>
                <div class="text-[10px] text-slate-400">${al.email}</div>
              </td>
              <td class="px-4 py-2.5 text-slate-650">${dtInicio}</td>
              <td class="px-4 py-2.5 text-slate-650 font-medium">${dtFim}</td>
              <td class="px-4 py-2.5 text-center font-bold text-slate-700">${Math.round(al.progresso_total)}%</td>
              <td class="px-4 py-2.5 text-center font-bold text-slate-700">${nota}</td>
            </tr>
          `;
        }).join('');
      }

      document.getElementById('modal-relatorio-resumo').classList.remove('hidden');

    } catch (e) {
      alert('Erro ao carregar relatório: ' + e.message);
    }
  }

  function fecharModalRelatorio() {
    document.getElementById('modal-relatorio-resumo').classList.add('hidden');
  }

  // ========================================== MODAL DETALHE AVALIAÇÃO ==========================================
  async function abrirModalAvaliacao(alunoId, cursoId, nome, email) {
    document.getElementById('modal-av-aluno-nome').textContent = nome;
    document.getElementById('modal-av-aluno-email').textContent = email;
    
    // Reseta gabarito
    document.getElementById('modal-av-detalhe-gabarito').classList.add('hidden');
    document.getElementById('modal-av-perguntas-lista').innerHTML = '';

    const tbody = document.getElementById('modal-av-tentativas-tbody');
    tbody.innerHTML = `<tr><td colspan="5" class="px-4 py-6 text-center text-slate-400">Carregando histórico...</td></tr>`;

    try {
      const data = await apiFetch(BASE + `/api/master/alunos/${alunoId}/cursos/${cursoId}/avaliacoes`);
      
      if (data.tentativas.length === 0) {
        tbody.innerHTML = `
          <tr>
            <td colspan="5" class="px-4 py-6 text-center text-slate-400">Nenhum exame detalhado realizado por este participante.</td>
          </tr>
        `;
      } else {
        tbody.innerHTML = data.tentativas.map((t, idx) => {
          const num = data.tentativas.length - idx;
          const dt = new Date(t.created_at).toLocaleDateString('pt-BR', {hour: '2-digit', minute: '2-digit'});
          const resultadoHtml = t.resultado === 'aprovado'
            ? `<span class="bg-green-50 border border-green-200 text-green-700 font-bold px-2 py-0.5 rounded text-[10px]">APROVADO</span>`
            : `<span class="bg-red-50 border border-red-200 text-red-600 font-bold px-2 py-0.5 rounded text-[10px]">REPROVADO</span>`;

          return `
            <tr class="hover:bg-slate-50/50">
              <td class="px-4 py-3">
                <span class="font-bold text-slate-800 block">Tentativa #${num}</span>
                <span class="text-[9px] text-slate-400">${dt}</span>
              </td>
              <td class="px-4 py-3 text-center text-slate-650">
                ${t.acertos} acertos de ${t.total_questoes}
              </td>
              <td class="px-4 py-3 text-center font-bold text-slate-700">
                ${t.nota}%
              </td>
              <td class="px-4 py-3 text-center">
                ${resultadoHtml}
              </td>
              <td class="px-4 py-3 text-right">
                <button onclick='renderizarGabaritoTentativa(${t.id}, ${JSON.stringify(t.respostas).replace(/'/g, "&apos;")})'
                  class="bg-primary hover:bg-slate-900 text-white font-semibold text-[9px] px-3 py-1 rounded-md transition-colors uppercase">
                  Ver Respostas
                </button>
              </td>
            </tr>
          `;
        }).join('');
      }

      document.getElementById('modal-ver-avaliacao').classList.remove('hidden');

    } catch (e) {
      tbody.innerHTML = `<tr><td colspan="5" class="px-4 py-6 text-center text-red-550">Erro ao carregar: ${e.message}</td></tr>`;
    }
  }

  function renderizarGabaritoTentativa(tentativaId, respostas) {
    const box = document.getElementById('modal-av-detalhe-gabarito');
    const container = document.getElementById('modal-av-perguntas-lista');
    
    box.classList.remove('hidden');
    container.innerHTML = '';

    if (!respostas || respostas.length === 0) {
      container.innerHTML = '<p class="text-xs text-slate-400">Gabarito detalhado indisponível para esta tentativa.</p>';
      return;
    }

    container.innerHTML = respostas.map((resp, idx) => {
      const correctBadge = resp.acertou
        ? `<span class="bg-green-50 text-green-700 border border-green-200 text-[9px] font-bold px-1.5 py-0.5 rounded">Correta</span>`
        : `<span class="bg-red-50 text-red-600 border border-red-200 text-[9px] font-bold px-1.5 py-0.5 rounded">Incorreta</span>`;

      return `
        <div class="p-3.5 bg-white border border-slate-200 rounded-xl space-y-2">
          <div class="flex justify-between items-start gap-2">
            <span class="text-xs font-bold text-slate-800">${idx + 1}. ${resp.texto_pergunta}</span>
            ${correctBadge}
          </div>
          <div class="text-[11px] space-y-1 text-slate-650">
            <div><span class="font-semibold text-slate-500">Respostas Marcadas:</span> ${(resp.opcoes_escolhidas && resp.opcoes_escolhidas.length) ? resp.opcoes_escolhidas.length + ' alternativa(s)' : 'Sem resposta/Tempo esgotado'}</div>
            <div class="text-emerald-700"><span class="font-semibold text-slate-500">Alternativa(s) Correta(s):</span> ${(resp.textos_corretos || []).join('; ') || resp.texto_correta || '—'}</div>
          </div>
          ${resp.justificativa ? `
          <div class="text-[10px] text-slate-400 bg-slate-50 p-2 rounded-lg italic">
            <strong>Justificativa:</strong> ${resp.justificativa}
          </div>` : ''}
        </div>
      `;
    }).join('');
  }

  function fecharModalAvaliacao() {
    document.getElementById('modal-ver-avaliacao').classList.add('hidden');
  }
</script>
