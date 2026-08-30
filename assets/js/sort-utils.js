// Ordenação A-Z / Z-A reutilizável para as tabelas dos perfis Admin e Gestor.
// Uso: ordenarLista(array, 'nome', 'asc') — aceita string, número ou data (string
// já em formato comparável) e lida com null/undefined mandando pro final.

function ordenarLista(lista, campo, direcao) {
  const getVal = (obj) => {
    const v = campo.split('.').reduce((o, k) => (o == null ? o : o[k]), obj);
    return v;
  };
  const copia = [...lista];
  copia.sort((a, b) => {
    let va = getVal(a);
    let vb = getVal(b);
    if (va == null && vb == null) return 0;
    if (va == null) return 1;
    if (vb == null) return -1;
    if (typeof va === 'number' && typeof vb === 'number') {
      return direcao === 'asc' ? va - vb : vb - va;
    }
    va = String(va).toLowerCase();
    vb = String(vb).toLowerCase();
    const cmp = va.localeCompare(vb, 'pt-BR');
    return direcao === 'asc' ? cmp : -cmp;
  });
  return copia;
}

// Gera o HTML de uma seta clicável de ordenação para um <th>.
// campo: nome do campo a ordenar · estadoId: id do elemento <th>/<span> que
// guarda o estado atual via data-sort-campo/data-sort-dir · onClick: nome da
// função global a chamar (deve reordenar e re-renderizar a tabela).
function setaOrdenacao(campo, onClickFn) {
  return `<button type="button" onclick="${onClickFn}('${campo}')" class="inline-flex items-center ml-1 align-middle text-slate-400 hover:text-primary transition-colors" title="Ordenar">
    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 3l4 5H6l4-5zM10 17l-4-5h8l-4 5z"/></svg>
  </button>`;
}
