<div class="admin-panel panel-analytics">
    <div class="admin-panel-card">
        <h2>Analitik Visual</h2>
        <p>Grafik ditampilkan saat tab Analitik dibuka (lazy-loaded).</p>
        <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; margin-top:12px; flex-wrap:wrap;">
            <div class="analytics-summary-grid" style="display:flex; gap:12px; flex-wrap:wrap;">
            <div class="status-count-card" style="flex:1; min-width:180px;">
                <div style="color:#94a3b8; font-size:0.9rem;">Total Orders (Hari ini)</div>
                <strong id="summary-total-today" style="font-size:1.6rem; color:#fff; display:block; margin-top:8px;">-</strong>
            </div>
            <div class="status-count-card" style="flex:1; min-width:180px;">
                <div style="color:#94a3b8; font-size:0.9rem;">Selesai (Hari ini)</div>
                <strong id="summary-finished-today" style="font-size:1.6rem; color:#a7f3d0; display:block; margin-top:8px;">-</strong>
            </div>
            <div class="status-count-card" style="flex:1; min-width:220px;">
                <div style="color:#94a3b8; font-size:0.9rem;">Pendapatan (Hari ini)</div>
                <strong id="summary-revenue-today" style="font-size:1.6rem; color:#60a5fa; display:block; margin-top:8px;">-</strong>
            </div>
            <div style="display:flex; gap:8px; align-items:center;">
                <button id="toggle-dummy-data" class="btn-luxury btn-luxury-outline" data-state="0">Force Dummy Data: Off</button>
            </div>
        </div>
        <div class="admin-chart-grid">
            <div class="admin-chart-card">
                <h4>Revenue (30 hari)</h4>
                <canvas id="chart-revenue"></canvas>
            </div>
            <div class="admin-chart-card">
                <h4>Orders (30 hari)</h4>
                <canvas id="chart-orders"></canvas>
            </div>
            <div class="admin-chart-card">
                <h4>Orders Mingguan (8 minggu)</h4>
                <canvas id="chart-weekly"></canvas>
            </div>
        </div>
    </div>
</div>