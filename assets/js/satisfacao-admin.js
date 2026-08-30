// Admin > Pesquisa de Satisfação: relatório filtrável, formato tabular/lista,
// exportação CSV e CRUD das perguntas.
function esc(s) {
  if (!s) return '';
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
}

var _satFormato = 'cards';
var _satUltimoRelatorio = [];

document.addEventListener('DOMContentLoaded', () => {
  carregarFiltrosSatisfacao();
  carregarRelatorioSatisfacao();
});

async function carregarFiltrosSatisfacao() {
  try {
    const [cursos, clientes, alunos] = await Promise.all([
      apiFetch(BASE + '/api/cursos'),
      apiFetch(BASE + '/api/admin/clientes'),
      apiFetch(BASE + '/api/admin/alunos'),
    ]);

    const selCurso = document.getElementById('sat-filtro-curso');
    cursos.forEach(c => {
      const opt = document.createElement('option');
      opt.value = c.id; opt.textContent = c.titulo;
      selCurso.appendChild(opt);
    });

    const selCliente = document.getElementById('sat-filtro-cliente');
    clientes.forEach(c => {
      const opt = document.createElement('option');
      opt.value = c.organizacao_id || '';
      if (!opt.value) return; // só clientes com organização (contrato B2B) fazem sentido como filtro
      opt.textContent = c.razao_social || c.nome;
      selCliente.appendChild(opt);
    });

    const vistos = new Set();
    const selAluno = document.getElementById('sat-filtro-aluno');
    alunos.forEach(a => {
      if (vistos.has(a.aluno_id)) return;
      vistos.add(a.aluno_id);
      const opt = document.createElement('option');
      opt.value = a.aluno_id; opt.textContent = a.aluno_nome;
      selAluno.appendChild(opt);
    });
  } catch (e) {
    // Filtros são um extra — se falhar, o relatório sem filtro continua funcionando.
  }
}

function montarQueryFiltros() {
  const curso = document.getElementById('sat-filtro-curso').value;
  const cliente = document.getElementById('sat-filtro-cliente').value;
  const aluno = document.getElementById('sat-filtro-aluno').value;
  const params = new URLSearchParams();
  if (curso) params.set('curso_id', curso);
  if (cliente) params.set('cliente_id', cliente);
  if (aluno) params.set('aluno_id', aluno);
  return params.toString();
}

function exportarSatisfacaoCsv() {
  const qs = montarQueryFiltros();
  const url = BASE + '/api/admin/satisfacao/relatorio' + (qs ? '?' + qs + '&export=csv' : '?export=csv');
  window.open(url, '_blank');
}

function alternarFormatoSatisfacao(formato) {
  _satFormato = formato;
  document.getElementById('btn-formato-cards').className = 'px-3 py-1.5 text-xs font-bold rounded-md ' + (formato === 'cards' ? 'bg-white shadow-sm text-slate-700' : 'text-slate-500');
  document.getElementById('btn-formato-tabular').className = 'px-3 py-1.5 text-xs font-bold rounded-md ' + (formato === 'tabular' ? 'bg-white shadow-sm text-slate-700' : 'text-slate-500');
  renderRelatorioSatisfacao(_satUltimoRelatorio);
}

async function carregarRelatorioSatisfacao() {
  const container = document.getElementById('satisfacao-relatorio-container');
  try {
    const qs = montarQueryFiltros();
    const data = await apiFetch(BASE + '/api/admin/satisfacao/relatorio' + (qs ? '?' + qs : ''));
    _satUltimoRelatorio = data || [];
    renderRelatorioSatisfacao(_satUltimoRelatorio);
  } catch (e) {
    container.innerHTML = `<div class="col-span-full text-center py-12 text-red-500 bg-white rounded-xl border border-slate-200 shadow-sm">Falha ao carregar relatório: ${e.message}</div>`;
  }
}

