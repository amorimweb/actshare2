// Máscaras de CPF/CNPJ e Telefone — aplicadas via input listener, com o valor
// sempre normalizado (só dígitos) internamente e reformatado a cada tecla, o
// que impede colar/digitar mais caracteres do que o formato permite.

function apenasDigitos(v) {
  return (v || '').replace(/\D/g, '');
}

function formatarDocumento(digits) {
  digits = digits.slice(0, 14);
  if (digits.length <= 11) {
    // CPF: 000.000.000-00
    return digits
      .replace(/(\d{3})(\d)/, '$1.$2')
      .replace(/(\d{3})(\d)/, '$1.$2')
      .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
  }
  // CNPJ: 00.000.000/0000-00
  return digits
    .replace(/(\d{2})(\d)/, '$1.$2')
    .replace(/(\d{3})(\d)/, '$1.$2')
    .replace(/(\d{3})(\d)/, '$1/$2')
    .replace(/(\d{4})(\d{1,2})$/, '$1-$2');
}

function formatarTelefone(digits) {
  digits = digits.slice(0, 11);
  if (digits.length <= 10) {
    // (00) 0000-0000
    return digits
      .replace(/(\d{2})(\d)/, '($1) $2')
      .replace(/(\d{4})(\d{1,4})$/, '$1-$2');
  }
  // (00) 00000-0000
  return digits
    .replace(/(\d{2})(\d)/, '($1) $2')
    .replace(/(\d{5})(\d{1,4})$/, '$1-$2');
}

function documentoValido(valor) {
  const d = apenasDigitos(valor);
  return d.length === 11 || d.length === 14;
}

function telefoneValido(valor) {
  const d = apenasDigitos(valor);
  return d.length === 10 || d.length === 11;
}

function aplicarMascaraDocumento(input) {
  if (!input || input.dataset.maskBound) return;
  input.dataset.maskBound = '1';
  input.setAttribute('inputmode', 'numeric');
  input.addEventListener('input', () => {
    input.value = formatarDocumento(apenasDigitos(input.value));
  });
  input.addEventListener('paste', (e) => {
    e.preventDefault();
    const texto = (e.clipboardData || window.clipboardData).getData('text');
    input.value = formatarDocumento(apenasDigitos(texto));
  });
}

function aplicarMascaraTelefone(input) {
  if (!input || input.dataset.maskBound) return;
  input.dataset.maskBound = '1';
  input.setAttribute('inputmode', 'numeric');
  input.addEventListener('input', () => {
    input.value = formatarTelefone(apenasDigitos(input.value));
  });
  input.addEventListener('paste', (e) => {
    e.preventDefault();
    const texto = (e.clipboardData || window.clipboardData).getData('text');
    input.value = formatarTelefone(apenasDigitos(texto));
  });
}

// Aplica automaticamente em qualquer input já marcado no HTML com
// data-mask="documento" ou data-mask="telefone", nas páginas que incluem este script.
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-mask="documento"]').forEach(aplicarMascaraDocumento);
  document.querySelectorAll('[data-mask="telefone"]').forEach(aplicarMascaraTelefone);
});
