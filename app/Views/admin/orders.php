<?= $this->extend('layout/template') ?>

<?= $this->section('styles') ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin-dashboard.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="admin-dashboard-container">
    <div class="admin-dashboard-header">
        <h1>Kelola Pesanan</h1>
        <p>Kelola status, harga, dan konfirmasi pesanan dari pelanggan</p>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-card">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order Code</th>
                        <th>Customer ID</th>
                        <th>Total Harga</th>
                        <th>Status Order</th>
                        <th>Status Pembayaran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $o): ?>
                    <tr>
                        <td>
                            <strong><?= esc($o['order_code']) ?></strong>
                            <div style="color:#94a3b8; font-size:0.88rem; margin-top:4px;"><?= date('d M Y', strtotime($o['created_at'])) ?></div>
                        </td>
                        <td><?= esc($o['user_id']) ?></td>
                        <td>
                            <?php if (!empty($o['final_price']) && !empty($o['price_confirmed'])): ?>
                                <strong style="color:#a7f3d0;">Rp <?= number_format($o['final_price'],0,',','.') ?></strong>
                            <?php elseif (!empty($o['total_price'])): ?>
                                Rp <?= number_format($o['total_price'],0,',','.') ?>
                            <?php else: ?>
                                <span style="color:#fbbf24;">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="post" action="<?= base_url('admin/orders/update-status/'.$o['id']) ?>" style="display:inline;">
                                <?= csrf_field() ?>
                                <select name="status" <?= $o['order_status'] === 'finished' ? 'disabled' : '' ?> onchange="if(this.value === 'finished' && !confirm('Pesanan akan dipindahkan ke status Finished dan disertakan dalam laporan. Lanjutkan?')) { this.value='<?= esc($o['order_status']) ?>'; } else { this.form.submit(); }">
                                    <option value="pending" <?= $o['order_status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="approved" <?= $o['order_status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
                                    <option value="in_progress" <?= $o['order_status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                    <option value="processing" <?= $o['order_status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                                    <option value="delivering" <?= $o['order_status'] === 'delivering' ? 'selected' : '' ?>>Delivering</option>
                                    <option value="ready_to_ship" <?= $o['order_status'] === 'ready_to_ship' ? 'selected' : '' ?>>Ready to Ship</option>
                                    <option value="shipped" <?= $o['order_status'] === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                    <option value="delivered" <?= $o['order_status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                    <option value="finished" <?= $o['order_status'] === 'finished' ? 'selected' : '' ?>>Finished</option>
                                    <option value="cancelled" <?= $o['order_status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <span class="payment-badge <?= $o['payment_status'] === 'paid' ? 'payment-paid' : 'payment-unpaid' ?>">
                                <?= ucfirst($o['payment_status'] ?? 'unpaid') ?>
                            </span>
                        </td>
                        <td>
                            <div class="admin-panel-actions" style="gap:6px;">
                                <?php if (empty($o['price_confirmed'])): ?>
                                    <button class="btn-set-price btn-luxury btn-luxury-sm" data-order-id="<?= $o['id'] ?>" data-order-code="<?= esc($o['order_code']) ?>" data-final-price="<?= esc($o['final_price'] ?? '') ?>" title="Atur harga final dan konfirmasi">
                                        <i class="fa-solid fa-tag"></i> Set Price
                                    </button>
                                <?php else: ?>
                                    <span style="color:#a7f3d0; font-size:0.88rem;"><i class="fa-solid fa-check-circle"></i> Confirmed</span>
                                <?php endif; ?>
                                <a href="<?= base_url('user/history#'.urlencode($o['order_code'])) ?>" class="btn-luxury btn-luxury-sm" title="Lihat di halaman user" style="text-decoration:none; display:inline-block;">
                                    <i class="fa-solid fa-eye"></i> View
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="admin-action-row" style="margin-top:18px;">
        <a href="<?= base_url('admin/dashboard') ?>" class="btn-luxury btn-luxury-outline">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>
</div>

<!-- Set Price Modal -->
<div id="setPriceModal" class="confirm-modal hidden">
    <div class="confirm-modal-backdrop"></div>
    <div class="confirm-modal-panel">
        <h3 id="modalTitle"><i class="fa-solid fa-tag"></i> Set Final Price</h3>
        <form id="setPriceForm">
            <?= csrf_field() ?>
            <input type="hidden" name="order_id" id="modalOrderId" />
            
            <div style="margin-top:16px;">
                <label style="display:block; color:#cbd5e1; font-size:0.95rem; margin-bottom:6px;">Final Price (Rp)</label>
                <input type="text" name="final_price" id="modalFinalPrice" placeholder="Contoh: 500000" style="width:100%; padding:10px 12px; border-radius:10px; border:1px solid rgba(148,163,184,0.2); background:rgba(15,23,42,0.95); color:#e2e8f0;" />
            </div>

            <div style="margin-top:14px;">
                <label style="display:block; color:#cbd5e1; font-size:0.95rem; margin-bottom:6px;">Catatan (opsional)</label>
                <textarea name="price_note" id="modalPriceNote" placeholder="Tambahkan catatan terkait harga..." style="width:100%; padding:10px 12px; border-radius:10px; border:1px solid rgba(148,163,184,0.2); background:rgba(15,23,42,0.95); color:#e2e8f0; height:80px; resize:vertical;"></textarea>
            </div>

            <div style="margin-top:14px; display:flex; align-items:center; gap:8px;">
                <input type="checkbox" name="price_confirmed" id="modalPriceConfirmed" style="width:16px; height:16px; cursor:pointer;" />
                <label for="modalPriceConfirmed" style="color:#cbd5e1; cursor:pointer; font-size:0.95rem; margin:0;">Konfirmasi harga dan masukkan dalam laporan</label>
            </div>

            <div class="confirm-modal-actions" style="margin-top:20px;">
                <button type="button" id="modalCancel" class="btn-luxury btn-luxury-outline">Batal</button>
                <button type="submit" id="modalSave" class="btn-luxury btn-luxury-solid">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('click', function(e){
    if (e.target && e.target.closest('.btn-set-price')) {
        var btn = e.target.closest('.btn-set-price');
        var id = btn.getAttribute('data-order-id');
        var code = btn.getAttribute('data-order-code');
        var price = btn.getAttribute('data-final-price') || '';
        document.getElementById('modalOrderId').value = id;
        document.getElementById('modalFinalPrice').value = price;
        document.getElementById('modalPriceNote').value = '';
        document.getElementById('modalPriceConfirmed').checked = false;
        document.getElementById('modalTitle').innerText = 'Set Final Price — ' + code;
        document.getElementById('setPriceModal').classList.remove('hidden');
        document.getElementById('setPriceModal').style.display = 'flex';
    }
});

document.getElementById('modalCancel').addEventListener('click', function(){
    document.getElementById('setPriceModal').classList.add('hidden');
    document.getElementById('setPriceModal').style.display = 'none';
});

document.getElementById('setPriceForm').addEventListener('submit', function(ev){
    ev.preventDefault();
    var form = ev.target;
    var id = document.getElementById('modalOrderId').value;
    var data = new FormData(form);
    
    fetch('<?= base_url('admin/orders/update-price/') ?>' + id, {
        method: 'POST',
        body: data,
        headers: {'X-Requested-With': 'XMLHttpRequest'}
    })
    .then(r => r.json())
    .then(js => {
        if (js.success) {
            alert(js.message || 'Harga berhasil diperbarui');
            location.reload();
        } else {
            alert(js.message || 'Gagal memperbarui harga');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Terjadi kesalahan jaringan. Silakan coba lagi.');
    });
});
</script>
<?= $this->endSection() ?>
