// Lógica do painel do aluno (substitui stores/student.ts)
const _B = () => (typeof BASE !== 'undefined' ? BASE : '');

function esc(s) {
  if (!s) return '';
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

async function carregarMatriculas() {
  const grid  = document.getElementById('matriculas-grid');
  const empty = document.getElementById('matriculas-empty');
  if (!grid) return;

  try {
    const matriculas = await apiFetch(_B() + '/api/aluno/matriculas');

    if (!matriculas.length) {
      grid.classList.add('hidden');
      empty?.classList.remove('hidden');
      return;
    }

    grid.innerHTML = matriculas.map(m => {
      const prog = Math.round(m.progresso_total || 0);
      return `
        <a href="${_B()}/painel/curso/${m.curso_id}" class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-md transition-shadow flex flex-col">
          ${m.thumb_url
            ? `<img src="${esc(m.thumb_url)}" alt="" class="w-full h-36 object-cover">`
            : `<div class="w-full h-36 bg-gradient-to-br from-primary to-blue-700"></div>`
          }
          <div class="p-4 flex-1 flex flex-col">
            <h3 class="font-semibold text-gray-800 mb-2 line-clamp-2">${esc(m.curso_titulo)}</h3>
            <div class="mt-auto">
              <div class="flex justify-between text-xs text-gray-500 mb-1">
                <span>Progresso</span><span>${prog}%</span>
              </div>
              <div class="w-full bg-gray-100 rounded-full h-2">
                <div class="bg-secondary rounded-full h-2 transition-all" style="width:${prog}%"></div>
              </div>
              ${m.concluido
                ? '<p class="text-xs text-secondary font-medium mt-2">✓ Concluído</p>'
                : `<p class="text-xs text-gray-400 mt-2">${prog > 0 ? 'Em andamento' : 'Não iniciado'}</p>`
              }
            </div>
          </div>
        </a>
      `;
    }).join('');
  } catch (e) {
    grid.innerHTML = '<p class="col-span-full text-center text-red-400 py-8">Erro ao carregar matrículas.</p>';
  }
}
