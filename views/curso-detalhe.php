<?php $pageTitle = 'Curso — ActShare'; ?>
<?php require __DIR__ . '/layout/header.php'; ?>

<div id="curso-loading" class="max-w-5xl mx-auto px-4 py-16 text-center text-gray-400">
  <div class="inline-block w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin mb-3"></div>
  <p>Carregando curso...</p>
</div>

<div id="curso-content" class="hidden max-w-5xl mx-auto px-4 py-10">
  <!-- Hero do curso -->
  <div class="grid lg:grid-cols-3 gap-8 mb-10">
    <div class="lg:col-span-2">
      <div id="curso-categoria" class="text-sm text-secondary font-medium mb-2"></div>
      <h1 id="curso-titulo" class="text-3xl font-bold text-gray-800 mb-4"></h1>
      <p id="curso-descricao" class="text-gray-600 mb-6"></p>
      <div class="flex flex-wrap gap-4 text-sm text-gray-500">
        <span id="curso-carga" class="flex items-center gap-1">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </span>
        <span id="curso-instrutor" class="flex items-center gap-1">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        </span>
      </div>
    </div>

    <!-- Card lateral -->
    <div class="bg-white border border-gray-200 rounded-xl p-6 h-fit sticky top-24">
      <img id="curso-thumb" src="" alt="" class="w-full rounded-lg mb-4 hidden">
      <div id="curso-preco" class="text-3xl font-bold text-primary mb-4"></div>
      
      <!-- Exame Exemplar Global (opcional, pode marcar mais de um) -->
      <div id="opcoes-prova-container" class="hidden space-y-2 mb-6 border-t border-gray-150 pt-4">
        <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wide mb-1">Exame Exemplar Global (opcional)</span>
        <div id="exames-opcoes-list" class="space-y-2"></div>
      </div>

      <button id="btn-matricular" onclick="matricular()"
        class="w-full bg-secondary text-white font-semibold py-3 rounded-lg hover:bg-green-600 transition-colors disabled:opacity-60">
        Matricular-se
      </button>
      <button id="btn-adicionar-carrinho" onclick="adicionarAoCarrinho()" class="hidden w-full bg-secondary text-white font-semibold py-3 rounded-lg hover:bg-green-600 transition-colors mt-2">
        Adicionar ao Carrinho
      </button>
      <button id="btn-acessar" onclick="acessarCurso()" class="hidden w-full bg-primary text-white font-semibold py-3 rounded-lg hover:bg-blue-900 transition-colors mt-2">
        Acessar Curso
      </button>
      <p id="matricula-msg" class="hidden text-center text-sm text-green-600 mt-2 font-medium"></p>
    </div>
  </div>

  <!-- Vídeo explicativo -->
  <div id="curso-video-bloco" class="hidden mb-10">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Vídeo Explicativo</h2>
    <div class="aspect-video rounded-xl overflow-hidden bg-black">
      <iframe id="curso-video-frame" class="w-full h-full" src="" frameborder="0" allowfullscreen></iframe>
    </div>
  </div>

  <!-- Diferencial -->
  <div id="curso-diferencial-bloco" class="hidden mb-10">
    <h2 class="text-xl font-bold text-gray-800 mb-3">Diferencial do Treinamento</h2>
    <p id="curso-diferencial-texto" class="text-gray-600 whitespace-pre-line"></p>
  </div>

  <!-- Módulos -->
  <div>
    <h2 class="text-xl font-bold text-gray-800 mb-4">Conteúdo do Curso</h2>
    <div id="modulos-list" class="space-y-3"></div>
  </div>

  <!-- Conteúdo programático -->
  <div id="curso-conteudo-bloco" class="hidden mt-10">
    <h2 class="text-xl font-bold text-gray-800 mb-3">Conteúdo Programático</h2>
    <p id="curso-conteudo-texto" class="text-gray-600 whitespace-pre-line"></p>
  </div>

  <!-- Público alvo -->
  <div id="curso-publico-alvo-bloco" class="hidden mt-10">
    <h2 class="text-xl font-bold text-gray-800 mb-3">Público Alvo</h2>
    <p id="curso-publico-alvo-texto" class="text-gray-600 whitespace-pre-line"></p>
  </div>

  <!-- Demais condições -->
  <div id="curso-condicoes-bloco" class="hidden mt-10">
    <h2 class="text-xl font-bold text-gray-800 mb-3">Demais Condições</h2>
    <p id="curso-condicoes-texto" class="text-gray-600 whitespace-pre-line"></p>
  </div>
</div>

<div id="curso-error" class="hidden max-w-xl mx-auto px-4 py-20 text-center">
  <p class="text-gray-500">Curso não encontrado.</p>
  <a href="<?= BASE_PATH ?>/cursos" class="mt-4 inline-block text-primary hover:underline">← Voltar aos cursos</a>
