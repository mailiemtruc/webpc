(() => {
  const dataNode = document.getElementById('pcb-data');
  if (!dataNode) return;
  const catalog = JSON.parse(dataNode.textContent || '{}');
  const selected = {};
  const totalSlots = document.querySelectorAll('[data-slot]').length || 9;
  const labels = {
    cpu: 'CPU',
    motherboard: 'Mainboard',
    ram: 'RAM',
    gpu: 'VGA',
    storage: 'SSD / HDD',
    psu: 'PSU',
    case: 'Case',
    cooler: 'Tản nhiệt',
    monitor: 'Màn hình',
  };
  const dialog = document.getElementById('pcb-picker');
  const list = document.getElementById('pcb-picker-list');
  const searchInput = document.getElementById('pcb-picker-search');
  const pickerCount = document.getElementById('pcb-picker-count');
  const feedback = document.getElementById('pcb-feedback');
  const pageNode = document.querySelector('.pcb-page');
  const placeholderImg = pageNode?.dataset.placeholder || '';
  let activeType = '';

  const money = value => new Intl.NumberFormat('vi-VN').format(value) + ' ₫';
  const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));

  const specValue = (product, keys) => {
    const specs = product?.specs || {};
    for (const key of keys) {
      if (specs[key] !== undefined && String(specs[key]).trim() !== '') return String(specs[key]).trim();
    }
    return '';
  };

  const normSocket = s => String(s || '').toUpperCase().replace(/\s+/g, '');

  function estimateCpuWatts(cpu) {
    if (!cpu) return 0;
    const n = cpu.name.toUpperCase();
    if (/9950|13900|14900|285K|7950/.test(n)) return 210;
    if (/9900|265K|14700|13700|7900/.test(n)) return 165;
    if (/9850|9800|7800|14600|13600|12600/.test(n)) return 125;
    return 85;
  }

  function estimateGpuWatts(gpu) {
    if (!gpu) return 0;
    const n = gpu.name.toUpperCase();
    if (/5090/.test(n)) return 575;
    if (/5080/.test(n)) return 360;
    if (/5070\s*TI|9070\s*XT/.test(n)) return 300;
    if (/5070|4070/.test(n)) return 250;
    if (/5060\s*TI|B70/.test(n)) return 180;
    if (/5060|4060/.test(n)) return 145;
    if (/5050|3050/.test(n)) return 130;
    return 180;
  }

  function parsePsuWatts(psu) {
    if (!psu) return 0;
    const raw = specValue(psu, ['wattage', 'power']) || psu.name;
    const m = String(raw).match(/(\d{3,4})\s*W/i);
    return m ? parseInt(m[1], 10) : 0;
  }

  function formatSpecBadges(product) {
    const specs = product?.specs || {};
    const keys = ['socket', 'memory_type', 'form_factor', 'supported_form_factor', 'vram', 'capacity', 'speed', 'wattage', 'efficiency', 'type', 'size', 'refresh_rate'];
    const chips = [];
    for (const k of keys) {
      if (specs[k]) {
        const val = String(specs[k]).trim();
        if (val && !chips.includes(val)) chips.push(val);
      }
      if (chips.length >= 3) break;
    }
    return chips.map(c => `<em class="pcb-spec-chip">${escapeHtml(c)}</em>`).join('');
  }

  function render() {
    let total = 0, count = 0;
    document.querySelectorAll('[data-slot]').forEach(slot => {
      const type = slot.dataset.slot, item = selected[type];
      slot.querySelector('[data-selected-label]').textContent = item ? item.name : 'Chưa chọn linh kiện';
      slot.querySelector('[data-selected-price]').textContent = item ? money(item.price) : '—';
      const chooseBtn = slot.querySelector('[data-choose]');
      if (chooseBtn) chooseBtn.textContent = item ? 'Đổi' : 'Chọn';
      const removeBtn = slot.querySelector('[data-remove]');
      if (removeBtn) removeBtn.hidden = !item;
      slot.classList.toggle('is-selected', !!item);
      if (item) { total += item.price; count++; }
    });
    document.getElementById('pcb-count').textContent = `${count}/${totalSlots}`;
    document.getElementById('pcb-total').textContent = money(total);

    const estW = (selected.cpu || selected.gpu) ? (estimateCpuWatts(selected.cpu) + estimateGpuWatts(selected.gpu) + 120) : 0;
    const powerBox = document.getElementById('pcb-power-box');
    const powerEst = document.getElementById('pcb-power-est');
    if (powerBox && powerEst) {
      powerBox.hidden = estW === 0;
      powerEst.textContent = `~${estW}W (khuyến nghị ≥ ${Math.ceil((estW * 1.25) / 50) * 50}W)`;
    }

    checkCompatibility(estW);
  }

  function checkCompatibility(estW) {
    const notes = [];

    // 1. CPU Socket <-> Mainboard Socket
    const cpuSocket = specValue(selected.cpu, ['socket', 'Socket', 'socket_type']);
    const boardSocket = specValue(selected.motherboard, ['socket', 'Socket', 'cpu_socket', 'socket_type']);
    if (selected.cpu && selected.motherboard) {
      if (cpuSocket && boardSocket) {
        const match = normSocket(cpuSocket) === normSocket(boardSocket);
        notes.push({
          ok: match,
          text: match
            ? `Socket CPU & Mainboard tương thích hoàn hảo (${cpuSocket}).`
            : `Lệch Socket: CPU dùng ${cpuSocket}, Mainboard dùng ${boardSocket}. Hãy đổi một trong hai linh kiện!`,
        });
      } else {
        notes.push({ unknown: true, text: 'Chưa đủ thông tin socket để xác nhận CPU và Mainboard.' });
      }
    }

    // 2. RAM Type <-> Mainboard Memory
    const ramType = specValue(selected.ram, ['memory_type', 'ram_type', 'type', 'loai_ram']);
    const boardRam = specValue(selected.motherboard, ['memory_type', 'ram_type', 'supported_memory', 'loai_ram', 'ram']);
    if (selected.ram && selected.motherboard) {
      if (ramType && boardRam) {
        const match = boardRam.toLowerCase().includes(ramType.toLowerCase()) || ramType.toLowerCase().includes(boardRam.toLowerCase());
        notes.push({
          ok: match,
          text: match
            ? `Chuẩn bộ nhớ RAM khớp nhau (${ramType}).`
            : `Không khớp chuẩn RAM: RAM là ${ramType}, nhưng Mainboard chỉ hỗ trợ ${boardRam}!`,
        });
      } else {
        notes.push({ unknown: true, text: 'Chưa đủ thông tin chuẩn RAM của Mainboard hoặc RAM.' });
      }
    }

    // 3. Mainboard Form Factor <-> Case Support
    const boardSize = specValue(selected.motherboard, ['form_factor', 'formfactor', 'size', 'kich_thuoc']);
    const caseSize = specValue(selected.case, ['supported_form_factor', 'form_factor', 'motherboard_support', 'size']);
    if (selected.case && selected.motherboard) {
      if (boardSize && caseSize) {
        const tokens = caseSize.toLowerCase().split(/[,/|]+/).map(t => t.trim());
        const match = tokens.includes(boardSize.toLowerCase()) || caseSize.toLowerCase().includes(boardSize.toLowerCase());
        notes.push({
          ok: match,
          text: match
            ? `Vỏ Case lắp vừa Mainboard chuẩn ${boardSize} (hỗ trợ: ${caseSize}).`
            : `Mainboard chuẩn ${boardSize} có thể không vừa Case (hỗ trợ: ${caseSize}).`,
        });
      } else {
        notes.push({ unknown: true, text: 'Chưa đủ thông tin kích thước để xác nhận Mainboard có vừa Case.' });
      }
    }

    // 4. Cooler Socket <-> CPU Socket
    const coolerSocket = specValue(selected.cooler, ['socket', 'Socket']);
    if (selected.cooler && selected.cpu && cpuSocket && coolerSocket) {
      const cpuKey = normSocket(cpuSocket).replace('LGA', '');
      const match = normSocket(coolerSocket).includes(cpuKey);
      notes.push({
        ok: match,
        text: match
          ? `Tản nhiệt hỗ trợ đúng socket CPU ${cpuSocket}.`
          : `Tản nhiệt (${coolerSocket}) có thể cần ngàm chuyển cho socket ${cpuSocket}.`,
      });
    }

    // 5. PSU Wattage <-> Estimated CPU + GPU + System Power
    const psuW = parsePsuWatts(selected.psu);
    if (selected.psu && estW > 0) {
      const recommended = Math.ceil((estW * 1.25) / 50) * 50;
      if (psuW > 0) {
        const match = psuW >= recommended;
        notes.push({
          ok: match,
          text: match
            ? `Nguồn ${psuW}W đủ cấp điện an toàn cho cấu hình (ước tính ~${estW}W, khuyến nghị ≥ ${recommended}W).`
            : `Cảnh báo thiếu nguồn: PSU ${psuW}W thấp hơn mức khuyến nghị ≥ ${recommended}W (ước tính cấu hình ~${estW}W).`,
        });
      }
    }

    const panel = document.getElementById('pcb-compatibility');
    if (!notes.length) {
      panel.textContent = 'Chọn CPU, Mainboard, RAM, VGA, PSU và Case để hệ thống kiểm tra tự động độ tương thích & công suất nguồn.';
      panel.className = 'pcb-notice';
      return;
    }
    panel.innerHTML = notes.map(n => `<p class="${n.unknown ? 'is-unknown' : n.ok ? 'is-ok' : 'is-warning'}"><i class="fa-solid ${n.unknown ? 'fa-circle-info' : n.ok ? 'fa-circle-check' : 'fa-triangle-exclamation'}"></i> ${escapeHtml(n.text)}</p>`).join('');
    panel.className = 'pcb-notice has-results';
  }

  function renderPickerList(query = '') {
    const items = catalog[activeType] || [];
    const q = query.trim().toLowerCase();
    const filtered = q
      ? items.filter(p => {
          const hay = `${p.name} ${p.sku} ${Object.values(p.specs || {}).join(' ')}`.toLowerCase();
          return hay.includes(q);
        })
      : items;

    if (pickerCount) {
      pickerCount.textContent = `${filtered.length}/${items.length} sản phẩm`;
    }

    const onErrAttr = placeholderImg ? ` onerror="this.onerror=null;this.src='${escapeHtml(placeholderImg)}';"` : '';
    list.innerHTML = filtered.length
      ? filtered.map(p => `
        <article class="pcb-product-option">
          <img src="${escapeHtml(p.thumbnail)}" alt="${escapeHtml(p.name)}"${onErrAttr}>
          <div class="pcb-option-info">
            <strong>${escapeHtml(p.name)}</strong>
            <div class="pcb-spec-row">${formatSpecBadges(p)}</div>
            <span>${escapeHtml(p.sku)} · Tồn kho: ${p.stock}</span>
            <b>${money(p.price)}</b>
          </div>
          <button class="button button-primary" type="button" data-select-product="${p.id}" ${p.stock < 1 ? 'disabled' : ''}>${p.stock < 1 ? 'Hết hàng' : 'Chọn'}</button>
        </article>`).join('')
      : '<p class="pcb-empty">Không tìm thấy linh kiện phù hợp từ khóa.</p>';
  }

  function openPicker(type) {
    activeType = type;
    document.getElementById('pcb-picker-title').textContent = `Chọn ${labels[type] || type}`;
    if (searchInput) searchInput.value = '';
    renderPickerList('');
    dialog.showModal();
    if (searchInput) searchInput.focus();
  }

  if (searchInput) {
    searchInput.addEventListener('input', () => renderPickerList(searchInput.value));
  }

  document.querySelectorAll('[data-choose]').forEach(button => button.addEventListener('click', () => openPicker(button.dataset.choose)));
  document.querySelectorAll('[data-remove]').forEach(button => button.addEventListener('click', () => {
    const type = button.dataset.remove;
    if (selected[type]) {
      delete selected[type];
      feedback.textContent = `Đã bỏ chọn ${labels[type]}.`;
      render();
    }
  }));
  list.addEventListener('click', event => {
    const button = event.target.closest('[data-select-product]');
    if (!button) return;
    const item = (catalog[activeType] || []).find(p => p.id === Number(button.dataset.selectProduct));
    if (item) selected[activeType] = item;
    dialog.close();
    render();
    feedback.textContent = `Đã chọn ${labels[activeType]}.`;
  });
  document.getElementById('pcb-picker-close').addEventListener('click', () => dialog.close());
  document.getElementById('pcb-clear').addEventListener('click', () => {
    Object.keys(selected).forEach(key => delete selected[key]);
    feedback.textContent = 'Đã xóa cấu hình hiện tại.';
    render();
  });
  document.getElementById('pcb-print').addEventListener('click', () => {
    if (!Object.keys(selected).length) {
      feedback.textContent = 'Hãy chọn ít nhất một linh kiện để in báo giá.';
      return;
    }
    window.print();
  });
  document.getElementById('pcb-add-cart').addEventListener('click', async () => {
    const ids = Object.values(selected).map(item => item.id);
    if (!ids.length) {
      feedback.textContent = 'Cấu hình đang trống. Hãy chọn linh kiện trước.';
      return;
    }
    const button = document.getElementById('pcb-add-cart');
    button.disabled = true;
    button.textContent = 'Đang kiểm tra…';
    try {
      const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
      const body = new URLSearchParams({ _csrf: token, product_ids: JSON.stringify(ids) });
      const base = document.body.dataset.appBase || '';
      const response = await fetch(`${base}/api/builder/add-to-cart`, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token },
        body,
      });
      const result = await response.json();
      if (!response.ok || !result.success) throw new Error(result.message || 'Không thể thêm cấu hình vào giỏ hàng.');
      const bagCount = document.querySelector('.bag-count');
      if (bagCount && typeof result.cartCount === 'number') bagCount.textContent = String(result.cartCount);
      feedback.textContent = result.message;
      window.location.href = result.redirect;
    } catch (error) {
      feedback.textContent = error.message;
    } finally {
      button.disabled = false;
      button.textContent = 'Thêm cả bộ vào giỏ';
    }
  });
  render();
})();
