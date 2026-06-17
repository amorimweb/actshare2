// Lógica do painel do aluno (substitui stores/student.ts)
var _B = _B || (() => (typeof BASE !== 'undefined' ? BASE : ''));

window._todasMatriculas = [];
window._abaAtiva = 'cursos';

function esc(s) {
  if (!s) return '';
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

async function carregarMatriculas() {
  const list = document.getElementById('matriculas-list');
  const empty = document.getElementById('matriculas-empty');
  if (!list) return;

  try {
    const matriculas = await apiFetch(_B() + '/api/aluno/matriculas');
    window._todasMatriculas = matriculas || [];

    // Preenche as opções de categorias no select
    await carregarCategoriasFiltro();

    renderizarPainel();
  } catch (e) {
    list.innerHTML = '<p class="text-center text-red-400 py-8">Erro ao carregar seus treinamentos.</p>';
  }
}

async function carregarCategoriasFiltro() {
  const select = document.getElementById('categoria-select');
  if (!select) return;

  try {
    const categorias = await apiFetch(_B() + '/api/categorias');
    // Limpa opções além da padrão "Categorias"
    select.innerHTML = '<option value="todos">Categorias</option>';
    categorias.forEach(cat => {
      const opt = document.createElement('option');
      opt.value = cat.id;
      opt.textContent = cat.nome;
      opt.className = "bg-white text-gray-800";
      select.appendChild(opt);
    });
  } catch (err) {
    // Fallback: extrair categorias das matrículas carregadas
    const catsMap = {};
    window._todasMatriculas.forEach(m => {
      if (m.categoria_id && m.categoria_nome) {
        catsMap[m.categoria_id] = m.categoria_nome;
      }
    });
    select.innerHTML = '<option value="todos">Categorias</option>';
    Object.keys(catsMap).forEach(id => {
      const opt = document.createElement('option');
      opt.value = id;
      opt.textContent = catsMap[id];
      opt.className = "bg-white text-gray-800";
      select.appendChild(opt);
    });
  }
}

function switchTab(tab) {
  window._abaAtiva = tab;

  // Atualiza classes das abas (Estilo Segmented Control - cor secundária)
  const tabs = ['cursos', 'conteudos', 'certificados', 'desejos'];
  tabs.forEach(t => {
    const btn = document.getElementById('tab-' + t);
    if (!btn) return;
    if (t === tab) {
      btn.className = "px-5 py-2 text-xs sm:text-sm font-bold uppercase rounded-lg transition-all focus:outline-none bg-white text-secondary shadow-sm";
    } else {
      btn.className = "px-5 py-2 text-xs sm:text-sm font-bold uppercase rounded-lg transition-all focus:outline-none text-gray-500 hover:text-gray-700";
    }
  });

  // Mostra/oculta filtros e conteúdos das abas
  const filters = document.getElementById('painel-filters');
  const mainContent = document.getElementById('aba-cursos-conteudo');
  const placeholderPanel = document.getElementById('aba-placeholders');

  if (tab === 'cursos' || tab === 'certificados') {
    if (filters) filters.classList.remove('hidden');
    if (mainContent) mainContent.classList.remove('hidden');
    if (placeholderPanel) placeholderPanel.classList.add('hidden');
    renderizarPainel();
  } else {
    if (filters) filters.classList.add('hidden');
    if (mainContent) mainContent.classList.add('hidden');
    if (placeholderPanel) placeholderPanel.classList.remove('hidden');

    const tituloEl = document.getElementById('placeholder-titulo');
    const descEl = document.getElementById('placeholder-desc');

    if (tab === 'conteudos') {
      tituloEl.textContent = 'Sem Conteúdos Complementares';
      descEl.textContent = 'Nenhum material extra ou artigo foi disponibilizado para você no momento.';
    } else if (tab === 'desejos') {
      tituloEl.textContent = 'Sua Lista de Desejos está vazia';
      descEl.textContent = 'Adicione treinamentos na lista de desejos explorando o catálogo para vê-los aqui.';
    }
  }
}

function filtrarMatriculasLocal() {
  renderizarPainel();
}

function renderizarPainel() {
  const list = document.getElementById('matriculas-list');
  const empty = document.getElementById('matriculas-empty');
  if (!list) return;

  const searchQuery = (document.getElementById('search-input')?.value || '').toLowerCase().trim();
  const statusFilter = document.getElementById('status-select')?.value || 'todos';
  const categoriaFilter = document.getElementById('categoria-select')?.value || 'todos';

  // 1. Filtragem de acordo com a aba ativa e filtros selecionados
  let filtradas = window._todasMatriculas;

  if (window._abaAtiva === 'certificados') {
    // Apenas cursos concluídos
    filtradas = filtradas.filter(m => parseInt(m.concluido) === 1);
  }

  // Filtragem por busca textual
  if (searchQuery) {
    filtradas = filtradas.filter(m => 
      (m.curso_titulo || '').toLowerCase().includes(searchQuery) || 
      (m.instrutor_nome || '').toLowerCase().includes(searchQuery)
    );
  }

  // Filtragem por status
  if (statusFilter !== 'todos') {
    if (statusFilter === 'concluido') {
      filtradas = filtradas.filter(m => parseInt(m.concluido) === 1);
    } else if (statusFilter === 'andamento') {
      filtradas = filtradas.filter(m => parseInt(m.concluido) !== 1);
    }
  }

  // Filtragem por categoria
  if (categoriaFilter !== 'todos') {
    filtradas = filtradas.filter(m => m.categoria_id == categoriaFilter);
  }

  // 2. Exibição
  if (filtradas.length === 0) {
    if (window._abaAtiva === 'certificados') {
      list.innerHTML = `
        <div class="bg-white border border-gray-200 rounded-2xl p-10 text-center shadow-sm">
          <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <h4 class="text-gray-700 font-bold text-base mb-1">Nenhum certificado disponível</h4>
          <p class="text-xs text-gray-400">Conclua seus treinamentos (100% de progresso) para emitir seus certificados nesta seção.</p>
        </div>
      `;
    } else {
      list.innerHTML = `
        <div class="bg-white border border-gray-200 rounded-2xl p-10 text-center shadow-sm">
          <p class="text-gray-400 text-sm">Nenhum treinamento encontrado com os filtros aplicados.</p>
        </div>
      `;
    }
    empty.classList.add('hidden');
    return;
  }

  empty.classList.add('hidden');

  list.innerHTML = filtradas.map(m => {
    const prog = Math.round(m.progresso_total || 0);
    
    // Formatação de data de acesso (criado_at + 1 ano caso não tenha)
    let dataFim = 'Acesso vitalício';
    if (m.data_fim_acesso) {
      const d = new Date(m.data_fim_acesso);
      dataFim = `Acesso até ${d.getDate().toString().padStart(2, '0')}/${(d.getMonth() + 1).toString().padStart(2, '0')}/${d.getFullYear()}`;
    } else {
      const d = new Date(m.created_at || new Date());
      d.setFullYear(d.getFullYear() + 1);
      dataFim = `Acesso até ${d.getDate().toString().padStart(2, '0')}/${(d.getMonth() + 1).toString().padStart(2, '0')}/${d.getFullYear()}`;
    }

    // Ação do botão conforme aba (Padrão de Cores ActShare)
    let btnText = 'CONTINUAR';
    let btnUrl = `${_B()}/painel/curso/${m.curso_id}`;
    let btnColorClass = "bg-secondary hover:bg-emerald-600";
    
    if (window._abaAtiva === 'certificados') {
      btnText = 'VER CERTIFICADO';
      btnUrl = `${_B()}/certificado?curso=${m.curso_id}`;
      btnColorClass = "bg-green-600 hover:bg-green-700";
    }

    return `
      <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm flex flex-col md:flex-row gap-5 items-center justify-between transition-shadow hover:shadow-md">
        <!-- Col 1: Imagem e Informações (Flex-1) -->
        <div class="flex flex-col sm:flex-row gap-4 items-center w-full md:flex-1">
          <!-- Thumbnail -->
          <div class="w-full sm:w-40 h-24 rounded-lg overflow-hidden flex-shrink-0 relative bg-gray-100 shadow-inner">
            ${m.thumb_url
              ? `<img src="${esc(m.thumb_url)}" alt="" class="w-full h-full object-cover">`
              : `<div class="w-full h-full bg-gradient-to-br from-[#0c1323] to-[#2c3e50] flex items-center justify-center text-white/20 font-bold text-xs uppercase">EAD</div>`
            }
          </div>

          <!-- Informações do Treinamento -->
          <div class="text-center sm:text-left flex-1 min-w-0">
            <h3 class="font-bold text-gray-800 text-sm sm:text-base leading-snug line-clamp-2 hover:text-secondary transition-colors">
              <a href="${btnUrl}">${esc(m.curso_titulo)}</a>
            </h3>
            
            <!-- Nome do Instrutor -->
            <div class="flex items-center justify-center sm:justify-start gap-1 text-xs text-gray-500 mt-1">
              <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
              <span>${esc(m.instrutor_nome || 'François André Martinot')}</span>
            </div>

            <!-- Badges (Cores válidas no Tailwind) -->
            <div class="mt-2 flex flex-wrap justify-center sm:justify-start gap-1.5 items-center">
              <!-- Status Pill -->
              ${parseInt(m.concluido) === 1
                ? '<span class="inline-block text-[10px] bg-green-50 text-green-700 border border-green-200 font-bold px-2 py-0.5 rounded-md">✓ Concluído</span>'
                : `<span class="inline-block text-[10px] ${prog > 0 ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-gray-50 text-gray-500 border border-gray-200'} font-bold px-2 py-0.5 rounded-md">${prog > 0 ? 'Em andamento' : 'Não iniciado'}</span>`
              }
              <!-- Acesso Pill -->
              <span class="inline-block text-[10px] bg-orange-50 text-orange-700 border border-orange-200 font-bold px-2 py-0.5 rounded-md">${dataFim}</span>
            </div>

            <!-- Estrelas / Avaliação (Mockada para Premium) -->
            <div class="flex items-center justify-center sm:justify-start gap-1 text-[11px] text-amber-500 mt-2">
              <span class="font-bold text-gray-800">4.9</span>
              <div class="flex text-amber-400 tracking-tighter">
                ★ ★ ★ ★ ★
              </div>
              <span class="text-gray-400 font-normal">(${100 + (m.curso_id * 17) % 150})</span>
            </div>
          </div>
        </div>

        <!-- Col 2: Progresso (w-full em mobile, md:w-44 em desktop) -->
        <div class="w-full md:w-44 flex-shrink-0">
          <div class="flex justify-between items-center text-[11px] text-gray-500 mb-1">
            <span class="font-medium">Progresso</span>
            <span class="font-bold text-gray-800">${prog}%</span>
          </div>
          <div class="w-full bg-gray-200 rounded-full h-1.5 shadow-inner relative overflow-hidden">
            <div class="bg-secondary rounded-full h-1.5 transition-all progress-bar" style="width:${prog}%"></div>
          </div>
        </div>

        <!-- Col 3: Botão de Ação (w-full em mobile, md:w-36 em desktop) -->
        <div class="w-full md:w-36 flex-shrink-0">
          <a href="${btnUrl}" class="w-full block text-center ${btnColorClass} text-white font-bold text-xs uppercase tracking-wider py-2.5 px-4 rounded-lg transition-all hover:shadow-md">
            ${btnText}
          </a>
        </div>
      </div>
    `;
  }).join('');
}
