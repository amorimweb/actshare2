const fluxosProduto = {
  0: 'O cadastro anterior será preservado. Escolha um modelo para novos produtos.',
  '': 'Primeiro escolha um modelo de produto para definir as etapas.',
  1: 'Treinamento e quizzes → pesquisa do treinamento → certificado de participação.',
  2: 'Treinamento e quizzes → pesquisa do treinamento → avaliação. Reprovado: participação. Aprovado: sucesso.',
  3: 'Avaliação → pesquisa da avaliação → resultado. Aprovado: sucesso. Reprovado: nenhum certificado.',
  4: 'Exame EXGL → pesquisa do exame → resultado. Aprovado: sucesso. Reprovado: nenhum certificado.',
  5: 'Dois caminhos independentes: treinamento → pesquisa → participação; EXGL → pesquisa → sucesso se aprovado.'
};
function atualizarModeloProduto(prefix) {
  const modelo = document.getElementById(prefix + '-modelo').value;
  document.getElementById(prefix + '-dados').hidden = modelo === '';
  document.getElementById(prefix + '-fluxo').textContent = fluxosProduto[modelo];
  document.querySelector(`[data-produto-config="${prefix}"]`).querySelectorAll('[data-modelos]').forEach(el => el.hidden = !el.dataset.modelos.split(',').includes(modelo));
  // Produto avulso (só Avaliação ou só Exame EXGL, sem treinamento) não tem carga horária.
  const campoCarga = document.getElementById(prefix + '-campo-carga');
  if (campoCarga) campoCarga.hidden = ['3', '4'].includes(modelo);
}
async function preencherConfigProduto(prefix, curso = {}) {
  const root = document.querySelector(`[data-produto-config="${prefix}"]`);
  if (!root) return;
  let config = {};
  try { config = JSON.parse(curso.componentes_config || '{}'); } catch (_) {}
  document.getElementById(prefix + '-modelo').value = curso.id ? (curso.modelo_produto || 0) : '';
  document.getElementById(prefix + '-tipo_exgl').value = config.tipo_exgl || 'QM';
  root.querySelectorAll('textarea').forEach(field => field.value = config[field.id.slice(prefix.length + 1)] || '');
  document.getElementById(prefix + '-descricao_avaliacao_modelo2').value = config.descricao_avaliacao || '';
  document.getElementById(prefix + '-modelo').required = !curso.id;
  document.querySelector(`#${prefix}-modelo option[value="0"]`).hidden = !curso.id;
  atualizarModeloProduto(prefix);
  try {
    const [pesquisas, certificados] = await Promise.all([apiFetch(BASE + '/api/admin/pesquisas'), apiFetch(BASE + '/api/admin/certificado-templates')]);
    root.querySelectorAll('[data-pesquisa]').forEach(select => {
      const tipo = select.dataset.pesquisa === 'exame' ? 'exame_exgl' : select.dataset.pesquisa;
      select.replaceChildren(new Option('Sem pesquisa de satisfação', ''));
      pesquisas.filter(p => p.aplicavel_a === tipo).forEach(p => select.add(new Option(p.nome, p.id)));
      select.value = config['pesquisa_' + select.dataset.pesquisa + '_id'] || (tipo === 'treinamento' && !curso.id ? '1' : '');
    });
    root.querySelectorAll('[data-certificado]').forEach(select => {
      select.replaceChildren(new Option('Certificado padrão do sistema', ''));
      certificados.filter(t => t.ativo && t.tipo === (select.dataset.certificado === 'participacao' ? 'participacao' : 'sucesso') && (select.dataset.certificado !== 'exgl' || t.layout_base === 'exgl')).forEach(t => select.add(new Option(t.nome, t.id)));
      select.value = config['certificado_' + select.dataset.certificado + '_id'] || '';
    });
    document.getElementById(prefix + '-config-erro').textContent = '';
  } catch (e) { document.getElementById(prefix + '-config-erro').textContent = 'Não foi possível carregar as bibliotecas: ' + e.message; }
}
function lerConfigProduto(prefix) {
  const modelo = Number(document.getElementById(prefix + '-modelo')?.value || 0);
  if (!modelo) return {};
  const config = {};
  document.querySelector(`[data-produto-config="${prefix}"]`).querySelectorAll('select,textarea').forEach(field => {
    const key = field.id.slice(prefix.length + 1);
    if (key !== 'modelo') config[key] = field.value || null;
  });
  if (modelo === 2) config.descricao_avaliacao = config.descricao_avaliacao_modelo2;
  delete config.descricao_avaliacao_modelo2;
  return {modelo_produto: modelo, componentes_config: config};
}
