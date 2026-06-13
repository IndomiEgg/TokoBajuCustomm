<div class="admin-panel panel-reports">
    <div class="admin-panel-card">
        <h2>Laporan</h2>
        <p>Generate laporan penjualan ketika order berstatus <strong>finished</strong>. Pilih rentang lalu klik <strong>Download</strong>.</p>
        <div class="report-controls">
            <label for="range">Pilih rentang</label>
            <select id="range" name="range">
                <option value="daily">Harian</option>
                <option value="weekly">Mingguan</option>
                <option value="monthly">Bulanan</option>
            </select>
            <button id="btn-refresh" type="button" class="btn-luxury btn-luxury-outline">Refresh</button>
            <form id="download-form" method="post" action="<?= base_url('admin/dashboard/reports/export') ?>" style="display:inline-block; margin-left:8px;">
                <?= csrf_field() ?>
                <input type="hidden" name="range" id="download-range" value="daily">
                <input type="hidden" name="detailed" value="1">
                <button type="submit" class="btn-luxury btn-luxury-primary">Download Excel (CSV)</button>
            </form>
        </div>

        <div class="report-note">
            <strong>Catatan:</strong> Hanya pesanan dengan status <strong>finished</strong> yang disertakan. Tabel di bawah menampilkan baris terperinci per item pesanan.
        </div>

        <div style="margin-top:14px; overflow:auto;">
            <table id="report-table" class="admin-table" style="width:100%;">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Order</th>
                        <th>Nama Produk / Permintaan</th>
                        <th>Jumlah (Qty)</th>
                        <th>Harga Satuan</th>
                        <th>Total Penjualan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="6" style="color:#888; padding:18px;">Klik Refresh untuk memuat data laporan.</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>