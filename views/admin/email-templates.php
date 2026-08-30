<?php $pageTitle = 'E-mails — ActShare'; ?>
<?php require __DIR__ . '/../layout/admin-header.php'; ?>

<div class="mb-8">
  <h1 class="text-2xl font-bold text-slate-800">Configuração de E-mails</h1>
  <p class="text-xs text-slate-400 mt-1">Templates disparados automaticamente pela plataforma. Use <code class="bg-slate-100 px-1 rounded">{nome}</code>, <code class="bg-slate-100 px-1 rounded">{curso}</code>, <code class="bg-slate-100 px-1 rounded">{pedido_id}</code>, <code class="bg-slate-100 px-1 rounded">{total}</code>, <code class="bg-slate-100 px-1 rounded">{link_site}</code> conforme o template.</p>
</div>

<div id="templates-list" class="space-y-4">Carregando...</div>

<script src="<?= BASE_PATH ?>/assets/js/admin.js?v=10"></script>
<script>
  document.addEventListener('DOMContentLoaded', carregarEmailTemplates);

  async function carregarEmailTemplates() {
    const list = document.getElementById('templates-list');
    try {
      const templates = await apiFetch(BASE + '/api/admin/email-templates');
      list.innerHTML = templates.map(t => `
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
          <div class="flex items-center justify-between mb-3">
            <div>
              <h2 class="font-bold text-slate-800 text-sm">${t.nome}</h2>
              <p class="text-[10px] text-slate-400 uppercase font-mono">${t.chave}</p>
            </div>
            <div class="flex items-center gap-3">
              <label class="flex items-center gap-1.5 text-xs text-slate-500">
                <input type="checkbox" id="tpl-ativo-${t.chave}" ${t.ativo ? 'checked' : ''} class="rounded accent-primary">
                Ativo
              </label>
              <button onclick="salvarEmailTemplate('${t.chave}')" class="bg-primary text-white text-xs font-semibold px-3 py-1.5 rounded-lg hover:bg-slate-800">Salvar</button>
            </div>
          </div>
          <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Assunto</label>
          <input type="text" id="tpl-assunto-${t.chave}" value="${t.assunto.replace(/"/g, '&quot;')}" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm mb-3">
          <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Corpo</label>
          <textarea id="tpl-corpo-${t.chave}" rows="5" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm font-mono">${t.corpo}</textarea>
        </div>
      `).join('');
    } catch (e) {
      list.innerHTML = `<p class="text-red-500 text-sm">Erro: ${e.message}</p>`;
    }
  }

  async function salvarEmailTemplate(chave) {
    try {
      await apiPut(BASE + '/api/admin/email-templates/' + chave, {
        assunto: document.getElementById('tpl-assunto-' + chave).value,
        corpo: document.getElementById('tpl-corpo-' + chave).value,
        ativo: document.getElementById('tpl-ativo-' + chave).checked ? 1 : 0,
      });
      alert('Template salvo com sucesso!');
    } catch (e) {
      alert('Erro ao salvar: ' + e.message);
    }
  }
</script>

    </div></main></div></body></html>
