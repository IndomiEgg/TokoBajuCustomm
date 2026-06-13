<?= $this->extend('layout/template') ?>

<?= $this->section('styles') ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin-dashboard.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="admin-dashboard-container">
    <div class="admin-dashboard-header">
        <h1>Admin Dashboard</h1>
        <p>Selamat datang, <?= esc($admin_name) ?></p>
    </div>

    <div class="admin-dashboard-grid">
        <aside class="admin-sidebar">
            <nav>
                <ul>
                    <li><button class="panel-btn" data-panel="overview"><i class="fa-solid fa-house"></i>Ringkasan</button></li>
                    <li><button class="panel-btn" data-panel="status"><i class="fa-solid fa-users"></i>Status Pelanggan</button></li>
                    <li><button class="panel-btn" data-panel="reports"><i class="fa-solid fa-file-csv"></i>Laporan</button></li>
                    <li><button class="panel-btn" data-panel="analytics"><i class="fa-solid fa-chart-pie"></i>Analitik</button></li>
                </ul>
            </nav>
        </aside>

        <main class="admin-main">
            <div id="panel-root" class="admin-panel-root">
                <div style="color:#888; padding:18px;">Pilih menu di kiri untuk menampilkan panel.</div>
            </div>
        </main>
    </div>

    <div class="admin-action-row">
        <a href="<?= base_url('admin/orders') ?>" class="btn-luxury btn-luxury-outline">Kelola Pesanan</a>
        <a href="<?= base_url('admin/logout') ?>" class="btn-luxury btn-luxury-outline">Logout Admin</a>
    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function(){
        const root = document.getElementById('panel-root');
        const buttons = document.querySelectorAll('.panel-btn');
        const currentPath = window.location.pathname.replace(/\/+$/, '');
        const adminBasePath = currentPath.replace(/\/admin\/dashboard(?:\/.*)?$/, '/admin');

        function buildAdminUrl(path) {
            const sanitizedPath = path.startsWith('/') ? path : '/' + path;
            return adminBasePath + sanitizedPath;
        }

        function getAdminBaseUrl() {
            return adminBasePath;
        }

        function initializeAnalytics() {
            const chartRoot = root;
            const revenueCanvas = chartRoot.querySelector('#chart-revenue');
            const ordersCanvas = chartRoot.querySelector('#chart-orders');
            const weeklyCanvas = chartRoot.querySelector('#chart-weekly');
            const toggleBtn = chartRoot.querySelector('#toggle-dummy-data');
            if (!revenueCanvas || !ordersCanvas || !weeklyCanvas) return;

            let useDummy = false;

            function updateToggleText() {
                if (!toggleBtn) return;
                toggleBtn.dataset.state = useDummy ? '1' : '0';
                toggleBtn.textContent = 'Force Dummy Data: ' + (useDummy ? 'On' : 'Off');
            }

            async function loadAndRender() {
                const url = buildAdminUrl('/dashboard/analytics-data') + (useDummy ? '?dummy=1' : '');
                try {
                    const res = await fetch(url, { credentials: 'same-origin' });
                    const json = await res.json();
                    if (!json.success) throw new Error('Invalid analytics response');

                    const daily = json.daily || json.data || [];
                    const weekly = json.weekly || [];
                    const summary = json.summary || { total_today: 0, finished_today: 0, revenue_today: 0 };

                    // populate summary cards
                    const fmt = n => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(n);
                    const totalTodayEl = chartRoot.querySelector('#summary-total-today');
                    const finishedTodayEl = chartRoot.querySelector('#summary-finished-today');
                    const revenueTodayEl = chartRoot.querySelector('#summary-revenue-today');
                    if (totalTodayEl) totalTodayEl.textContent = summary.total_today ?? 0;
                    if (finishedTodayEl) finishedTodayEl.textContent = summary.finished_today ?? 0;
                    if (revenueTodayEl) revenueTodayEl.textContent = fmt(Number(summary.revenue_today || 0));

                    const labels = daily.map(r => r.d);
                    const revenue = daily.map(r => Number(r.revenue_confirmed || r.revenue || 0));
                    const orders = daily.map(r => Number(r.orders || 0));

                    const ctxR = revenueCanvas.getContext('2d');
                    const ctxO = ordersCanvas.getContext('2d');
                    const ctxW = weeklyCanvas.getContext('2d');

                    // destroy existing charts if present (lightweight)
                    if (chartRoot._charts) {
                        chartRoot._charts.forEach(c => c.destroy && c.destroy());
                    }
                    chartRoot._charts = [];

                    chartRoot._charts.push(new Chart(ctxR, {
                        type: 'bar',
                        data: { labels, datasets: [{ label: 'Revenue', data: revenue, backgroundColor: '#60a5fa', borderRadius: 6 }] },
                        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
                    }));

                    chartRoot._charts.push(new Chart(ctxO, {
                        type: 'line',
                        data: { labels, datasets: [{ label: 'Orders', data: orders, borderColor: '#34d399', backgroundColor: 'rgba(52,211,153,0.18)', fill: true, tension: 0.3 }] },
                        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
                    }));

                    // weekly chart
                    const weekLabels = weekly.map(w => (w.yr && w.wk) ? (w.yr + '-W' + w.wk) : ('W' + (w.wk || '')));
                    const weekOrders = weekly.map(w => Number(w.orders || 0));
                    const weekRevenue = weekly.map(w => Number(w.revenue_confirmed || 0));

                    chartRoot._charts.push(new Chart(ctxW, {
                        type: 'bar',
                        data: { labels: weekLabels, datasets: [{ label: 'Orders', data: weekOrders, backgroundColor: '#f59e0b' }, { label: 'Revenue', data: weekRevenue, backgroundColor: '#60a5fa' }] },
                        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
                    }));

                } catch (err) {
                    console.error('Analytics load failed', err);
                    const message = document.createElement('div');
                    message.style.color = '#c00';
                    message.style.padding = '18px';
                    message.textContent = 'Gagal memuat data analitik.';
                    chartRoot.appendChild(message);
                }
            }

            if (toggleBtn) {
                toggleBtn.addEventListener('click', function () {
                    useDummy = !useDummy;
                    updateToggleText();
                    loadAndRender();
                });
                updateToggleText();
            }

            // initial load
            loadAndRender();
        }

        function getCsrfToken() {
            const nameMeta = document.querySelector('meta[name="csrf-token-name"]');
            const valueMeta = document.querySelector('meta[name="csrf-token-value"]');
            if (!nameMeta || !valueMeta) {
                return null;
            }
            return { name: nameMeta.content, value: valueMeta.content };
        }

        function showStatusMessage(text, success = true) {
            const panel = root.querySelector('.panel-status');
            if (!panel) return;
            let box = panel.querySelector('.status-message');
            if (!box) return;
            box.textContent = text;
            box.className = 'status-message ' + (success ? 'status-message-success' : 'status-message-error');
            setTimeout(() => {
                if (box) box.textContent = '';
            }, 3500);
        }

        function initializeReportPanel() {
            const panel = root.querySelector('.panel-reports');
            if (!panel) return;

            const rangeEl = panel.querySelector('#range');
            const btnRefresh = panel.querySelector('#btn-refresh');
            const downloadRange = panel.querySelector('#download-range');
            const tableBody = panel.querySelector('#report-table tbody');
            if (!rangeEl || !btnRefresh || !downloadRange || !tableBody) {
                return;
            }

            function fmtCurrency(n) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(n);
            }

            function renderRows(rows) {
                tableBody.innerHTML = '';
                if (!rows || !rows.length) {
                    tableBody.innerHTML = '<tr><td colspan="6" style="color:#888; padding:18px;">Tidak ada data untuk rentang ini.</td></tr>';
                    return;
                }
                for (const r of rows) {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `<td>${r.date}</td><td>${r.order_code}</td><td>${r.product}</td><td style="text-align:center">${r.qty}</td><td>${fmtCurrency(Number(r.unit_price))}</td><td>${fmtCurrency(Number(r.total))}</td>`;
                    tableBody.appendChild(tr);
                }
            }

            async function loadReport() {
                const range = rangeEl.value || 'daily';
                downloadRange.value = range;
                const url = buildAdminUrl('/dashboard/report-data') + '?range=' + encodeURIComponent(range);
                try {
                    const res = await fetch(url, { credentials: 'same-origin' });
                    const json = await res.json();
                    if (!json.success) throw new Error('Gagal memuat data');
                    renderRows(json.data || []);
                } catch (e) {
                    tableBody.innerHTML = '<tr><td colspan="6" style="color:#c00; padding:18px;">' + e.message + '</td></tr>';
                }
            }

            btnRefresh.addEventListener('click', loadReport);
            loadReport();
        }

        function applyFilters(panel, filter) {
            const rows = panel.querySelectorAll('.order-row');
            rows.forEach(row => {
                const orderStatus = row.dataset.orderStatus || '';
                const paymentStatus = row.dataset.paymentStatus || '';
                let visible = false;

                if (filter === 'all') {
                    visible = true;
                } else if (filter.startsWith('status-')) {
                    visible = orderStatus === filter.replace('status-', '');
                } else if (filter.startsWith('payment-')) {
                    visible = paymentStatus === filter.replace('payment-', '');
                }

                row.style.display = visible ? '' : 'none';
            });
        }

        function refreshStatusSummary(panel) {
            const rows = panel.querySelectorAll('.order-row');
            const counts = {
                paid: 0,
                unpaid: 0,
                pending: 0,
                in_progress: 0,
                processing: 0,
                delivering: 0,
                finished: 0,
                cancelled: 0,
            };

            rows.forEach(row => {
                const payment = row.dataset.paymentStatus || 'unpaid';
                const status = row.dataset.orderStatus || 'pending';
                if (counts[payment] !== undefined) counts[payment] += 1;
                if (counts[status] !== undefined) counts[status] += 1;
            });

            const statusCount1 = panel.querySelector('.status-count-card:nth-child(1) strong');
            const statusCount2 = panel.querySelector('.status-count-card:nth-child(2) strong');
            const statusCount3 = panel.querySelector('.status-count-card:nth-child(3) strong');
            const statusCount4 = panel.querySelector('.status-count-card:nth-child(4) strong');
            const statusCount5 = panel.querySelector('.status-count-card:nth-child(5) strong');
            const statusCount6 = panel.querySelector('.status-count-card:nth-child(6) strong');
            const statusCount7 = panel.querySelector('.status-count-card:nth-child(7) strong');
            const statusCount8 = panel.querySelector('.status-count-card:nth-child(8) strong');

            if (statusCount1) statusCount1.textContent = counts.paid;
            if (statusCount2) statusCount2.textContent = counts.unpaid;
            if (statusCount3) statusCount3.textContent = counts.pending;
            if (statusCount4) statusCount4.textContent = counts.in_progress;
            if (statusCount5) statusCount5.textContent = counts.processing;
            if (statusCount6) statusCount6.textContent = counts.delivering;
            if (statusCount7) statusCount7.textContent = counts.finished;
            if (statusCount8) statusCount8.textContent = counts.cancelled;
        }

        function enhanceStatusPanel() {
            const panel = root.querySelector('.panel-status');
            if (!panel) return;

            const filterButtons = panel.querySelectorAll('.status-filter-btn');
            filterButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    filterButtons.forEach(x => x.classList.remove('active'));
                    btn.classList.add('active');
                    applyFilters(panel, btn.dataset.filter);
                });
            });

            const modal = panel.querySelector('#action-confirm-modal');
            const modalText = panel.querySelector('#confirm-modal-text');
            const cancelBtn = panel.querySelector('#confirm-modal-cancel');
            const acceptBtn = panel.querySelector('#confirm-modal-accept');
            let currentPaymentTarget = null;

            const showModal = (orderId, orderCode) => {
                if (!modal) return;
                currentPaymentTarget = orderId;
                if (modalText) {
                    modalText.textContent = `Ubah status pembayaran order ${orderCode} menjadi Paid?`;
                }
                modal.classList.remove('hidden');
            };
            const hideModal = () => {
                if (!modal) return;
                modal.classList.add('hidden');
                currentPaymentTarget = null;
            };

            if (cancelBtn) {
                cancelBtn.addEventListener('click', hideModal);
            }
            if (modal) {
                const backdrop = modal.querySelector('.confirm-modal-backdrop');
                if (backdrop) {
                    backdrop.addEventListener('click', hideModal);
                }
            }
            if (acceptBtn) {
                acceptBtn.addEventListener('click', () => {
                    if (!currentPaymentTarget) return;
                    const url = buildAdminUrl('/orders/update-status/' + currentPaymentTarget);
                    const data = new FormData();
                    data.append('payment_status', 'paid');

                    const csrf = getCsrfToken();
                    if (csrf) {
                        data.append(csrf.name, csrf.value);
                    }

                    fetch(url, {
                        method: 'POST',
                        body: data,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                        .then(r => r.json())
                        .then(json => {
                            if (!json.success) throw new Error(json.message || 'Update failed');
                            const row = panel.querySelector('.order-row[data-order-id="' + currentPaymentTarget + '"]');
                            if (row) {
                                row.dataset.paymentStatus = json.order.payment_status;
                                const badge = row.querySelector('.payment-badge');
                                if (badge) {
                                    badge.textContent = 'Paid';
                                    badge.className = 'payment-badge payment-paid';
                                }
                                const actionCell = row.querySelector('td:last-child');
                                if (actionCell) {
                                    const paidBtn = actionCell.querySelector('.btn-mark-paid');
                                    if (paidBtn) {
                                        paidBtn.remove();
                                    }
                                    const paidLabel = document.createElement('span');
                                    paidLabel.className = 'payment-locked';
                                    paidLabel.textContent = 'Paid';
                                    actionCell.insertBefore(paidLabel, actionCell.firstChild);
                                }
                            }
                            showStatusMessage(json.message || 'Status pembayaran berhasil diubah');
                        })
                        .catch(err => showStatusMessage(err.message || 'Update gagal', false))
                        .finally(hideModal);
                });
            }

            panel.querySelectorAll('.btn-mark-paid').forEach(btn => {
                btn.addEventListener('click', () => {
                    const orderId = btn.dataset.orderId;
                    const orderCode = btn.dataset.orderCode;
                    showModal(orderId, orderCode);
                });
            });

            const updateOrderRow = (row, order) => {
                if (!row || !order) return;
                row.dataset.orderStatus = order.order_status;
                row.dataset.paymentStatus = order.payment_status;

                const statusForm = row.querySelector('.order-status-form');
                if (statusForm) {
                    statusForm.dataset.currentStatus = order.order_status;
                    const statusSelect = statusForm.querySelector('select[name="status"]');
                    if (statusSelect) {
                        statusSelect.value = order.order_status;
                    }
                }

                const paymentBadge = row.querySelector('.payment-badge');
                if (paymentBadge) {
                    paymentBadge.textContent = order.payment_status ? order.payment_status.charAt(0).toUpperCase() + order.payment_status.slice(1) : '';
                    paymentBadge.className = 'payment-badge payment-' + (order.payment_status || 'unpaid');
                }
            };

            panel.querySelectorAll('.order-status-form').forEach(form => {
                form.addEventListener('submit', event => {
                    event.preventDefault();
                    const orderId = form.dataset.orderId;
                    const currentStatus = form.dataset.currentStatus || '';
                    const statusSelect = form.querySelector('select[name="status"]');
                    if (statusSelect && statusSelect.value === 'finished' && currentStatus !== 'finished') {
                        const confirmed = window.confirm('Pesanan akan dipindahkan ke status Finished dan disertakan dalam laporan. Lanjutkan?');
                        if (!confirmed) {
                            if (statusSelect) {
                                statusSelect.value = currentStatus;
                            }
                            showStatusMessage('Perubahan status dibatalkan', false);
                            return;
                        }
                    }

                    const data = new FormData(form);
                    const actionUrl = form.getAttribute('action') || '';
                    const url = actionUrl.startsWith(window.location.origin)
                        ? actionUrl
                        : buildAdminUrl('/orders/update-status/' + orderId);

                    const csrf = getCsrfToken();
                    if (csrf && !form.querySelector(`input[name="${csrf.name}"]`)) {
                        data.append(csrf.name, csrf.value);
                    }

                    fetch(url, {
                        method: 'POST',
                        body: data,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                        .then(r => r.json())
                        .then(json => {
                            if (!json.success) throw new Error(json.message || 'Update failed');
                            const row = panel.querySelector(`.order-row[data-order-id="${orderId}"]`);
                            if (row) {
                                updateOrderRow(row, json.order);
                            }
                            refreshStatusSummary(panel);
                            showStatusMessage(json.message || 'Status order berhasil diperbarui');
                        })
                        .catch(err => showStatusMessage(err.message || 'Update gagal', false));
                });
            });
        }

        function loadPanel(name){
            root.innerHTML = '<div style="padding:18px;color:#888;">Memuat '+name+'...</div>';
            fetch(buildAdminUrl('/dashboard/panel/' + name))
                .then(r => r.text())
                .then(html => {
                    root.innerHTML = html;
                    if (name === 'analytics') {
                        initializeAnalytics();
                    }
                    if (name === 'status') {
                        enhanceStatusPanel();
                    }
                    if (name === 'reports') {
                        initializeReportPanel();
                    }
                })
                .catch(err => {
                    root.innerHTML = '<div style="padding:18px;color:#c00;">Gagal memuat panel</div>';
                    console.error(err);
                });
        }

        buttons.forEach(b => b.addEventListener('click', ()=> {
            buttons.forEach(x => x.classList.remove('active'));
            b.classList.add('active');
            loadPanel(b.dataset.panel);
        }));

        if (buttons.length) { buttons[0].classList.add('active'); loadPanel(buttons[0].dataset.panel || 'overview'); } else { loadPanel('overview'); }
    })();
</script>
<?= $this->endSection() ?>
