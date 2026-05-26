// Lógica do painel admin (substitui stores + pages admin/*.vue)
const _B = () => (typeof BASE !== 'undefined' ? BASE : '');
function esc(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

// ============ CURSOS ADMIN ============
async function carregarCursosAdmin() {
  const tbody = document.getElementById('cursos-tbody');
  try {
    const cursos = await apiFetch(_B() + '/api/cursos');
    if (!cursos.length) {
      tbody.innerHTML = '<tr><td colspan="4" class="text-center py-8 text-gray-400">Nenhum curso cadastrado.</td></tr>';
      return;
    }
    tbody.innerHTML = cursos.map(c => `
      <tr class="hover:bg-gray-50">
        <td class="px-5 py-3 font-medium text-gray-800">${esc(c.titulo)}</td>
        <td class="px-5 py-3 text-gray-500">${esc(c.categoria?.nome || '—')}</td>
        <td class="px-5 py-3">
          <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium ${c.ativo ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'}">
            ${c.ativo ? 'Ativo' : 'Inativo'}
          </span>
        </td>
        <td class="px-5 py-3">
          <div class="flex gap-2">
            <a href="${_B()}/admin/cursos/${c.id}" class="text-xs text-primary hover:underline">Editar</a>
            <button onclick="excluirCursoAdmin(${c.id})" class="text-xs text-red-500 hover:underline">Excluir</button>
          </div>
        </td>
      </tr>
    `).join('');

    // Preenche select de categorias no modal
    const catSelect = document.getElementById('curso-categoria');
    if (catSelect) {
      const cats = await apiFetch(_B() + '/api/categorias');
      cats.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c.id; opt.textContent = c.nome;
        catSelect.appendChild(opt);
      });
    }
  } catch (e) {
    tbody.innerHTML = `<tr><td colspan="4" class="text-center py-8 text-red-400">Erro: ${e.message}</td></tr>`;
  }
}

function abrirModalNovoCurso() {
  document.getElementById('modal-titulo').textContent = 'Novo Curso';
  document.getElementById('curso-id').value    = '';
  document.getElementById('curso-titulo').value = '';
  document.getElementById('curso-descricao').value = '';
  document.getElementById('curso-thumb').value  = '';
  document.getElementById('curso-carga').value  = '0';
  document.getElementById('curso-preco').value  = '0';
  document.getElementById('curso-ativo').checked   = true;
  document.getElementById('curso-publico').checked = false;
  document.getElementById('modal-curso').classList.remove('hidden');
}

function fecharModalCurso() {
  document.getElementById('modal-curso').classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', () => {
  const formCurso = document.getElementById('form-curso');
  if (formCurso) {
    formCurso.addEventListener('submit', async (e) => {
      e.preventDefault();
      const btn = document.getElementById('btn-salvar-curso');
      const err = document.getElementById('form-erro');
      btn.disabled = true; err.classList.add('hidden');

      const id    = document.getElementById('curso-id').value;
      const body  = {
        titulo:              document.getElementById('curso-titulo').value,
        descricao:           document.getElementById('curso-descricao').value,
        thumb_url:           document.getElementById('curso-thumb').value || null,
        carga_horaria_horas: parseInt(document.getElementById('curso-carga').value) || 0,
        preco:               parseFloat(document.getElementById('curso-preco').value) || 0,
        categoria_id:        document.getElementById('curso-categoria').value || null,
        ativo:               document.getElementById('curso-ativo').checked ? 1 : 0,
        publico:             document.getElementById('curso-publico').checked ? 1 : 0,
      };

      try {
        if (id) await apiPut(_B() + '/api/cursos/' + id, body);
        else    await apiPost(_B() + '/api/cursos', body);
        fecharModalCurso();
        carregarCursosAdmin();
      } catch (ex) {
        err.textContent = ex.message; err.classList.remove('hidden');
      } finally { btn.disabled = false; }
    });
  }
});

async function excluirCursoAdmin(id) {
  if (!confirm('Excluir este curso? Esta ação não pode ser desfeita.')) return;
  try {
    await apiDelete(_B() + '/api/cursos/' + id);
    carregarCursosAdmin();
  } catch (e) { alert(e.message); }
}

// ============ CATEGORIAS ADMIN ============
async function carregarCategoriasAdmin() {
  const tbody = document.getElementById('cats-tbody');
  try {
    const cats = await apiFetch(_B() + '/api/categorias');
    tbody.innerHTML = cats.length
      ? cats.map(c => `
          <tr class="hover:bg-gray-50">
            <td class="px-5 py-3 font-medium text-gray-800">${esc(c.nome)}</td>
            <td class="px-5 py-3 text-gray-500">${esc(c.slug || '—')}</td>
            <td class="px-5 py-3">
              <button onclick="excluirCategoriaAdmin(${c.id})" class="text-xs text-red-500 hover:underline">Excluir</button>
            </td>
          </tr>
        `).join('')
      : '<tr><td colspan="3" class="text-center py-8 text-gray-400">Nenhuma categoria.</td></tr>';
  } catch (e) { tbody.innerHTML = `<tr><td colspan="3" class="text-center py-8 text-red-400">Erro: ${e.message}</td></tr>`; }
}

