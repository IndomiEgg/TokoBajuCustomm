<div class="admin-panel panel-overview">
    <div class="admin-panel-card">
        <h2>Ringkasan Cepat</h2>
        <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:14px;">
            <div class="admin-panel-card small">
                <div style="color:#94a3b8; font-size:12px;">Total Orders</div>
                <div style="font-size:24px; color:#fff; font-weight:700; margin-top:10px;"><?= $total_orders ?? 0 ?></div>
            </div>
            <div class="admin-panel-card small">
                <div style="color:#94a3b8; font-size:12px;">Unpaid</div>
                <div style="font-size:24px; color:#fff; font-weight:700; margin-top:10px;"><?= $orders_unpaid ?? 0 ?></div>
            </div>
            <div class="admin-panel-card small">
                <div style="color:#94a3b8; font-size:12px;">Paid</div>
                <div style="font-size:24px; color:#fff; font-weight:700; margin-top:10px;"><?= $orders_paid ?? 0 ?></div>
            </div>
        </div>
    </div>

    <div class="admin-panel-card">
        <h3>Recent Orders</h3>
        <div style="max-height:250px; overflow:auto; margin-top:14px;">
            <table class="admin-table">
                <tbody>
                    <?php foreach ($recent_orders as $o): ?>
                        <tr>
                            <td style="width:180px;"><strong><?= esc($o['order_code']) ?></strong><div style="color:#94a3b8; font-size:12px; margin-top:6px;"><?= date('d M Y', strtotime($o['created_at'])) ?></div></td>
                            <td><?= esc($o['design_description'] ?? '-') ?></td>
                            <td style="width:140px; text-align:right;"><span style="color:#60a5fa; font-weight:600;"><?= esc(strtoupper($o['payment_status'] ?? 'unpaid')) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>