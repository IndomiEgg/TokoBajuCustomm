<div class="admin-panel panel-status">
    <div class="admin-panel-card">
        <h2>Status Pelanggan & Pesanan</h2>
        <p>Pilih order untuk mengelola status pembayaran dan proses pesanan langsung dari dashboard.</p>

        <div class="status-summary-grid">
            <div class="status-count-card">
                <div>Paid</div>
                <strong><?= $payment_counts['paid'] ?? 0 ?></strong>
            </div>
            <div class="status-count-card">
                <div>Unpaid</div>
                <strong><?= $payment_counts['unpaid'] ?? 0 ?></strong>
            </div>
            <div class="status-count-card">
                <div>Pending</div>
                <strong><?= $order_status_counts['pending'] ?? 0 ?></strong>
            </div>
            <div class="status-count-card">
                <div>In Progress</div>
                <strong><?= $order_status_counts['in_progress'] ?? 0 ?></strong>
            </div>
            <div class="status-count-card">
                <div>Processing</div>
                <strong><?= $order_status_counts['processing'] ?? 0 ?></strong>
            </div>
            <div class="status-count-card">
                <div>Delivering</div>
                <strong><?= $order_status_counts['delivering'] ?? 0 ?></strong>
            </div>
            <div class="status-count-card">
                <div>Finished</div>
                <strong><?= $order_status_counts['finished'] ?? 0 ?></strong>
            </div>
            <div class="status-count-card">
                <div>Cancelled</div>
                <strong><?= $order_status_counts['cancelled'] ?? 0 ?></strong>
            </div>
        </div>

        <div class="status-filter-panel">
            <button type="button" class="status-filter-btn active" data-filter="all">Semua</button>
            <button type="button" class="status-filter-btn" data-filter="payment-paid">Paid</button>
            <button type="button" class="status-filter-btn" data-filter="payment-unpaid">Unpaid</button>
            <button type="button" class="status-filter-btn" data-filter="status-pending">Pending</button>
            <button type="button" class="status-filter-btn" data-filter="status-in_progress">In Progress</button>
            <button type="button" class="status-filter-btn" data-filter="status-processing">Processing</button>
            <button type="button" class="status-filter-btn" data-filter="status-delivering">Delivering</button>
            <button type="button" class="status-filter-btn" data-filter="status-finished">Finished</button>
            <button type="button" class="status-filter-btn" data-filter="status-cancelled">Cancelled</button>
        </div>

        <div class="status-message" id="status-message"></div>
    </div>

    <div class="admin-panel-card">
        <div style="max-height:520px; overflow:auto; margin-top:14px;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Deskripsi</th>
                        <th>Status Pembayaran</th>
                        <th>Status Order</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_orders as $o): ?>
                        <tr class="order-row" data-order-id="<?= esc($o['id']) ?>" data-order-status="<?= esc($o['order_status'] ?? '') ?>" data-payment-status="<?= esc($o['payment_status'] ?? 'unpaid') ?>">
                            <td>
                                <strong><?= esc($o['order_code']) ?></strong>
                                <div style="color:#94a3b8; font-size:12px; margin-top:6px;"><?= date('d M Y', strtotime($o['created_at'])) ?></div>
                            </td>
                            <td><?= esc($o['design_description'] ?? '-') ?></td>
                            <td>
                                <span class="payment-badge payment-<?= esc($o['payment_status'] ?? 'unpaid') ?>">
                                    <?= esc(ucfirst($o['payment_status'] ?? 'unpaid')) ?>
                                </span>
                            </td>
                            <td>
                                <form method="post" action="<?= base_url('admin/orders/update-status/'.$o['id']) ?>" class="admin-panel-form order-status-form" data-order-id="<?= $o['id'] ?>" data-current-status="<?= esc($o['order_status'] ?? '') ?>">
                                    <?= csrf_field() ?>
                                        <select name="status" <?= ($o['order_status'] ?? '')==='finished' ? 'disabled' : '' ?>>
                                        <option value="pending" <?= ($o['order_status'] ?? '')==='pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="in_progress" <?= ($o['order_status'] ?? '')==='in_progress' ? 'selected' : '' ?>>In Progress</option>
                                        <option value="processing" <?= ($o['order_status'] ?? '')==='processing' ? 'selected' : '' ?>>Processing</option>
                                        <option value="delivering" <?= ($o['order_status'] ?? '')==='delivering' ? 'selected' : '' ?>>Delivering</option>
                                        <option value="finished" <?= ($o['order_status'] ?? '')==='finished' ? 'selected' : '' ?>>Finished</option>
                                        <option value="cancelled" <?= ($o['order_status'] ?? '')==='cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" class="btn-luxury btn-luxury-sm" <?= ($o['order_status'] ?? '')==='finished' ? 'disabled' : '' ?>>Update</button>
                                </form>
                            </td>
                            <td style="text-align:right; display:flex; justify-content:flex-end; gap:10px; align-items:center;">
                                <?php if (($o['payment_status'] ?? 'unpaid') === 'unpaid' && ($o['order_status'] ?? '')!=='finished'): ?>
                                    <button type="button" class="btn-luxury btn-luxury-sm btn-mark-paid" data-order-id="<?= $o['id'] ?>" data-order-code="<?= esc($o['order_code']) ?>">Mark Paid</button>
                                <?php else: ?>
                                    <span class="payment-locked"><?= ($o['order_status'] ?? '')==='finished' ? 'Finished' : 'Paid' ?></span>
                                <?php endif; ?>
                                <a href="<?= base_url('admin/orders') ?>" class="btn-luxury btn-luxury-outline">Kelola</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="confirm-modal hidden" id="action-confirm-modal">
        <div class="confirm-modal-backdrop"></div>
        <div class="confirm-modal-panel">
            <h3>Konfirmasi Aksi</h3>
            <p id="confirm-modal-text">Apakah Anda yakin?</p>
            <div class="confirm-modal-actions">
                <button type="button" class="btn-luxury btn-luxury-outline" id="confirm-modal-cancel">Batal</button>
                <button type="button" class="btn-luxury btn-luxury-solid" id="confirm-modal-accept">Lanjutkan</button>
            </div>
        </div>
    </div>
</div>