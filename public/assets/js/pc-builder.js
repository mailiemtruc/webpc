(() => {
  const dataNode = document.getElementById('pcb-data');
  if (!dataNode) return;
  const catalog = JSON.parse(dataNode.textContent || '{}');
  const selected = {};
  const slots = Object.keys(catalog);
  const labels = { cpu:'CPU', motherboard:'Mainboard', ram:'RAM', gpu:'VGA', storage:'SSD / HDD', psu:'PSU', case:'Case', cooler:'Tản nhiệt' };
  const dialog = document.getElementById('pcb-picker');
  const list = document.getElementById('pcb-picker-list');
  const feedback = document.getElementById('pcb-feedback');
  let activeType = '';
  const money = value => new Intl.NumberFormat('vi-VN').format(value) + ' ₫';
  const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
  const specValue = (product, keys) => {
    const specs = product?.specs || {};
    for (const key of keys) if (specs[key] !== undefined && String(specs[key]).trim() !== '') return String(specs[key]).trim();
    return '';
  };
  function render() {
    let total = 0, count = 0;
    document.querySelectorAll('[data-slot]').forEach(slot => {
      const type = slot.dataset.slot, item = selected[type];
      slot.querySelector('[data-selected-label]').textContent = item ? item.name : 'Chưa chọn linh kiện';
      slot.querySelector('[data-selected-price]').textContent = item ? money(item.price) : '—';
      slot.classList.toggle('is-selected', !!item);
      if (item) { total += item.price; count++; }
    });
    document.getElementById('pcb-count').textContent = `${count}/8`;
    document.getElementById('pcb-total').textContent = money(total);
    checkCompatibility();
  }
  function checkCompatibility() {
    const notes = [];
    const cpuSocket = specValue(selected.cpu, ['socket','Socket','socket_type']);
    const boardSocket = specValue(selected.motherboard, ['socket','Socket','cpu_socket','socket_type']);
    if (selected.cpu && selected.motherboard) {
      if (cpuSocket && boardSocket) notes.push({ok:cpuSocket.toLowerCase()===boardSocket.toLowerCase(), text:cpuSocket.toLowerCase()===boardSocket.toLowerCase() ? `Socket CPU/Mainboard trùng khớp (${cpuSocket}).` : `Socket chưa khớp: CPU ${cpuSocket}, Mainboard ${boardSocket}.`});
      else notes.push({unknown:true,text:'Chưa đủ thông tin socket để xác nhận CPU và Mainboard.'});
    }
    const ramType = specValue(selected.ram, ['memory_type','ram_type','type','loai_ram']);
    const boardRam = specValue(selected.motherboard, ['memory_type','ram_type','supported_memory','loai_ram']);
    if (selected.ram && selected.motherboard) {
      if (ramType && boardRam) notes.push({ok:boardRam.toLowerCase().includes(ramType.toLowerCase()) || ramType.toLowerCase().includes(boardRam.toLowerCase()),text:`Chuẩn RAM: ${ramType}; Mainboard: ${boardRam}.`});
      else notes.push({unknown:true,text:'Chưa đủ thông tin chuẩn RAM của Mainboard hoặc RAM.'});
    }
    const boardSize = specValue(selected.motherboard, ['form_factor','formfactor','size','kich_thuoc']);
    const caseSize = specValue(selected.case, ['supported_form_factor','form_factor','motherboard_support','size']);
    if (selected.case && selected.motherboard) {
      if (boardSize && caseSize) notes.push({ok:caseSize.toLowerCase().includes(boardSize.toLowerCase()),text:`Kích thước Mainboard: ${boardSize}; Case hỗ trợ: ${caseSize}.`});
      else notes.push({unknown:true,text:'Chưa đủ thông tin kích thước để xác nhận Mainboard có vừa Case.'});
    }
    const panel = document.getElementById('pcb-compatibility');
    if (!notes.length) { panel.textContent = 'Chọn CPU, Mainboard, RAM và Case để kiểm tra các thông số tương thích có sẵn.'; panel.className = 'pcb-notice'; return; }
    panel.innerHTML = notes.map(n => `<p class="${n.unknown?'is-unknown':n.ok?'is-ok':'is-warning'}"><i class="fa-solid ${n.unknown?'fa-circle-info':n.ok?'fa-circle-check':'fa-triangle-exclamation'}"></i> ${escapeHtml(n.text)}</p>`).join('');
    panel.className = 'pcb-notice has-results';
  }
  function openPicker(type) {
    activeType = type;
    document.getElementById('pcb-picker-title').textContent = `Chọn ${labels[type]}`;
    const items = catalog[type] || [];
    list.innerHTML = items.length ? items.map(p => `<article class="pcb-product-option"><img src="${escapeHtml(p.thumbnail)}" alt=""><div class="pcb-option-info"><strong>${escapeHtml(p.name)}</strong><span>${escapeHtml(p.sku)} · Tồn kho: ${p.stock}</span><b>${money(p.price)}</b></div><button class="button button-primary" type="button" data-select-product="${p.id}" ${p.stock < 1 ? 'disabled' : ''}>${p.stock < 1 ? 'Hết hàng' : 'Chọn'}</button></article>`).join('') : '<p class="pcb-empty">Chưa có sản phẩm trong danh mục này.</p>';
    dialog.showModal();
  }
  document.querySelectorAll('[data-choose]').forEach(button => button.addEventListener('click', () => openPicker(button.dataset.choose)));
  list.addEventListener('click', event => {
    const button = event.target.closest('[data-select-product]');
    if (!button) return;
    const item = (catalog[activeType] || []).find(p => p.id === Number(button.dataset.selectProduct));
    if (item) selected[activeType] = item;
    dialog.close(); render(); feedback.textContent = `Đã chọn ${labels[activeType]}.`;
  });
  document.getElementById('pcb-picker-close').addEventListener('click', () => dialog.close());
  document.getElementById('pcb-clear').addEventListener('click', () => { Object.keys(selected).forEach(key => delete selected[key]); feedback.textContent = 'Đã xóa cấu hình hiện tại.'; render(); });
  document.getElementById('pcb-print').addEventListener('click', () => {
    if (!Object.keys(selected).length) { feedback.textContent = 'Hãy chọn ít nhất một linh kiện để in báo giá.'; return; }
    window.print();
  });
  document.getElementById('pcb-add-cart').addEventListener('click', async () => {
    const ids = Object.values(selected).map(item => item.id);
    if (!ids.length) { feedback.textContent = 'Cấu hình đang trống. Hãy chọn linh kiện trước.'; return; }
    const button = document.getElementById('pcb-add-cart'); button.disabled = true; button.textContent = 'Đang kiểm tra…';
    try {
      const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
      const body = new URLSearchParams({ _csrf: token, product_ids: JSON.stringify(ids) });
      const base = document.body.dataset.appBase || '';
      const response = await fetch(`${base}/api/builder/add-to-cart`, { method:'POST', credentials:'same-origin', headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}, body });
      const result = await response.json();
      if (!response.ok || !result.success) throw new Error(result.message || 'Không thể thêm cấu hình vào giỏ hàng.');
      feedback.textContent = result.message;
      window.location.href = result.redirect;
    } catch (error) { feedback.textContent = error.message; }
    finally { button.disabled = false; button.textContent = 'Thêm cả bộ vào giỏ'; }
  });
  render();
})();
