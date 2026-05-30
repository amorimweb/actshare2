// Lógica de cursos (substitui stores/courses.ts e stores/categories.ts)
let _cursos     = null;
let _categorias = null;
var _B = _B || (() => (typeof BASE !== 'undefined' ? BASE : ''));

async function fetchCursos(params = {}) {
  if (_cursos) return _cursos;
  const qs = new URLSearchParams(params).toString();
  _cursos = await apiFetch(_B() + '/api/cursos' + (qs ? '?' + qs : ''));
  return _cursos;
}

async function fetchCategorias() {
  if (_categorias) return _categorias;
  _categorias = await apiFetch(_B() + '/api/categorias');
  return _categorias;
}

function renderCardCurso(curso) {
  return `
    <a href="${_B()}/cursos/${curso.id}" class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-md transition-shadow flex flex-col">
      ${curso.thumb_url
        ? `<img src="${curso.thumb_url}" alt="${esc(curso.titulo)}" class="w-full h-40 object-cover">`
        : `<div class="w-full h-40 bg-gradient-to-br from-primary to-blue-700 flex items-center justify-center">
             <svg class="w-12 h-12 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
           </div>`
      }
      <div class="p-4 flex flex-col flex-1">
        ${curso.categoria ? `<span class="text-xs text-secondary font-medium mb-1">${esc(curso.categoria.nome)}</span>` : ''}
        <h3 class="font-semibold text-gray-800 mb-2 line-clamp-2">${esc(curso.titulo)}</h3>
        ${curso.descricao ? `<p class="text-xs text-gray-500 line-clamp-2 mb-3">${esc(curso.descricao)}</p>` : ''}
        <div class="mt-auto flex items-center justify-between text-xs text-gray-400">
          <span>${curso.carga_horaria_horas || 0}h</span>
          <span class="font-semibold text-primary">${curso.preco > 0 ? 'R$ ' + parseFloat(curso.preco).toFixed(2).replace('.', ',') : 'Gratuito'}</span>
        </div>
      </div>
    </a>
  `;
}

async function carregarCursosDestaque(containerId, limite = 4) {
  const container = document.getElementById(containerId);
  if (!container) return;

  try {
    const cursos = await fetchCursos({ ativo: 'true', publico: 'true' });
    const slice  = cursos.slice(0, limite);
    container.innerHTML = slice.length
      ? slice.map(renderCardCurso).join('')
      : '<p class="col-span-full text-center text-gray-400 py-8">Nenhum curso disponível.</p>';
  } catch {
    container.innerHTML = '<p class="col-span-full text-center text-gray-400 py-8">Erro ao carregar cursos.</p>';
  }
}

let _categoriaAtiva = null;

async function carregarPaginaCursos() {
  await Promise.all([
    carregarSidebarCategorias(),
    renderizarCursos(null),
  ]);
}

async function carregarSidebarCategorias() {
  const list = document.getElementById('categorias-list');
  if (!list) return;

  try {
    const cats = await fetchCategorias();
    cats.forEach(cat => {
      const li = document.createElement('li');
      li.innerHTML = `
        <button onclick="filtrarCategoria(${cat.id})"
          class="categoria-btn w-full text-left text-sm px-3 py-1.5 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors"
          data-id="${cat.id}">
          ${esc(cat.nome)}
        </button>
      `;
      list.appendChild(li);
    });
  } catch {}
}

async function renderizarCursos(categoriaId) {
  _categoriaAtiva = categoriaId;
  const grid  = document.getElementById('cursos-grid');
  const empty = document.getElementById('cursos-empty');
  if (!grid) return;

  grid.innerHTML = '<div class="col-span-full text-center py-8 text-gray-400"><div class="inline-block w-6 h-6 border-4 border-primary border-t-transparent rounded-full animate-spin"></div></div>';

  try {
    const params = { ativo: 'true', publico: 'true' };
    if (categoriaId) params.categoria = categoriaId;
    _cursos = null;
    const cursos = await fetchCursos(params);

    if (!cursos.length) {
      grid.innerHTML = '';
      empty?.classList.remove('hidden');
      return;
    }
    empty?.classList.add('hidden');
    grid.innerHTML = cursos.map(renderCardCurso).join('');
  } catch {
    grid.innerHTML = '<p class="col-span-full text-center text-gray-400 py-8">Erro ao carregar.</p>';
  }
}

function esc(str) {
  if (!str) return '';
  return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
