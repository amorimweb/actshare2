<?php $pageTitle = 'Exames — ActShare'; ?>
<?php require __DIR__ . '/../layout/admin-header.php'; ?>

<div class="flex items-center justify-between mb-8 gap-4 flex-wrap">
  <div>
    <h1 class="text-2xl font-bold text-slate-800">Exames</h1>
    <p class="text-xs text-slate-400 mt-1">Avaliação e Exame Exemplar Global (QM/AU/EM/TL), como produto avulso (sem treinamento) ou vinculados a um curso.</p>
  </div>
  <a href="<?= BASE_PATH ?>/admin/cursos?novo=avulso" class="bg-primary text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-blue-900 transition-colors shrink-0">
    + Novo Produto de Avaliação/Exame
  </a>
</div>

<div class="bg-slate-50 border border-slate-200 rounded-xl p-5 text-sm text-slate-600 mb-6">
  Um produto de Avaliação ou Exame Exemplar Global avulso (sem treinamento) é criado como um Produto normal, escolhendo o modelo
  "Só Avaliação" ou "Só Exame Exemplar Global" — o banco de questões, prazos, nota de corte e tempo limite ficam na própria tela de edição do
  produto, em "Exame Exemplar Global". Esta tela lista só os produtos cadastrados nesses dois modelos.
</div>

<div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-6">
  <table class="w-full text-sm">
    <thead class="bg-slate-50 text-left">
      <tr>
        <th class="px-5 py-3 font-medium text-slate-600">Título</th>
        <th class="px-5 py-3 font-medium text-slate-600">Tipo</th>
        <th class="px-5 py-3 font-medium text-slate-600">Preço</th>
        <th class="px-5 py-3 font-medium text-slate-600">Status</th>
        <th class="px-5 py-3 font-medium text-slate-600">Ações</th>
      </tr>
    </thead>
    <tbody id="exames-avulsos-tbody" class="divide-y divide-slate-100">
      <tr><td colspan="5" class="text-center py-8 text-slate-400">Carregando...</td></tr>
    </tbody>
  </table>
</div>

<div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
  <div class="flex items-center justify-between mb-3">
    <div>
      <h2 class="font-bold text-slate-800 text-sm">Texto explicativo (página pública "Avaliação e Exames")</h2>
      <p class="text-xs text-slate-400 mt-0.5">Exibido para o aluno ao clicar no link "?" ao lado das opções de Avaliação/Exame, na ficha do curso e no card da loja.</p>
    </div>
    <button onclick="salvarTextoExplicacaoExames()" id="btn-salvar-texto-exames" class="bg-primary text-white text-xs font-semibold uppercase tracking-wider px-4 py-2.5 rounded-lg hover:bg-slate-800 transition-all">Salvar</button>
  </div>
  <textarea id="texto-explicacao-exames" rows="6" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm"></textarea>
  <div id="texto-exames-msg" class="hidden mt-3 text-xs rounded-lg px-3 py-2"></div>
</div>

<script src="<?= BASE_PATH ?>/assets/js/admin.js?v=19"></script>
<script>
  document.addEventListener('DOMContentLoaded', carregarTextoExplicacaoExames);
  document.addEventListener('DOMContentLoaded', carregarExamesAvulsos);

  async function carregarTextoExplicacaoExames() {
    try {
      const config = await apiFetch(BASE + '/api/admin/configuracoes');
      document.getElementById('texto-explicacao-exames').value = config.texto_explicacao_exames || '';
    } catch (e) {
      alert('Erro ao carregar: ' + e.message);
    }
  }

  async function salvarTextoExplicacaoExames() {
    const btn = document.getElementById('btn-salvar-texto-exames');
    const msg = document.getElementById('texto-exames-msg');
    btn.disabled = true;
    try {
      await apiPut(BASE + '/api/admin/configuracoes', {
        texto_explicacao_exames: document.getElementById('texto-explicacao-exames').value,
      });
      msg.className = 'mt-3 text-xs rounded-lg px-3 py-2 bg-green-50 text-green-700 border border-green-200 block';
      msg.textContent = 'Texto salvo com sucesso!';
    } catch (e) {
      msg.className = 'mt-3 text-xs rounded-lg px-3 py-2 bg-red-50 text-red-700 border border-red-200 block';
      msg.textContent = 'Erro: ' + e.message;
    } finally {
      btn.disabled = false;
    }
  }
</script>

    </div></main></div></body></html>