</div>

<script>
  const cursoId = <?= (int)($_GET['id'] ?? 0) ?>;
  let cursoData = null;

  async function carregarCurso() {
    try {
      cursoData = await apiFetch(BASE + '/api/cursos/' + cursoId);
      renderCurso();
      verificarMatricula();
    } catch {
      document.getElementById('curso-loading').classList.add('hidden');
      document.getElementById('curso-error').classList.remove('hidden');
    }
  }

  function renderCurso() {
    document.getElementById('curso-loading').classList.add('hidden');
    document.getElementById('curso-content').classList.remove('hidden');

    // Toggles de visibilidade (0/vazio = oculto). Ausência do campo (cursos
    // antigos sem a migration aplicada) é tratada como visível por padrão.
    const visivel = (campo) => cursoData[campo] === undefined || cursoData[campo] == 1;

    document.getElementById('curso-titulo').textContent     = visivel('vis_nome') ? cursoData.titulo : '';
    document.getElementById('curso-descricao').textContent  = visivel('vis_breve_descricao') ? (cursoData.descricao || '') : '';
    document.getElementById('curso-categoria').textContent  = cursoData.categoria?.nome || '';
    if (visivel('vis_carga_horaria')) document.getElementById('curso-carga').innerHTML += `${cursoData.carga_horaria_horas || 0}h de conteúdo`;
    document.getElementById('curso-instrutor').innerHTML   += cursoData.instrutor?.nome || '';
    document.getElementById('curso-preco').textContent      = !visivel('vis_valor') ? '' : (cursoData.preco > 0
      ? 'R$ ' + parseFloat(cursoData.preco).toFixed(2).replace('.', ',')
      : 'Gratuito');

    if (visivel('vis_video') && cursoData.video_url_explicativo) {
      document.getElementById('curso-video-frame').src = cursoData.video_url_explicativo;
      document.getElementById('curso-video-bloco').classList.remove('hidden');
    }
    if (visivel('vis_diferencial') && cursoData.diferencial) {
      document.getElementById('curso-diferencial-texto').textContent = cursoData.diferencial;
      document.getElementById('curso-diferencial-bloco').classList.remove('hidden');
    }
    if (visivel('vis_conteudo') && cursoData.conteudo_programatico) {
      document.getElementById('curso-conteudo-texto').textContent = cursoData.conteudo_programatico;
      document.getElementById('curso-conteudo-bloco').classList.remove('hidden');
    }
    if (visivel('vis_publico_alvo') && cursoData.publico_alvo) {
      document.getElementById('curso-publico-alvo-texto').textContent = cursoData.publico_alvo;
      document.getElementById('curso-publico-alvo-bloco').classList.remove('hidden');
    }
    if (visivel('vis_condicoes') && cursoData.condicoes) {
      document.getElementById('curso-condicoes-texto').textContent = cursoData.condicoes;
      document.getElementById('curso-condicoes-bloco').classList.remove('hidden');
    }

    if (cursoData.preco > 0) {
      document.getElementById('btn-matricular').classList.add('hidden');
      document.getElementById('btn-adicionar-carrinho').classList.remove('hidden');
      renderExamesOpcoes();
      const examesPreSelecionados = (new URLSearchParams(location.search).get('exames') || '').split(',').filter(Boolean);
      examesPreSelecionados.forEach(tipo => {
        const cb = document.querySelector(`.exame-checkbox[value="${tipo}"]`);
        if (cb) cb.checked = true;
      });
      atualizarPrecoComExames();
    } else {
      document.getElementById('btn-matricular').classList.remove('hidden');
      document.getElementById('btn-adicionar-carrinho').classList.add('hidden');
      document.getElementById('opcoes-prova-container').classList.add('hidden');
    }

    if (cursoData.thumb_url) {
      const img = document.getElementById('curso-thumb');
      img.src = cursoData.thumb_url;
      img.classList.remove('hidden');
    }

    const modulosList = document.getElementById('modulos-list');
    (cursoData.modulos || []).forEach((mod, i) => {
      const div = document.createElement('div');
      div.className = 'border border-gray-200 rounded-xl overflow-hidden';
      div.innerHTML = `
        <button onclick="toggleModulo(${i})" class="w-full flex items-center justify-between px-5 py-4 bg-gray-50 hover:bg-gray-100 transition-colors">
          <span class="font-medium text-gray-800">${mod.titulo}</span>
          <span class="text-sm text-gray-400">${(mod.aulas || []).length} aulas</span>
        </button>
        <div id="mod-${i}" class="hidden divide-y divide-gray-100">
          ${(mod.aulas || []).map(a => `
            <div class="flex items-center gap-3 px-5 py-3 text-sm text-gray-600">
              <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              ${a.titulo}
            </div>
          `).join('')}
        </div>
      `;
      modulosList.appendChild(div);
    });
  }

  function toggleModulo(i) {
    const el = document.getElementById('mod-' + i);
    el.classList.toggle('hidden');
  }

  const EXAME_LABEL = { QM: 'Exame Exemplar Global QM', AU: 'Exame Exemplar Global AU', TL: 'Exame Exemplar Global TL' };

  function renderExamesOpcoes() {
    const container = document.getElementById('opcoes-prova-container');
    const list = document.getElementById('exames-opcoes-list');
    const exames = cursoData.exames || [];
    if (!exames.length) {
      container.classList.add('hidden');
      return;
    }
    container.classList.remove('hidden');
    list.innerHTML = exames.map(ex => `
      <label class="flex items-center justify-between p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-all text-left">
        <div class="flex items-center gap-3">
          <input type="checkbox" value="${ex.tipo}" data-preco="${ex.preco}" onchange="atualizarPrecoComExames()" class="w-4 h-4 accent-primary exame-checkbox">
          <span class="text-xs font-bold text-gray-800">${EXAME_LABEL[ex.tipo] || ex.tipo}</span>
        </div>
        <span class="text-xs text-gray-500">+ R$ ${parseFloat(ex.preco).toFixed(2).replace('.', ',')}</span>
      </label>
    `).join('');
  }

  function examesSelecionados() {
    return [...document.querySelectorAll('.exame-checkbox:checked')].map(el => el.value);
  }

  function atualizarPrecoComExames() {
    const basePreco = parseFloat(cursoData.preco);
    let total = basePreco;
    document.querySelectorAll('.exame-checkbox:checked').forEach(el => { total += parseFloat(el.dataset.preco); });
    document.getElementById('curso-preco').textContent = 'R$ ' + total.toFixed(2).replace('.', ',');
  }

  async function verificarMatricula() {
    const user = authGetUser();
    if (!user) return;

    try {
      const matriculas = await apiFetch(BASE + '/api/aluno/matriculas');
      const jaMatriculado = matriculas.some(m => m.curso_id == cursoId);
      if (jaMatriculado) {
        document.getElementById('btn-matricular').classList.add('hidden');
        document.getElementById('btn-adicionar-carrinho').classList.add('hidden');
        document.getElementById('opcoes-prova-container').classList.add('hidden');
        document.getElementById('btn-acessar').classList.remove('hidden');
      }
    } catch {}
  }

  async function matricular() {
    const user = authGetUser();
    if (!user) { window.location.href = BASE + '/login?redirect=' + encodeURIComponent(location.href); return; }

    const btn = document.getElementById('btn-matricular');
    btn.disabled = true;
    btn.textContent = 'Matriculando...';

    try {
      await apiPost(BASE + '/api/aluno/matricular', { cursoId: parseInt(cursoId) });
      document.getElementById('matricula-msg').textContent = '✓ Matriculado com sucesso!';
      document.getElementById('matricula-msg').classList.remove('hidden');
      btn.classList.add('hidden');
      document.getElementById('btn-acessar').classList.remove('hidden');
    } catch (ex) {
      alert(ex.message || 'Erro ao matricular.');
      btn.disabled = false;
      btn.textContent = 'Matricular-se';
    }
  }

  function acessarCurso() {
    window.location.href = BASE + '/painel/curso/' + cursoId;
  }

  function adicionarAoCarrinho() {
    let cart = [];
    try {
      cart = JSON.parse(localStorage.getItem('act_carrinho')) || [];
    } catch {
      cart = [];
    }
    
    const exames = examesSelecionados();
    const comProva = exames.length > 0 ? 1 : 0;
    const basePreco = parseFloat(cursoData.preco);
    let precoFinal = basePreco;
    document.querySelectorAll('.exame-checkbox:checked').forEach(el => { precoFinal += parseFloat(el.dataset.preco); });
    const examesKey = exames.slice().sort().join(',');

    const existingIndex = cart.findIndex(item => item.curso_id == cursoId && (item.exames_selecionados || '') === examesKey);
    if (existingIndex > -1) {
      cart[existingIndex].vagas = (cart[existingIndex].vagas || 0) + 1;
    } else {
      cart.push({
        curso_id: cursoId,
        titulo: cursoData.titulo,
        preco: precoFinal,
        thumb_url: cursoData.thumb_url || '',
        vagas: 1,
        com_prova: comProva,
        exames_selecionados: examesKey
      });
    }
    
    localStorage.setItem('act_carrinho', JSON.stringify(cart));
    if (typeof updateCartCountBadge === 'function') {
      updateCartCountBadge();
    }
    window.location.href = BASE + '/carrinho';
  }

  document.addEventListener('DOMContentLoaded', carregarCurso);
</script>

<?php require __DIR__ . '/layout/footer.php'; ?>