function renderRelatorioSatisfacao(data) {
  const container = document.getElementById('satisfacao-relatorio-container');

  if (!data || !data.length) {
    container.className = 'grid grid-cols-1 gap-6';
    container.innerHTML = `<div class="text-center py-12 text-slate-400 bg-white rounded-xl border border-slate-200 shadow-sm">Nenhuma resposta registrada para os filtros selecionados.</div>`;
    return;
  }

  let somaMedias = 0;
  data.forEach(item => somaMedias += parseFloat(item.media));
  const mediaGlobal = (somaMedias / data.length).toFixed(1);

  const cardGlobal = `
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
      <div>
        <h3 class="font-bold text-slate-800 text-base">Satisfação Global</h3>
        <p class="text-xs text-slate-400 mt-0.5">Média geral ponderada obtida de todas as avaliações respondidas.</p>
      </div>
      <div class="flex items-center gap-4 bg-slate-50 border border-slate-100 px-5 py-3.5 rounded-2xl">
        <span class="text-3xl font-extrabold text-slate-800">${mediaGlobal}</span>
        <div class="flex flex-col">
          <div class="flex text-red-500 gap-0.5">${gerarEstrelasHtml(mediaGlobal)}</div>
          <span class="text-[10px] font-bold text-slate-400 mt-0.5 uppercase tracking-wide">Média ActShare</span>
        </div>
      </div>
    </div>
  `;

  if (_satFormato === 'tabular') {
    container.className = 'grid grid-cols-1 gap-6';
    const linhas = data.map(item => `
      <tr class="border-t border-slate-100">
        <td class="px-4 py-3 text-slate-700">${esc(item.texto)}</td>
        <td class="px-4 py-3 text-center font-bold text-slate-800">${parseFloat(item.media).toFixed(1)}</td>
        <td class="px-4 py-3 text-center text-slate-500">${item.total_respostas}</td>
      </tr>
    `).join('');
    container.innerHTML = cardGlobal + `
      <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 text-left text-slate-500 text-xs uppercase">
            <tr><th class="px-4 py-3">Pergunta</th><th class="px-4 py-3 text-center">Nota Média</th><th class="px-4 py-3 text-center">Respostas</th></tr>
          </thead>
          <tbody>${linhas}</tbody>
        </table>
      </div>
    `;
    return;
  }

  container.className = 'grid grid-cols-1 md:grid-cols-2 gap-6';
  const cardsHtml = data.map(item => {
    const percentualLargura = (parseFloat(item.media) / 5) * 100;
    return `
      <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm space-y-4">
        <div class="flex justify-between items-start gap-4">
          <p class="font-bold text-slate-700 text-xs sm:text-sm leading-relaxed">${esc(item.texto)}</p>
          <span class="text-xs font-bold text-slate-400 bg-slate-100 rounded-full px-2 py-0.5 whitespace-nowrap">${item.total_respostas} voto(s)</span>
        </div>
        <div class="flex items-center justify-between gap-4">
          <div class="flex text-red-500 gap-1">${gerarEstrelasHtml(item.media)}</div>
          <span class="text-lg font-extrabold text-slate-800">${parseFloat(item.media).toFixed(1)}</span>
        </div>
        <div class="relative w-full h-2.5 bg-slate-100 rounded-full overflow-hidden">
          <div class="absolute top-0 left-0 bottom-0 bg-secondary rounded-full" style="width: ${percentualLargura}%"></div>
        </div>
      </div>
    `;
  }).join('');

  container.innerHTML = `<div class="col-span-full">${cardGlobal}</div>` + cardsHtml;
}

function gerarEstrelasHtml(media) {
  const valor = parseFloat(media);
  let html = '';
  for (let i = 1; i <= 5; i++) {
    if (valor >= i) html += '<span class="text-base">♥</span>';
    else if (valor > i - 1) html += '<span class="text-base text-red-400 opacity-60">♥</span>';
    else html += '<span class="text-base text-slate-200">♥</span>';
  }
  return html;
}

// ============ CRUD de perguntas ============
async function abrirModalPerguntasSatisfacao() {
  document.getElementById('modal-perguntas-satisfacao').classList.remove('hidden');
  await carregarListaPerguntasSatisfacao();
}

async function carregarListaPerguntasSatisfacao() {
  const el = document.getElementById('lista-perguntas-satisfacao');
  el.innerHTML = 'Carregando...';
  try {
    const perguntas = await apiFetch(BASE + '/api/admin/satisfacao/relatorio');
    if (!perguntas.length) {
      el.innerHTML = '<p class="text-slate-400 text-center py-4">Nenhuma pergunta cadastrada.</p>';
      return;
    }
    el.innerHTML = perguntas.map(p => `
      <div class="flex items-center gap-2 border border-gray-200 rounded-lg px-3 py-2">
        <input type="text" value="${esc(p.texto)}" id="pergunta-sat-${p.id}" class="flex-1 text-xs border-0 focus:ring-0 p-0 bg-transparent">
        <button onclick="salvarPerguntaSatisfacao(${p.id})" class="text-xs text-primary font-semibold hover:underline">Salvar</button>
        <button onclick="excluirPerguntaSatisfacao(${p.id})" class="text-xs text-red-500 font-semibold hover:underline">Excluir</button>
      </div>
    `).join('');
  } catch (e) {
    el.innerHTML = `<p class="text-red-500 text-center py-4">Erro: ${e.message}</p>`;
  }
}

document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('form-nova-pergunta-satisfacao');
  if (form) {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const input = document.getElementById('nova-pergunta-satisfacao-texto');
      const texto = input.value.trim();
      if (!texto) return;
      try {
        await apiPost(BASE + '/api/admin/satisfacao/perguntas', { texto });
        input.value = '';
        await carregarListaPerguntasSatisfacao();
        await carregarRelatorioSatisfacao();
      } catch (err) {
        alert('Erro ao adicionar pergunta: ' + err.message);
      }
    });
  }
});

async function salvarPerguntaSatisfacao(id) {
  const texto = document.getElementById('pergunta-sat-' + id).value.trim();
  if (!texto) return;
  try {
    await apiPut(BASE + '/api/admin/satisfacao/perguntas/' + id, { texto });
    await carregarRelatorioSatisfacao();
  } catch (e) {
    alert('Erro ao salvar: ' + e.message);
  }
}

async function excluirPerguntaSatisfacao(id) {
  if (!confirm('Excluir esta pergunta? Todas as respostas dadas a ela também serão apagadas.')) return;
  try {
    await apiDelete(BASE + '/api/admin/satisfacao/perguntas/' + id);
    await carregarListaPerguntasSatisfacao();
    await carregarRelatorioSatisfacao();
  } catch (e) {
    alert('Erro ao excluir: ' + e.message);
  }
}
