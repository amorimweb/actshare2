<?php $pageTitle = 'Meu produto — ActShare'; require __DIR__ . '/layout/header.php'; ?>
<main class="max-w-4xl mx-auto px-4 py-10">
  <a href="<?= BASE_PATH ?>/painel" class="text-sm text-primary">← Meus produtos</a>
  <h1 id="produto-titulo" class="text-2xl font-bold mt-5 mb-3">Carregando...</h1>
  <p id="produto-explicacao" class="text-gray-500 mb-6"></p>
  <div id="produto-componentes" class="space-y-5"></div>
</main>
<script>
const produtoId = <?= (int)$_GET['id'] ?>;
const escaparProduto = text => String(text ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
async function carregarProduto() {
  try {
    const data = await apiFetch(BASE + '/api/aluno/produto/' + produtoId);
    if (data.legado) { location.href = BASE + '/painel/curso/' + produtoId; return; }
    const s = data.status;
    document.getElementById('produto-titulo').textContent = data.curso.titulo;
    document.getElementById('produto-explicacao').textContent = s.modelo === 5 ? 'Treinamento e exame são independentes. Você pode começar por qualquer um deles.' : 'Siga as etapas abaixo para concluir seu produto.';
    let html = '';
    if (s.treinamento) {
      html += `<section class="bg-white border rounded-xl p-5"><h2 class="font-bold">Treinamento e quizzes</h2><p class="text-sm my-2">Progresso: ${s.treinamento.percentual}%</p><a class="text-primary font-semibold" href="${BASE}/painel/curso/${produtoId}">Abrir treinamento</a>`;
      html += `<p class="text-sm mt-3">Pesquisa do treinamento: ${s.treinamento.pesquisa_ok ? 'concluída ou não aplicável' : 'pendente'}</p>`;
      if (s.treinamento.aulas_completas && !s.treinamento.pesquisa_ok) html += '<button class="text-primary font-semibold mt-2" onclick="abrirPesquisaTreinamento()">Responder pesquisa do treinamento</button>';
      html += '</section>';
    }
    if (s.exame) {
      const titulo = s.exame.tipo === 'AVALIACAO' ? 'Avaliação' : 'Exame Exemplar Global ' + s.exame.tipo;
      html += `<section class="bg-white border rounded-xl p-5"><h2 class="font-bold">${escaparProduto(titulo)}</h2><p class="text-sm my-2">${!s.exame.pode_iniciar ? 'Conclua o treinamento e sua pesquisa para liberar a avaliação.' : s.exame.finalizado ? (!s.exame.pesquisa_ok ? 'Responda a pesquisa para ver o resultado.' : 'Resultado: ' + escaparProduto(s.exame.resultado)) : 'Disponível para iniciar.'}</p>`;
      if (s.exame.pode_iniciar) html += `<a class="text-primary font-semibold" href="${BASE}/painel/exame/${produtoId}?exame_id=${s.exame.id}">${s.exame.finalizado && !s.exame.pesquisa_ok ? 'Responder pesquisa e ver resultado' : 'Abrir ' + escaparProduto(titulo)}</a>`;
      html += '</section>';
    }
    html += '<section class="bg-white border rounded-xl p-5"><h2 class="font-bold mb-3">Certificados</h2>';
    const partes = s.modelo === 5 ? ['treinamento','exame'] : [s.modelo === 1 ? 'treinamento' : s.modelo === 2 || s.modelo === 3 ? 'avaliacao' : 'exame'];
    partes.forEach(parte => {
      const cert = s.certificados.find(c => c.parte === parte);
      html += cert ? `<a class="block text-emerald-700 font-semibold my-2" target="_blank" href="${BASE}/certificado-produto?curso=${produtoId}&parte=${parte}">Certificado de ${cert.tipo === 'sucesso' ? 'sucesso' : 'participação'} — ${parte}</a>` : `<button disabled class="block text-gray-400 my-2">Certificado — ${parte} (indisponível)</button>`;
    });
    document.getElementById('produto-componentes').innerHTML = html + '</section><div id="produto-pesquisa"></div>';
  } catch (e) { document.getElementById('produto-titulo').textContent = e.message; }
}
async function abrirPesquisaTreinamento() {
  const data = await apiFetch(BASE + '/api/aluno/produto/' + produtoId);
  const pesquisa = await apiFetch(BASE + '/api/aluno/satisfacao/status/' + data.matricula.id);
  if (pesquisa.respondida) { await carregarProduto(); return; }
  document.getElementById('produto-pesquisa').innerHTML = `<form id="produto-pesquisa-form" class="bg-white border rounded-xl p-5"><h2 class="font-bold mb-4">Pesquisa do treinamento</h2><p class="text-sm mb-4">1 = Muito insatisfeito · 5 = Muito satisfeito</p>${pesquisa.perguntas.map(p => `<label class="block mb-4"><span>${escaparProduto(p.texto)}</span><select required name="${p.id}" class="border rounded ml-3"><option value="">Escolha uma nota</option>${[1,2,3,4,5].map(n => `<option>${n}</option>`).join('')}</select></label>`).join('')}<button class="bg-primary text-white px-4 py-2 rounded">Enviar pesquisa</button><p id="produto-pesquisa-erro" role="alert"></p></form>`;
  document.getElementById('produto-pesquisa-form').onsubmit = async event => {
    event.preventDefault();
    try { await apiPost(BASE + '/api/aluno/satisfacao/responder', {matricula_id:data.matricula.id, respostas:Object.fromEntries(new FormData(event.target))}); await carregarProduto(); }
    catch (e) { document.getElementById('produto-pesquisa-erro').textContent = e.message; }
  };
}
document.addEventListener('DOMContentLoaded', carregarProduto);
</script>
<?php require __DIR__ . '/layout/footer.php'; ?>
