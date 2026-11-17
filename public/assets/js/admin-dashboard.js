(() => {
  const canvas = document.getElementById('paid-revenue-chart');
  if (!canvas || typeof Chart === 'undefined') return;
  const base = document.body.dataset.appBase || '';
  fetch(`${base}/api/admin/chart-data`, { headers: { Accept: 'application/json' }, credentials: 'same-origin' })
    .then(response => { if (!response.ok) throw new Error('Không tải được dữ liệu biểu đồ'); return response.json(); })
    .then(payload => {
      if (!payload.datasets?.length) return;
      new Chart(canvas, {
        type: 'line',
        data: payload,
        options: {
          responsive: true,
          maintainAspectRatio: false,
          interaction: { intersect: false, mode: 'index' },
          plugins: { legend: { display: true }, tooltip: { callbacks: { label: item => `${item.dataset.label}: ${new Intl.NumberFormat('vi-VN').format(item.parsed.y)} ₫` } } },
          scales: { y: { beginAtZero: true, ticks: { callback: value => new Intl.NumberFormat('vi-VN', { notation: 'compact' }).format(value) } } }
        }
      });
    })
    .catch(() => { const note = document.createElement('p'); note.className = 'admin-empty'; note.textContent = 'Chưa thể tải dữ liệu biểu đồ. Vui lòng thử tải lại trang.'; canvas.replaceWith(note); });
})();
