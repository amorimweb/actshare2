// Lógica do painel admin (substitui stores + pages admin/*.vue)
var _B = _B || (() => (typeof BASE !== 'undefined' ? BASE : ''));
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
            <button onclick="duplicarCursoAdmin(${c.id})" class="text-xs text-gray-500 hover:underline">Duplicar</button>
            <button onclick="excluirCursoAdmin(${c.id})" class="text-xs text-red-500 hover:underline">Excluir</button>
          </div>
        </td>
      </tr>
    `).join('');

    // Preenche select de categorias no modal
    const catSelect = document.getElementById('curso-categoria');
    if (catSelect && catSelect.children.length <= 1) {
      const cats = await apiFetch(_B() + '/api/categorias');
      cats.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c.id; opt.textContent = c.nome;
        catSelect.appendChild(opt);
      });
    }

    // Preenche select de instrutores no modal
    const instSelect = document.getElementById('curso-instrutor');
    if (instSelect && instSelect.children.length <= 1) {
      const insts = await apiFetch(_B() + '/api/instrutores');
      insts.forEach(i => {
        const opt = document.createElement('option');
        opt.value = i.id; opt.textContent = i.nome;
        instSelect.appendChild(opt);
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
  document.getElementById('curso-codigo').value = '';
  document.getElementById('curso-nome-certificado').value = '';
  document.getElementById('curso-prazo-acesso').value = '';
  document.getElementById('curso-descricao').value = '';
  document.getElementById('curso-thumb').value  = '';
  document.getElementById('curso-carga').value  = '0';
  document.getElementById('curso-preco').value  = '0';
  document.getElementById('curso-categoria').value = '';
  document.getElementById('curso-instrutor').value = '';
  document.getElementById('curso-ativo').checked   = true;
  document.getElementById('curso-publico').checked = false;
  document.getElementById('curso-disponivel-loja').checked = true;
  document.getElementById('curso-exibir-instrutor').checked = false;
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
        codigo:              document.getElementById('curso-codigo').value.trim().toUpperCase(),
        nome_certificado:    document.getElementById('curso-nome-certificado').value.trim() || null,
        prazo_acesso_dias:   document.getElementById('curso-prazo-acesso').value !== '' ? parseInt(document.getElementById('curso-prazo-acesso').value) : null,
        descricao:           document.getElementById('curso-descricao').value,
        thumb_url:           document.getElementById('curso-thumb').value || null,
        carga_horaria_horas: parseInt(document.getElementById('curso-carga').value) || 0,
        preco:               parseFloat(document.getElementById('curso-preco').value) || 0,
        categoria_id:        document.getElementById('curso-categoria').value || null,
        instrutor_id:        document.getElementById('curso-instrutor').value || null,
        ativo:               document.getElementById('curso-ativo').checked ? 1 : 0,
        publico:             document.getElementById('curso-publico').checked ? 1 : 0,
        disponivel_loja:     document.getElementById('curso-disponivel-loja').checked ? 1 : 0,
        exibir_instrutor:    document.getElementById('curso-exibir-instrutor').checked ? 1 : 0,
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

async function duplicarCursoAdmin(id) {
  if (!confirm('Duplicar este curso, com todos os módulos e aulas?')) return;
  try {
    const original = await apiFetch(_B() + '/api/cursos/' + id);

    const novoBody = { ...original };
    delete novoBody.id;
    delete novoBody.created_at;
    delete novoBody.updated_at;
    delete novoBody.modulos;
    delete novoBody.prerequisitos;
    delete novoBody.categoria; delete novoBody.categoria_nome; delete novoBody.categoria_slug;
    delete novoBody.instrutor; delete novoBody.instrutor_nome; delete novoBody.qualificacao1; delete novoBody.qualificacao2; delete novoBody.avatar_url; delete novoBody.assinatura_url;
    novoBody.titulo = original.titulo + ' (cópia)';
    novoBody.codigo = null; // código precisa ser único; admin define um novo ao editar
    novoBody.publico = 0;   // cópia nasce oculta da loja até revisão do admin
    novoBody.disponivel_loja = 0;

    const novoCurso = await apiPost(_B() + '/api/cursos', novoBody);

    for (const mod of (original.modulos || [])) {
      const novoModulo = await apiPost(_B() + '/api/modulos', { curso_id: novoCurso.id, titulo: mod.titulo, ordem: mod.ordem });
      for (const a of (mod.aulas || [])) {
        const aulaBody = { ...a };
        delete aulaBody.id; delete aulaBody.created_at;
        aulaBody.modulo_id = novoModulo.id;
        aulaBody.url = a.video_url;
        await apiPost(_B() + '/api/aulas', aulaBody);
      }
    }

    alert('Curso duplicado com sucesso! Revise o código e publique quando estiver pronto.');
    window.location.href = _B() + '/admin/cursos/' + novoCurso.id;
  } catch (e) { alert('Erro ao duplicar: ' + e.message); }
}

// ============ CATEGORIAS ADMIN ============
let _categoriasList = [];

async function carregarCategoriasAdmin() {
  const tbody = document.getElementById('cats-tbody');
  try {
    _categoriasList = await apiFetch(_B() + '/api/categorias');
    const porId = Object.fromEntries(_categoriasList.map(c => [c.id, c]));

    tbody.innerHTML = _categoriasList.length
      ? _categoriasList.map(c => `
          <tr class="hover:bg-gray-50">
            <td class="px-5 py-3 font-medium text-gray-800">${esc(c.nome)}</td>
            <td class="px-5 py-3 text-gray-500">${esc(c.slug || '—')}</td>
            <td class="px-5 py-3 text-gray-500">${c.parent_id && porId[c.parent_id] ? esc(porId[c.parent_id].nome) : '—'}</td>
            <td class="px-5 py-3">
              <button onclick="abrirModalEditarCategoria(${c.id})" class="text-xs text-primary hover:underline mr-3">Editar</button>
              <button onclick="excluirCategoriaAdmin(${c.id})" class="text-xs text-red-500 hover:underline">Excluir</button>
            </td>
          </tr>
        `).join('')
      : '<tr><td colspan="4" class="text-center py-8 text-gray-400">Nenhuma categoria.</td></tr>';
  } catch (e) { tbody.innerHTML = `<tr><td colspan="4" class="text-center py-8 text-red-400">Erro: ${e.message}</td></tr>`; }
}

function popularSelectCategoriaPai(excluindoId) {
  const select = document.getElementById('cat-parent');
  if (!select) return;
  select.innerHTML = '<option value="">Nenhuma (categoria de nível principal)</option>' +
    _categoriasList
      .filter(c => c.id !== excluindoId && !c.parent_id) // só permite 2 níveis: pai não pode já ter pai
      .map(c => `<option value="${c.id}">${esc(c.nome)}</option>`)
      .join('');
}

function abrirModalCategoria() {
  document.getElementById('modal-cat-titulo').textContent = 'Nova Categoria';
  document.getElementById('cat-id').value = '';
  document.getElementById('cat-nome').value = '';
  document.getElementById('cat-slug').value = '';
  popularSelectCategoriaPai(null);
  document.getElementById('cat-parent').value = '';
  document.getElementById('modal-cat').classList.remove('hidden');
}

function abrirModalEditarCategoria(id) {
  const c = _categoriasList.find(x => x.id === id);
  if (!c) return;
  document.getElementById('modal-cat-titulo').textContent = 'Editar Categoria';
  document.getElementById('cat-id').value = c.id;
  document.getElementById('cat-nome').value = c.nome;
  document.getElementById('cat-slug').value = c.slug || '';
  popularSelectCategoriaPai(c.id);
  document.getElementById('cat-parent').value = c.parent_id || '';
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
      const id = document.getElementById('cat-id').value;
      const body = {
        nome: document.getElementById('cat-nome').value,
        slug: document.getElementById('cat-slug').value || null,
        parent_id: document.getElementById('cat-parent').value || null,
      };
      try {
        if (id) await apiPut(_B() + '/api/categorias/' + id, body);
        else    await apiPost(_B() + '/api/categorias', body);
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

// ============ COMBOS ADMIN ============
let _combosList = [];
let _todosCursosCache = [];

async function carregarCombosAdmin() {
  const tbody = document.getElementById('combos-tbody');
  try {
    _combosList = await apiFetch(_B() + '/api/combos');
    tbody.innerHTML = _combosList.length
      ? _combosList.map(c => `
          <tr class="hover:bg-gray-50">
            <td class="px-5 py-3 font-medium text-gray-800">${esc(c.titulo)}</td>
            <td class="px-5 py-3 text-gray-500 text-xs">${(c.cursos || []).map(x => esc(x.titulo)).join(', ')}</td>
            <td class="px-5 py-3 font-semibold text-gray-700">R$ ${parseFloat(c.preco).toFixed(2).replace('.', ',')}</td>
            <td class="px-5 py-3">
              <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium ${c.ativo ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'}">${c.ativo ? 'Ativo' : 'Inativo'}</span>
            </td>
            <td class="px-5 py-3">
              <button onclick="abrirModalEditarCombo(${c.id})" class="text-xs text-primary hover:underline mr-3">Editar</button>
              <button onclick="excluirComboAdmin(${c.id})" class="text-xs text-red-500 hover:underline">Excluir</button>
            </td>
          </tr>
        `).join('')
      : '<tr><td colspan="5" class="text-center py-8 text-gray-400">Nenhum combo cadastrado.</td></tr>';
  } catch (e) { tbody.innerHTML = `<tr><td colspan="5" class="text-center py-8 text-red-400">Erro: ${e.message}</td></tr>`; }
}

async function popularSelectCursosCombo(selecionados = []) {
  if (!_todosCursosCache.length) {
    _todosCursosCache = await apiFetch(_B() + '/api/cursos');
  }
  const select = document.getElementById('combo-cursos');
  select.innerHTML = _todosCursosCache
    .map(c => `<option value="${c.id}" ${selecionados.includes(c.id) ? 'selected' : ''}>${esc(c.titulo)}</option>`)
    .join('');
}

async function abrirModalCombo() {
  document.getElementById('modal-combo-titulo').textContent = 'Novo Combo';
  document.getElementById('combo-id').value = '';
  document.getElementById('combo-titulo').value = '';
  document.getElementById('combo-descricao').value = '';
  document.getElementById('combo-preco').value = '';
  document.getElementById('combo-prazo').value = '';
  document.getElementById('combo-thumb').value = '';
  document.getElementById('combo-ativo').checked = true;
  document.getElementById('combo-publico').checked = false;
  document.getElementById('combo-disponivel-loja').checked = true;
  document.getElementById('combo-erro').classList.add('hidden');
  await popularSelectCursosCombo([]);
  document.getElementById('modal-combo').classList.remove('hidden');
}

async function abrirModalEditarCombo(id) {
  const c = _combosList.find(x => x.id === id);
  if (!c) return;
  document.getElementById('modal-combo-titulo').textContent = 'Editar Combo';
  document.getElementById('combo-id').value = c.id;
  document.getElementById('combo-titulo').value = c.titulo;
  document.getElementById('combo-descricao').value = c.descricao || '';
  document.getElementById('combo-preco').value = parseFloat(c.preco);
  document.getElementById('combo-prazo').value = c.prazo_validade_dias || '';
  document.getElementById('combo-thumb').value = c.thumb_url || '';
  document.getElementById('combo-ativo').checked = c.ativo == 1;
  document.getElementById('combo-publico').checked = c.publico == 1;
  document.getElementById('combo-disponivel-loja').checked = c.disponivel_loja == 1;
  document.getElementById('combo-erro').classList.add('hidden');
  await popularSelectCursosCombo((c.cursos || []).map(x => x.curso_id));
  document.getElementById('modal-combo').classList.remove('hidden');
}

document.addEventListener('DOMContentLoaded', () => {
  const formCombo = document.getElementById('form-combo');
  if (formCombo) {
    formCombo.addEventListener('submit', async (e) => {
      e.preventDefault();
      const btn = document.getElementById('btn-salvar-combo');
      const err = document.getElementById('combo-erro');
      btn.disabled = true; err.classList.add('hidden');

      const cursoIds = Array.from(document.getElementById('combo-cursos').selectedOptions).map(o => parseInt(o.value));
      if (cursoIds.length < 2) {
        err.textContent = 'Selecione pelo menos 2 cursos para o combo.';
        err.classList.remove('hidden');
        btn.disabled = false;
        return;
      }

      const id = document.getElementById('combo-id').value;
      const body = {
        titulo: document.getElementById('combo-titulo').value,
        descricao: document.getElementById('combo-descricao').value || null,
        preco: parseFloat(document.getElementById('combo-preco').value) || 0,
        prazo_validade_dias: document.getElementById('combo-prazo').value || null,
        thumb_url: document.getElementById('combo-thumb').value || null,
        ativo: document.getElementById('combo-ativo').checked ? 1 : 0,
        publico: document.getElementById('combo-publico').checked ? 1 : 0,
        disponivel_loja: document.getElementById('combo-disponivel-loja').checked ? 1 : 0,
        curso_ids: cursoIds,
      };

      try {
        if (id) await apiPut(_B() + '/api/combos/' + id, body);
        else    await apiPost(_B() + '/api/combos', body);
        document.getElementById('modal-combo').classList.add('hidden');
        carregarCombosAdmin();
      } catch (ex) { err.textContent = ex.message; err.classList.remove('hidden'); }
      finally { btn.disabled = false; }
    });
  }
});

async function excluirComboAdmin(id) {
  if (!confirm('Excluir este combo?')) return;
  try { await apiDelete(_B() + '/api/combos/' + id); carregarCombosAdmin(); }
  catch (e) { alert(e.message); }
}

// ============ INSTRUTORES ADMIN ============
let _instrutoresList = [];

async function carregarInstrutoresAdmin() {
  const tbody = document.getElementById('inst-tbody');
  try {
    _instrutoresList = await apiFetch(_B() + '/api/instrutores');
    tbody.innerHTML = _instrutoresList.length
      ? _instrutoresList.map(i => `
          <tr class="hover:bg-gray-50">
            <td class="px-5 py-3">
              ${i.avatar_url
                ? `<img src="${esc(i.avatar_url)}" class="w-9 h-9 rounded-full object-cover">`
                : `<div class="w-9 h-9 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-xs font-bold">${esc((i.nome||'?').charAt(0).toUpperCase())}</div>`}
            </td>
            <td class="px-5 py-3 font-medium text-gray-800">${esc(i.nome)}</td>
            <td class="px-5 py-3 text-gray-500 text-xs">${[i.qualificacao1, i.qualificacao2].filter(Boolean).map(esc).join(' • ') || '—'}</td>
            <td class="px-5 py-3">
              <button onclick="abrirModalEditarInstrutor(${i.id})" class="text-xs text-primary hover:underline mr-3">Editar</button>
              <button onclick="excluirInstrutorAdmin(${i.id})" class="text-xs text-red-500 hover:underline">Excluir</button>
            </td>
          </tr>
        `).join('')
      : '<tr><td colspan="4" class="text-center py-8 text-gray-400">Nenhum instrutor cadastrado.</td></tr>';
  } catch (e) { tbody.innerHTML = `<tr><td colspan="4" class="text-center py-8 text-red-400">Erro: ${e.message}</td></tr>`; }
}

function abrirModalInstrutor() {
  document.getElementById('modal-inst-titulo').textContent = 'Novo Instrutor';
  document.getElementById('inst-id').value = '';
  document.getElementById('inst-nome').value = '';
  document.getElementById('inst-qualificacao1').value = '';
  document.getElementById('inst-qualificacao2').value = '';
  document.getElementById('inst-descricao').value = '';
  document.getElementById('inst-avatar').value = '';
  document.getElementById('inst-assinatura').value = '';
  document.getElementById('inst-erro').classList.add('hidden');
  document.getElementById('modal-inst').classList.remove('hidden');
}

function abrirModalEditarInstrutor(id) {
  const i = _instrutoresList.find(x => x.id === id);
  if (!i) return;
  document.getElementById('modal-inst-titulo').textContent = 'Editar Instrutor';
  document.getElementById('inst-id').value = i.id;
  document.getElementById('inst-nome').value = i.nome || '';
  document.getElementById('inst-qualificacao1').value = i.qualificacao1 || '';
  document.getElementById('inst-qualificacao2').value = i.qualificacao2 || '';
  document.getElementById('inst-descricao').value = i.descricao || '';
  document.getElementById('inst-avatar').value = i.avatar_url || '';
  document.getElementById('inst-assinatura').value = i.assinatura_url || '';
  document.getElementById('inst-erro').classList.add('hidden');
  document.getElementById('modal-inst').classList.remove('hidden');
}

document.addEventListener('DOMContentLoaded', () => {
  const formInst = document.getElementById('form-inst');
  if (formInst) {
    formInst.addEventListener('submit', async (e) => {
      e.preventDefault();
      const btn = document.getElementById('btn-salvar-inst');
      const err = document.getElementById('inst-erro');
      btn.disabled = true; err.classList.add('hidden');
      const id = document.getElementById('inst-id').value;
      const body = {
        nome:            document.getElementById('inst-nome').value,
        qualificacao1:   document.getElementById('inst-qualificacao1').value || null,
        qualificacao2:   document.getElementById('inst-qualificacao2').value || null,
        descricao:       document.getElementById('inst-descricao').value || null,
        avatar_url:      document.getElementById('inst-avatar').value || null,
        assinatura_url:  document.getElementById('inst-assinatura').value || null,
      };
      try {
        if (id) await apiPut(_B() + '/api/instrutores/' + id, body);
        else    await apiPost(_B() + '/api/instrutores', body);
        document.getElementById('modal-inst').classList.add('hidden');
        carregarInstrutoresAdmin();
      } catch (ex) { err.textContent = ex.message; err.classList.remove('hidden'); }
      finally { btn.disabled = false; }
    });
  }
});

async function excluirInstrutorAdmin(id) {
  if (!confirm('Excluir este instrutor? Cursos vinculados ficarão sem instrutor.')) return;
  try { await apiDelete(_B() + '/api/instrutores/' + id); carregarInstrutoresAdmin(); }
  catch (e) { alert(e.message); }
}

// ============ USUÁRIOS ADMIN ============
let _usuariosList = [];

async function carregarUsuariosAdmin() {
  const tbody = document.getElementById('users-tbody');
  try {
    _usuariosList = await apiFetch(_B() + '/api/admin/usuarios');
    tbody.innerHTML = _usuariosList.map(u => `
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
        <td class="px-5 py-3 text-xs">
          <button onclick="abrirModalFicha(${u.id})" class="text-primary font-semibold hover:underline">Ver Ficha</button>
        </td>
      </tr>
    `).join('');
  } catch (e) { tbody.innerHTML = `<tr><td colspan="6" class="text-center py-8 text-red-400">Erro: ${e.message}</td></tr>`; }
}

async function alterarRole(userId, role) {
  try { await apiPatch(_B() + '/api/admin/usuarios', { userId, role }); }
  catch (e) { alert('Erro ao alterar role: ' + e.message); carregarUsuariosAdmin(); }
}

const FICHA_CAMPOS = ['documento','telefone','tipo_pessoa','razao_social','inscricao_estadual','cep','endereco','numero','bairro','cidade','estado'];

function abrirModalFicha(id) {
  const u = _usuariosList.find(x => x.id === id);
  if (!u) return;
  document.getElementById('ficha-id').value = u.id;
  document.getElementById('ficha-nome').value = u.nome || '';
  document.getElementById('ficha-email').value = u.email || '';
  FICHA_CAMPOS.forEach(campo => {
    const el = document.getElementById('ficha-' + campo.replace(/_/g, '-'));
    if (el) el.value = u[campo] || '';
  });
  document.getElementById('ficha-erro').classList.add('hidden');
  document.getElementById('modal-ficha').classList.remove('hidden');
}

document.addEventListener('DOMContentLoaded', () => {
  const formFicha = document.getElementById('form-ficha');
  if (formFicha) {
    formFicha.addEventListener('submit', async (e) => {
      e.preventDefault();
      const btn = document.getElementById('btn-salvar-ficha');
      const err = document.getElementById('ficha-erro');
      btn.disabled = true; err.classList.add('hidden');

      const body = {
        id: document.getElementById('ficha-id').value,
        nome: document.getElementById('ficha-nome').value,
        email: document.getElementById('ficha-email').value,
      };
      FICHA_CAMPOS.forEach(campo => {
        const el = document.getElementById('ficha-' + campo.replace(/_/g, '-'));
        if (el) body[campo] = el.value;
      });

      try {
        await apiPut(_B() + '/api/admin/usuarios', body);
        document.getElementById('modal-ficha').classList.add('hidden');
        carregarUsuariosAdmin();
      } catch (ex) { err.textContent = ex.message; err.classList.remove('hidden'); }
      finally { btn.disabled = false; }
    });
  }
});

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
            <span class="text-sm text-gray-700">
              <span class="font-bold text-xs text-slate-400 mr-1.5">
                ${a.e_prova ? '🎯 [PROVA]' : (a.tipo === 'quiz' ? '📖 [QUIZZ]' : `[${a.tipo.toUpperCase()}]`)}
              </span>
              ${esc(a.titulo)}
            </span>
            <div class="flex gap-2">
              <button onclick="editarAulaAdmin(${a.id}, ${mod.id})" class="text-xs text-gray-500 hover:underline">Editar</button>
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
  document.getElementById('aula-tipo').value     = 'video';
  document.getElementById('aula-e-prova').value  = '0';
  document.getElementById('aula-url').value      = '';
  document.getElementById('aula-descricao').value = '';
  document.getElementById('aula-ordem').value    = '0';
  document.getElementById('aula-duracao').value  = '0';
  document.getElementById('aula-publica').checked = false;
  document.getElementById('aula-quizz-qtd-perguntas').value = 1;
  document.getElementById('aula-exemplar-global').checked = false;
  document.getElementById('aula-nota-corte-tipo').value = 'percentual';
  document.getElementById('aula-nota-corte-valor').value = 70;
  document.getElementById('aula-tempo-limite').value = 0;
  document.getElementById('aula-bloquear-proctoring').checked = false;

  // Materiais só podem ser anexados depois que a aula existe (precisa de um aula_id)
  document.getElementById('bloco-materiais').classList.add('hidden');

  if (typeof toggleCamposTipoAula === 'function') toggleCamposTipoAula();
  document.getElementById('modal-aula').classList.remove('hidden');
}

function editarAulaAdmin(aulaId, moduloId) {
  const mod = _cursoAdminData.modulos.find(m => m.id === moduloId);
  if (!mod) return;
  const a = mod.aulas.find(l => l.id === aulaId);
  if (!a) return;
  
  document.getElementById('aula-modal-titulo').textContent = 'Editar Aula';
  document.getElementById('aula-id').value       = a.id;
  document.getElementById('aula-modulo-id').value = moduloId;
  document.getElementById('aula-titulo').value   = a.titulo;
  document.getElementById('aula-tipo').value     = a.tipo || 'video';
  document.getElementById('aula-e-prova').value  = a.e_prova == 1 ? '1' : '0';
  document.getElementById('aula-url').value      = a.video_url || '';
  document.getElementById('aula-descricao').value = a.descricao || '';
  document.getElementById('aula-ordem').value    = a.ordem || 0;
  document.getElementById('aula-duracao').value  = a.duracao_min || 0;
  document.getElementById('aula-publica').checked = a.publica == 1;
  document.getElementById('aula-quizz-qtd-perguntas').value = a.quizz_qtd_perguntas || 1;
  document.getElementById('aula-exemplar-global').checked = a.exemplar_global == 1;
  document.getElementById('aula-nota-corte-tipo').value = a.nota_corte_tipo || 'percentual';
  document.getElementById('aula-nota-corte-valor').value = a.nota_corte_valor || 70;
  document.getElementById('aula-tempo-limite').value = a.tempo_limite_minutos || 0;
  document.getElementById('aula-bloquear-proctoring').checked = a.bloquear_proctoring == 1;

  document.getElementById('bloco-materiais').classList.remove('hidden');
  carregarMateriaisAula(aulaId);

  if (typeof toggleCamposTipoAula === 'function') toggleCamposTipoAula();
  document.getElementById('modal-aula').classList.remove('hidden');
}

// Materiais de aula (upload até 5MB, vários por aula)
async function carregarMateriaisAula(aulaId) {
  const box = document.getElementById('materiais-lista');
  box.innerHTML = 'Carregando...';
  try {
    const materiais = await apiFetch(_B() + '/api/aulas/' + aulaId + '/materiais');
    box.innerHTML = materiais.length
      ? materiais.map(m => `
          <div class="flex items-center justify-between bg-white border border-slate-200 rounded px-2 py-1">
            <a href="${_B()}/api/aulas/materiais/${m.id}/download" target="_blank" class="text-primary hover:underline truncate">${esc(m.nome_arquivo)}</a>
            <button type="button" onclick="excluirMaterialAula(${m.id}, ${aulaId})" class="text-red-500 hover:underline ml-2">Excluir</button>
          </div>
        `).join('')
      : '<p class="text-slate-400">Nenhum material anexado.</p>';
  } catch (e) { box.innerHTML = `<p class="text-red-500">${e.message}</p>`; }
}

async function enviarMaterialAula() {
  const aulaId = document.getElementById('aula-id').value;
  const input = document.getElementById('material-arquivo');
  const err = document.getElementById('material-erro');
  err.classList.add('hidden');
  if (!aulaId) { err.textContent = 'Salve a aula antes de anexar materiais.'; err.classList.remove('hidden'); return; }
  if (!input.files.length) return;
  const file = input.files[0];
  if (file.size > 5 * 1024 * 1024) { err.textContent = 'Arquivo maior que 5MB.'; err.classList.remove('hidden'); return; }

  const fd = new FormData();
  fd.append('arquivo', file);
  try {
    const res = await fetch(_B() + '/api/aulas/' + aulaId + '/materiais', { method: 'POST', credentials: 'include', body: fd });
    const data = await res.json();
    if (!res.ok) throw new Error(data.error || 'Erro ao enviar material.');
    input.value = '';
    carregarMateriaisAula(aulaId);
  } catch (e) { err.textContent = e.message; err.classList.remove('hidden'); }
}

async function excluirMaterialAula(materialId, aulaId) {
  if (!confirm('Excluir este material?')) return;
  try { await apiDelete(_B() + '/api/aulas/materiais/' + materialId); carregarMateriaisAula(aulaId); }
  catch (e) { alert(e.message); }
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
        titulo:               document.getElementById('aula-titulo').value,
        url:                  document.getElementById('aula-url').value || null,
        descricao:            document.getElementById('aula-descricao').value || null,
        ordem:                parseInt(document.getElementById('aula-ordem').value) || 0,
        duracao_min:          parseInt(document.getElementById('aula-duracao').value) || 0,
        modulo_id:            moduloId,
        tipo:                 document.getElementById('aula-tipo').value,
        e_prova:              parseInt(document.getElementById('aula-e-prova').value) || 0,
        publica:              document.getElementById('aula-publica').checked ? 1 : 0,
        quizz_qtd_perguntas:  parseInt(document.getElementById('aula-quizz-qtd-perguntas').value) || 1,
        exemplar_global:      document.getElementById('aula-exemplar-global').checked ? 1 : 0,
        nota_corte_tipo:      document.getElementById('aula-nota-corte-tipo').value,
        nota_corte_valor:     parseInt(document.getElementById('aula-nota-corte-valor').value) || 70,
        tempo_limite_minutos: parseInt(document.getElementById('aula-tempo-limite').value) || 0,
        bloquear_proctoring:  document.getElementById('aula-bloquear-proctoring').checked ? 1 : 0,
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

// ============ CURSO DETALHE EDIT ACTIONS ============
async function editarCursoInfo() {
  if (!_cursoAdminData) return;

  // Preenche categorias e instrutores no modal de edição
  const catSelect = document.getElementById('ec-categoria');
  if (catSelect && catSelect.children.length <= 1) {
    const cats = await apiFetch(_B() + '/api/categorias');
    cats.forEach(c => {
      const opt = document.createElement('option');
      opt.value = c.id; opt.textContent = c.nome;
      catSelect.appendChild(opt);
    });
  }

  const instSelect = document.getElementById('ec-instrutor');
  if (instSelect && instSelect.children.length <= 1) {
    const insts = await apiFetch(_B() + '/api/instrutores');
    insts.forEach(i => {
      const opt = document.createElement('option');
      opt.value = i.id; opt.textContent = i.nome;
      instSelect.appendChild(opt);
    });
  }

  // Pré-requisitos: lista todos os outros cursos pra escolher
  const preSelect = document.getElementById('ec-prerequisitos');
  if (preSelect) {
    const todosCursos = await apiFetch(_B() + '/api/cursos');
    const prereqIds = (_cursoAdminData.prerequisitos || []).map(p => p.id);
    preSelect.innerHTML = todosCursos
      .filter(c => c.id !== _cursoAdminData.id)
      .map(c => `<option value="${c.id}" ${prereqIds.includes(c.id) ? 'selected' : ''}>${esc(c.titulo)}</option>`)
      .join('');
  }

  document.getElementById('ec-titulo').value            = _cursoAdminData.titulo || '';
  document.getElementById('ec-codigo').value            = _cursoAdminData.codigo || '';
  document.getElementById('ec-nome-certificado').value  = _cursoAdminData.nome_certificado || '';
  document.getElementById('ec-prazo-acesso').value      = _cursoAdminData.prazo_acesso_dias !== null ? _cursoAdminData.prazo_acesso_dias : '';
  document.getElementById('ec-descricao').value         = _cursoAdminData.descricao || '';
  document.getElementById('ec-carga').value             = _cursoAdminData.carga_horaria_horas || 0;
  document.getElementById('ec-preco').value             = _cursoAdminData.preco || 0;
  document.getElementById('ec-categoria').value         = _cursoAdminData.categoria_id || '';
  document.getElementById('ec-instrutor').value         = _cursoAdminData.instrutor_id || '';
  document.getElementById('ec-thumb').value             = _cursoAdminData.thumb_url || '';
  document.getElementById('ec-ativo').checked           = _cursoAdminData.ativo == 1;
  document.getElementById('ec-publico').checked         = _cursoAdminData.publico == 1;
  document.getElementById('ec-disponivel-loja').checked = _cursoAdminData.disponivel_loja == 1;
  document.getElementById('ec-exibir-instrutor').checked = _cursoAdminData.exibir_instrutor == 1;

  document.getElementById('ec-video-explicativo').value = _cursoAdminData.video_url_explicativo || '';
  document.getElementById('ec-diferencial').value       = _cursoAdminData.diferencial || '';
  document.getElementById('ec-conteudo').value          = _cursoAdminData.conteudo_programatico || '';
  document.getElementById('ec-publico-alvo').value      = _cursoAdminData.publico_alvo || '';
  document.getElementById('ec-condicoes').value         = _cursoAdminData.condicoes || '';
  document.getElementById('ec-vis-nome').checked             = _cursoAdminData.vis_nome != 0;
  document.getElementById('ec-vis-breve-descricao').checked  = _cursoAdminData.vis_breve_descricao != 0;
  document.getElementById('ec-vis-carga-horaria').checked    = _cursoAdminData.vis_carga_horaria != 0;
  document.getElementById('ec-vis-valor').checked            = _cursoAdminData.vis_valor != 0;
  document.getElementById('ec-vis-descricao').checked        = _cursoAdminData.vis_descricao != 0;
  document.getElementById('ec-vis-video').checked            = _cursoAdminData.vis_video != 0;
  document.getElementById('ec-vis-diferencial').checked      = _cursoAdminData.vis_diferencial != 0;
  document.getElementById('ec-vis-conteudo').checked         = _cursoAdminData.vis_conteudo != 0;
  document.getElementById('ec-vis-publico-alvo').checked     = _cursoAdminData.vis_publico_alvo != 0;
  document.getElementById('ec-vis-condicoes').checked        = _cursoAdminData.vis_condicoes != 0;

  document.getElementById('ec-form-erro').classList.add('hidden');
  document.getElementById('modal-editar-curso').classList.remove('hidden');
}

function fecharModalEditarCurso() {
  document.getElementById('modal-editar-curso').classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', () => {
  const formEc = document.getElementById('form-editar-curso');
  if (formEc) {
    formEc.addEventListener('submit', async (e) => {
      e.preventDefault();
      const btn = document.getElementById('btn-salvar-ec');
      const err = document.getElementById('ec-form-erro');
      btn.disabled = true; err.classList.add('hidden');

      const body = {
        titulo:              document.getElementById('ec-titulo').value,
        codigo:              document.getElementById('ec-codigo').value.trim().toUpperCase(),
        nome_certificado:    document.getElementById('ec-nome-certificado').value.trim() || null,
        prazo_acesso_dias:   document.getElementById('ec-prazo-acesso').value !== '' ? parseInt(document.getElementById('ec-prazo-acesso').value) : null,
        descricao:           document.getElementById('ec-descricao').value,
        thumb_url:           document.getElementById('ec-thumb').value || null,
        carga_horaria_horas: parseInt(document.getElementById('ec-carga').value) || 0,
        preco:               parseFloat(document.getElementById('ec-preco').value) || 0,
        categoria_id:        document.getElementById('ec-categoria').value || null,
        instrutor_id:        document.getElementById('ec-instrutor').value || null,
        ativo:               document.getElementById('ec-ativo').checked ? 1 : 0,
        publico:             document.getElementById('ec-publico').checked ? 1 : 0,
        disponivel_loja:     document.getElementById('ec-disponivel-loja').checked ? 1 : 0,
        exibir_instrutor:    document.getElementById('ec-exibir-instrutor').checked ? 1 : 0,

        video_url_explicativo: document.getElementById('ec-video-explicativo').value || null,
        diferencial:            document.getElementById('ec-diferencial').value || null,
        conteudo_programatico:  document.getElementById('ec-conteudo').value || null,
        publico_alvo:            document.getElementById('ec-publico-alvo').value || null,
        condicoes:               document.getElementById('ec-condicoes').value || null,
        vis_nome:             document.getElementById('ec-vis-nome').checked ? 1 : 0,
        vis_breve_descricao:  document.getElementById('ec-vis-breve-descricao').checked ? 1 : 0,
        vis_carga_horaria:    document.getElementById('ec-vis-carga-horaria').checked ? 1 : 0,
        vis_valor:            document.getElementById('ec-vis-valor').checked ? 1 : 0,
        vis_descricao:        document.getElementById('ec-vis-descricao').checked ? 1 : 0,
        vis_video:            document.getElementById('ec-vis-video').checked ? 1 : 0,
        vis_diferencial:      document.getElementById('ec-vis-diferencial').checked ? 1 : 0,
        vis_conteudo:         document.getElementById('ec-vis-conteudo').checked ? 1 : 0,
        vis_publico_alvo:     document.getElementById('ec-vis-publico-alvo').checked ? 1 : 0,
        vis_condicoes:        document.getElementById('ec-vis-condicoes').checked ? 1 : 0,

        prerequisitos: Array.from(document.getElementById('ec-prerequisitos').selectedOptions).map(o => parseInt(o.value)),
      };

      try {
        await apiPut(_B() + '/api/cursos/' + cursoAdminId, body);
        fecharModalEditarCurso();
        await carregarCursoAdmin(cursoAdminId);
      } catch (ex) {
        err.textContent = ex.message; err.classList.remove('hidden');
      } finally { btn.disabled = false; }
    });
  }
});

async function excluirCurso() {
  if (!confirm('Deseja excluir permanentemente este curso e todos os seus dados? Esta ação não pode ser desfeita.')) return;
  try {
    await apiDelete(_B() + '/api/cursos/' + cursoAdminId);
    window.location.href = _B() + '/admin/cursos';
  } catch (e) { alert('Erro ao excluir curso: ' + e.message); }
}
