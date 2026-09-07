(() => {
  'use strict';
  // Only this small formatting vocabulary is interpreted. HTML stays literal text.
  function formatted(value) {
    const root = document.createElement('span');
    root.className = 'formatted-text';
    function inline(text, target) {
      const pattern = /\*\*([^*\n]+)\*\*|__([^_\n]+)__|~~([^~\n]+)~~|\*([^*\n]+)\*/g;
      let start = 0;
      for (const match of text.matchAll(pattern)) {
        target.append(text.slice(start, match.index));
        const index = match.slice(1).findIndex(v => v !== undefined);
        const el = document.createElement(['strong', 'u', 's', 'em'][index]);
        el.textContent = match[index + 1];
        target.append(el);
        start = match.index + match[0].length;
      }
      target.append(text.slice(start));
    }
    let list = null;
    String(value).split('\n').forEach((line, index) => {
      const bullet = line.match(/^\s*(?:([-•])|\d+\.)\s+(.+)$/);
      if (bullet) {
        const tag = bullet[1] ? 'UL' : 'OL';
        if (!list || list.tagName !== tag) { list = document.createElement(tag); root.append(list); }
        const item = document.createElement('li'); inline(bullet[2], item); list.append(item);
      } else {
        if (index && !list) root.append('\n');
        list = null; inline(line, root);
      }
    });
    return root;
  }

  function editor(textarea) {
    if (textarea.dataset.formatReady || textarea.id === 'csv-texto' || textarea.dataset.noFormatting !== undefined) return;
    textarea.dataset.formatReady = '1';
    const bar = document.createElement('div'); bar.className = 'format-toolbar';
    bar.setAttribute('role', 'toolbar'); bar.setAttribute('aria-label', 'Formatação de texto');
    const preview = document.createElement('div'); preview.className = 'format-preview'; preview.hidden = true;
    const actions = [['Negrito', 'B', '**'], ['Itálico', '𝘐', '*'], ['Sublinhado', 'U̲', '__'], ['Tachado', 'S̶', '~~'], ['Lista com marcadores', '• ≡', '- '], ['Lista numerada', '1. ≡', '1. ']];
    actions.forEach(([label, icon, mark]) => {
      const button = document.createElement('button'); button.type = 'button'; button.title = label; button.setAttribute('aria-label', label); button.textContent = icon;
      button.addEventListener('click', () => {
        preview.hidden = true; textarea.hidden = false; toggle.textContent = 'Prévia';
        const start = textarea.selectionStart, end = textarea.selectionEnd;
        const selection = textarea.value.slice(start, end) || 'texto';
        const isList = mark.endsWith(' ');
        const from = isList ? textarea.value.lastIndexOf('\n', start - 1) + 1 : start;
        const selected = isList ? textarea.value.slice(from, end) || 'item' : selection;
        const replacement = isList ? selected.split('\n').map((line, i) => (mark === '1. ' ? `${i + 1}. ` : mark) + line.replace(/^\s*(?:[-•]|\d+\.)\s+/, '')).join('\n') : (selected.startsWith(mark) && selected.endsWith(mark) && selected.length > mark.length * 2 ? selected.slice(mark.length, -mark.length) : mark + selected + mark);
        textarea.setRangeText(replacement, from, end, 'select'); textarea.focus();
        textarea.dispatchEvent(new Event('input', { bubbles: true }));
      });
      bar.append(button);
    });
    const toggle = document.createElement('button'); toggle.type = 'button'; toggle.textContent = 'Prévia'; toggle.title = 'Alternar prévia da formatação';
    toggle.addEventListener('click', () => { preview.replaceChildren(formatted(textarea.value)); preview.hidden = !preview.hidden; textarea.hidden = !preview.hidden; toggle.textContent = preview.hidden ? 'Prévia' : 'Editar'; });
    bar.append(toggle); textarea.before(bar); textarea.after(preview);
    const editorModal = textarea.closest('[id^="modal-"]');
    if (editorModal) new MutationObserver(() => { textarea.hidden = false; preview.hidden = true; toggle.textContent = 'Prévia'; }).observe(editorModal, { attributes: true, attributeFilter: ['class'] });
    textarea.addEventListener('invalid', () => { textarea.hidden = false; preview.hidden = true; }, true);
    textarea.closest('form')?.addEventListener('reset', () => { textarea.hidden = false; preview.hidden = true; });
  }

  function imageUpload(input) {
    if (input.dataset.uploadReady) return;
    input.dataset.uploadReady = '1';
    const wrap = document.createElement('div'); wrap.className = 'course-image-upload';
    const label = document.createElement('label'); label.textContent = 'Ou envie uma imagem (JPG, PNG ou WebP, até 5 MB)';
    const file = document.createElement('input'); file.type = 'file'; file.accept = 'image/jpeg,image/png,image/webp'; label.append(file);
    const status = document.createElement('span'); status.setAttribute('role', 'status');
    const img = document.createElement('img'); img.alt = 'Prévia da capa do curso'; img.hidden = true;
    function refresh() { const value = input.value.trim(); img.hidden = !value; if (value) img.src = value; else img.removeAttribute('src'); }
    img.onerror = () => { img.hidden = true; };
    function validateUrl() {
      const value = input.value.trim();
      input.setCustomValidity(!value || /^(https?:\/\/\S+|\/(?!\/)\S+)$/i.test(value) ? '' : 'Informe uma URL http(s) válida ou envie uma imagem.');
    }
    input.addEventListener('input', () => { refresh(); validateUrl(); });
    let busy = false, requestVersion = 0;
    input.closest('form')?.addEventListener('submit', event => { validateUrl(); if (busy || !input.checkValidity()) { event.preventDefault(); event.stopImmediatePropagation(); if (!busy) input.reportValidity(); } }, true);
    file.addEventListener('change', async () => {
      const chosen = file.files[0]; if (!chosen) return;
      if (chosen.size > 5 * 1024 * 1024 || !['image/jpeg', 'image/png', 'image/webp'].includes(chosen.type)) { status.textContent = 'Selecione JPG, PNG ou WebP de até 5 MB.'; file.value = ''; return; }
      busy = true; file.disabled = true; input.readOnly = true; status.textContent = 'Enviando imagem…';
      const version = ++requestVersion;
      try {
        const body = new FormData(); body.append('arquivo', chosen);
        const response = await fetch(BASE + '/api/cursos/imagem', { method: 'POST', body, credentials: 'include' });
        const data = await response.json().catch(() => { throw new Error('Não foi possível enviar a imagem. Tente novamente.'); });
        if (!response.ok) throw new Error(data.error || 'Falha no envio da imagem.');
        if (version !== requestVersion) return;
        input.value = data.url; input.dispatchEvent(new Event('input', { bubbles: true }));
        status.textContent = 'Imagem enviada. Salve o curso para aplicar a capa.';
      } catch (error) { status.textContent = error.message; }
      finally { busy = false; file.disabled = false; input.readOnly = false; file.value = ''; }
    });
    wrap.append(label, status, img); input.after(wrap);
    const modal = input.closest('[id^="modal-"]');
    if (modal) new MutationObserver(() => { if (modal.classList.contains('hidden')) { requestVersion++; } else { refresh(); validateUrl(); if (!busy) status.textContent = ''; } }).observe(modal, { attributes: true, attributeFilter: ['class'] });
    refresh();
  }

  function enhance(root) {
    root.querySelectorAll('[id^="modal-"].fixed').forEach(modal => {
      if (modal.dataset.dialogReady) return;
      modal.dataset.dialogReady = '1'; modal.setAttribute('role', 'dialog'); modal.setAttribute('aria-modal', 'true');
      const panel = modal.firstElementChild; if (!panel) return;
      const heading = panel.querySelector('h2, h3'); if (heading?.id) modal.setAttribute('aria-labelledby', heading.id);
      const existing = [...panel.querySelectorAll('button')].find(b => ['✕', '×'].includes(b.textContent.trim()) || /fechar/i.test(b.getAttribute('aria-label') || '') || (!b.textContent.trim() && b.querySelector('svg') && /fechar|classList\.add\('hidden'\)/i.test(b.getAttribute('onclick') || '')));
      const close = existing || document.createElement('button'); close.type = 'button'; close.classList.add('dialog-close'); close.setAttribute('aria-label', 'Fechar janela'); close.title = 'Fechar';
      if (!existing) { close.textContent = '×'; close.addEventListener('click', () => {
        const cancel = [...panel.querySelectorAll('button')].find(b => b !== close && /^(Cancelar|Fechar)$/.test(b.textContent.trim()));
        if (cancel) cancel.click(); else modal.classList.add('hidden');
      }); }
      panel.append(close);
    });
    root.querySelectorAll('textarea').forEach(editor);
    root.querySelectorAll('[data-course-image]').forEach(imageUpload);
    // Render the same safe formatting in saved text, including dynamically loaded records.
    const walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT);
    const nodes = [];
    while (walker.nextNode()) {
      const node = walker.currentNode;
      if (node.parentElement.closest('script,style,textarea,input,code,pre,.format-toolbar,.format-preview,.formatted-text,[contenteditable]')) continue;
      if (/\*\*[^*\n]+\*\*|__[^_\n]+__|~~[^~\n]+~~|\*[^*\n]+\*|^\s*(?:[-•]|\d+\.)\s+\S/m.test(node.nodeValue)) nodes.push(node);
    }
    nodes.forEach(node => node.replaceWith(formatted(node.nodeValue)));
  }
  function start() {
    enhance(document.body);
    const observer = new MutationObserver(records => {
      observer.disconnect();
      const roots = new Set();
      records.forEach(record => { if (record.target.isConnected) roots.add(record.target.nodeType === 1 ? record.target : record.target.parentElement); });
      roots.forEach(enhance); observe();
    });
    function observe() { observer.observe(document.body, { childList: true, subtree: true, characterData: true }); }
    observe();
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start); else start();
})();
