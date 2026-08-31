<?php $pageTitle = 'Exames — ActShare'; ?>
<?php require __DIR__ . '/../layout/admin-header.php'; ?>

<div class="mb-8">
  <h1 class="text-2xl font-bold text-slate-800">Exames</h1>
  <p class="text-xs text-slate-400 mt-1">Avaliação e Exame Exemplar Global (QM/AU/TL) — vinculados a um treinamento na tela de edição do curso.</p>
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

<div class="bg-amber-50 border border-amber-200 rounded-xl p-5 mt-6 text-sm text-amber-800">
  O cadastro completo de produtos de Avaliação/Exame (banco de questões próprio, prazos, nota de corte, tempo limite, produto avulso sem
  vínculo a um treinamento) ainda está em construção — por enquanto, o vínculo de Avaliação/Exame a um curso é feito na própria
  tela de edição do curso, em "Exame Exemplar Global".
</div>

<script src="<?= BASE_PATH ?>/assets/js/admin.js?v=12"></script>
<script>
  document.addEventListener('DOMContentLoaded', carregarTextoExplicacaoExames);

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