function abrirModalCategoria() {
  document.getElementById('cat-nome').value = '';
  document.getElementById('cat-slug').value = '';
  document.getElementById('modal-cat').classList.remove('hidden');
}

document.addEventListener('DOMContentLoaded', () => {
  const formCat = document.getElementById('form-cat');
  if (formCat) {
    formCat.addEventListener('submit', async (e) => {
      e.preventDefault();
      const btn = document.getElementById('btn-salvar-cat');
      const err = document.getElementById('cat-erro');
      btn.disabled = true; err.classList.add('hidden');
      try {
        await apiPost(_B() + '/api/categorias', {
          nome: document.getElementById('cat-nome').value,
          slug: document.getElementById('cat-slug').value || null,
        });
        document.getElementById('modal-cat').classList.add('hidden');
        carregarCategoriasAdmin();
      } catch (ex) { err.textContent = ex.message; err.classList.remove('hidden'); }
      finally { btn.disabled = false; }
    });
  }
});

async function excluirCategoriaAdmin(id) {
  if (!confirm('Excluir esta categoria?')) return;
  try { await apiDelete(_B() + '/api/categorias/' + id); carregarCategoriasAdmin(); }
  catch (e) { alert(e.message); }
}

// ============ USUÁRIOS ADMIN ============
async function carregarUsuariosAdmin() {
  const tbody = document.getElementById('users-tbody');
  try {
    const users = await apiFetch(_B() + '/api/admin/usuarios');
    tbody.innerHTML = users.map(u => `
      <tr class="hover:bg-gray-50">
        <td class="px-5 py-3 font-medium text-gray-800">${esc(u.nome)}</td>
        <td class="px-5 py-3 text-gray-500">${esc(u.email)}</td>
        <td class="px-5 py-3">
          <select onchange="alterarRole(${u.id}, this.value)"
            class="text-xs border border-gray-200 rounded px-2 py-1 bg-white focus:outline-none focus:ring-1 focus:ring-primary">
            ${['aluno','gestor','admin','instrutor'].map(r => `<option value="${r}" ${u.role===r?'selected':''}>${r}</option>`).join('')}
          </select>
        </td>
        <td class="px-5 py-3 text-gray-400 text-xs">${new Date(u.created_at).toLocaleDateString('pt-BR')}</td>
        <td class="px-5 py-3 text-xs text-gray-400">${u.ativo ? '✓ Ativo' : 'Inativo'}</td>
      </tr>
    `).join('');
  } catch (e) { tbody.innerHTML = `<tr><td colspan="5" class="text-center py-8 text-red-400">Erro: ${e.message}</td></tr>`; }
}

async function alterarRole(userId, role) {
  try { await apiPatch(_B() + '/api/admin/usuarios', { userId, role }); }
  catch (e) { alert('Erro ao alterar role: ' + e.message); carregarUsuariosAdmin(); }
}

// ============ CURSO DETALHE ADMIN ============
let _cursoAdminData = null;

async function carregarCursoAdmin(id) {
  try {
    _cursoAdminData = await apiFetch(_B() + '/api/cursos/' + id);
    document.getElementById('curso-admin-loading').classList.add('hidden');
    document.getElementById('curso-admin-content').classList.remove('hidden');
    document.getElementById('ca-titulo').textContent    = _cursoAdminData.titulo;
    document.getElementById('ca-descricao').textContent = _cursoAdminData.descricao || '';
    renderModulosAdmin();
  } catch (e) {
    document.getElementById('curso-admin-loading').textContent = 'Erro: ' + e.message;
  }
}

function renderModulosAdmin() {
  const list = document.getElementById('modulos-admin-list');
  list.innerHTML = (_cursoAdminData.modulos || []).map(mod => `
    <div class="border border-gray-200 rounded-xl overflow-hidden">
      <div class="flex items-center justify-between px-5 py-3 bg-gray-50">
        <span class="font-medium text-gray-800">${esc(mod.titulo)}</span>
        <div class="flex gap-2">
          <button onclick="abrirModalAula(${mod.id})" class="text-xs text-primary hover:underline">+ Aula</button>
          <button onclick="editarModulo(${mod.id}, '${esc(mod.titulo)}', ${mod.ordem})" class="text-xs text-gray-500 hover:underline">Editar</button>
          <button onclick="excluirModulo(${mod.id})" class="text-xs text-red-500 hover:underline">Excluir</button>
        </div>
      </div>
      <div class="divide-y divide-gray-100">
        ${(mod.aulas || []).map(a => `
          <div class="flex items-center justify-between px-5 py-2.5">
            <span class="text-sm text-gray-700">${esc(a.titulo)}</span>
            <div class="flex gap-2">
              <button onclick="editarAula(${a.id}, '${esc(a.titulo)}', '${esc(a.video_url||'')}', '${esc(a.descricao||'')}', ${a.ordem||0}, ${a.duracao_min||0}, ${mod.id})" class="text-xs text-gray-500 hover:underline">Editar</button>
              <button onclick="excluirAula(${a.id})" class="text-xs text-red-500 hover:underline">Excluir</button>
            </div>
          </div>
        `).join('')}
      </div>
    </div>
  `).join('') || '<p class="text-gray-400 text-sm">Nenhum módulo. Clique em "+ Módulo".</p>';
}

// Módulos
function abrirModalModulo() {
  document.getElementById('mod-modal-titulo').textContent = 'Novo Módulo';
  document.getElementById('mod-id').value    = '';
  document.getElementById('mod-titulo').value = '';
  document.getElementById('mod-ordem').value  = '0';
  document.getElementById('modal-modulo').classList.remove('hidden');
}

function editarModulo(id, titulo, ordem) {
  document.getElementById('mod-modal-titulo').textContent = 'Editar Módulo';
  document.getElementById('mod-id').value    = id;
  document.getElementById('mod-titulo').value = titulo;
  document.getElementById('mod-ordem').value  = ordem;
  document.getElementById('modal-modulo').classList.remove('hidden');
}

function fecharModalModulo() { document.getElementById('modal-modulo').classList.add('hidden'); }

document.addEventListener('DOMContentLoaded', () => {
  const formMod = document.getElementById('form-modulo');
  if (formMod) {
    formMod.addEventListener('submit', async (e) => {
      e.preventDefault();
      const id     = document.getElementById('mod-id').value;
      const titulo = document.getElementById('mod-titulo').value;
      const ordem  = parseInt(document.getElementById('mod-ordem').value) || 0;
      try {
        if (id) await apiPut(_B() + '/api/modulos/' + id, { titulo, ordem });
        else    await apiPost(_B() + '/api/modulos', { curso_id: cursoAdminId, titulo, ordem });
        fecharModalModulo();
        _cursoAdminData = await apiFetch(_B() + '/api/cursos/' + cursoAdminId);
        renderModulosAdmin();
      } catch (ex) { alert(ex.message); }
    });
  }
});

async function excluirModulo(id) {
  if (!confirm('Excluir este módulo e todas as aulas?')) return;
  try { await apiDelete(_B() + '/api/modulos/' + id); _cursoAdminData = await apiFetch(_B() + '/api/cursos/' + cursoAdminId); renderModulosAdmin(); }
  catch (e) { alert(e.message); }
}

// Aulas
function abrirModalAula(moduloId) {
  document.getElementById('aula-modal-titulo').textContent = 'Nova Aula';
  document.getElementById('aula-id').value       = '';
  document.getElementById('aula-modulo-id').value = moduloId;
  document.getElementById('aula-titulo').value   = '';
  document.getElementById('aula-url').value      = '';
  document.getElementById('aula-descricao').value = '';
  document.getElementById('aula-ordem').value    = '0';
  document.getElementById('aula-duracao').value  = '0';
  document.getElementById('modal-aula').classList.remove('hidden');
}

function editarAula(id, titulo, url, descricao, ordem, duracao, moduloId) {
  document.getElementById('aula-modal-titulo').textContent = 'Editar Aula';
  document.getElementById('aula-id').value       = id;
  document.getElementById('aula-modulo-id').value = moduloId;
  document.getElementById('aula-titulo').value   = titulo;
  document.getElementById('aula-url').value      = url;
  document.getElementById('aula-descricao').value = descricao;
  document.getElementById('aula-ordem').value    = ordem;
  document.getElementById('aula-duracao').value  = duracao;
  document.getElementById('modal-aula').classList.remove('hidden');
}

function fecharModalAula() { document.getElementById('modal-aula').classList.add('hidden'); }

document.addEventListener('DOMContentLoaded', () => {
  const formAula = document.getElementById('form-aula');
  if (formAula) {
    formAula.addEventListener('submit', async (e) => {
      e.preventDefault();
      const id       = document.getElementById('aula-id').value;
      const moduloId = document.getElementById('aula-modulo-id').value;
      const body     = {
        titulo:      document.getElementById('aula-titulo').value,
        url:         document.getElementById('aula-url').value || null,
        descricao:   document.getElementById('aula-descricao').value || null,
        ordem:       parseInt(document.getElementById('aula-ordem').value) || 0,
        duracao_min: parseInt(document.getElementById('aula-duracao').value) || 0,
        modulo_id:   moduloId,
      };
      try {
        if (id) await apiPut(_B() + '/api/aulas/' + id, body);
        else    await apiPost(_B() + '/api/aulas', body);
        fecharModalAula();
        _cursoAdminData = await apiFetch(_B() + '/api/cursos/' + cursoAdminId);
        renderModulosAdmin();
      } catch (ex) { alert(ex.message); }
    });
  }
});

async function excluirAula(id) {
  if (!confirm('Excluir esta aula?')) return;
  try { await apiDelete(_B() + '/api/aulas/' + id); _cursoAdminData = await apiFetch(_B() + '/api/cursos/' + cursoAdminId); renderModulosAdmin(); }
  catch (e) { alert(e.message); }
}
