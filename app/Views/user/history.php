<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<div class="user-dashboard-container">
    <section class="history-hero">
        <div class="dashboard-hero-copy">
            <span class="section-accent-tag">Riwayat Pemesanan</span>
            <h1 class="section-heading">Status Pesanan Anda</h1>
            <p class="section-subtitle">Riwayat pemesanan dan update tracking akan muncul di sini setelah Anda memesan.</p>
        </div>
    </section>

    <?php if (!empty($orders)): ?>
        <section class="history-orders-list" style="padding-bottom: 60px;">
            <div class="section-title-block" style="margin-bottom: 30px;">
                <h2 class="section-heading" style="font-size: 2rem;">Pesanan Anda</h2>
                <p class="section-subtitle">Semua order yang telah masuk akan ditampilkan di bawah.</p>
            </div>

            <div class="orders-grid" style="display:grid; gap:20px;">
                <?php foreach ($orders as $order): 
                    $orderCode = esc($order['order_code'] ?? '—');
                    $orderAnchor = 'order-' . preg_replace('/[^A-Za-z0-9_-]/', '', $orderCode);
                    $statusLabel = esc(ucwords(str_replace('_', ' ', $order['order_status'] ?? 'pending')));
                    $paymentLabel = esc($paymentStatuses[$order['payment_status']] ?? $order['payment_status'] ?? 'Unpaid');
                    $badgeColor = '#cc162f';
                    if (in_array($order['order_status'], ['delivered','completed'])) $badgeColor = '#22c55e';
                    elseif (in_array($order['order_status'], ['in_progress','processing'])) $badgeColor = '#3b82f6';
                    elseif ($order['order_status'] === 'approved') $badgeColor = '#f59e0b';
                    $productLabel = esc($order['product_type'] ?? 'Custom Item');
                    $themeLabel = esc($order['artwork_theme'] ?? '—');
                    $totalPrice = isset($order['total_price']) ? 'Rp ' . number_format((float) $order['total_price'], 0, ',', '.') : 'Rp 0';
                    $createdAt = !empty($order['created_at']) ? date('d M Y', strtotime($order['created_at'])) : '-';
                ?>
                    <div id="<?= esc($orderAnchor) ?>" style="background: linear-gradient(135deg, #0f0f0f 0%, #181818 100%); border: 1px solid rgba(204,22,47,0.15); border-radius: 16px; padding: 24px; display:flex; flex-wrap:wrap; justify-content:space-between; gap:18px; position:relative;">
                        <div style="max-width: 100%;">
                            <div style="display:flex; align-items:center; gap:12px; margin-bottom:14px; flex-wrap:wrap;">
                                <span style="font-size:12px; color:#bdbdbd; text-transform:uppercase; letter-spacing:1px;"><?= $orderCode ?></span>
                                <span style="display:inline-block; padding:7px 14px; background: rgba(255,255,255,0.05); color: <?= $badgeColor ?>; font-size:12px; text-transform:uppercase; border-radius:999px; letter-spacing:0.4px;"><?= $statusLabel ?></span>
                                <span style="display:inline-block; padding:7px 14px; background: rgba(255,255,255,0.05); color: #38bdf8; font-size:12px; text-transform:uppercase; border-radius:999px; letter-spacing:0.4px;"><?= $paymentLabel ?></span>
                            </div>
                            <h3 style="margin:0 0 12px 0; font-size:18px; color:#fff;"><?= $productLabel ?> — <?= $themeLabel ?></h3>
                            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap:12px; font-size:13px; color:#cfcfcf; margin-bottom:18px;">
                                <div><div style="color:#888; font-size:11px;">Total</div><strong style="color:#22c55e;"><?= $totalPrice ?></strong></div>
                                <div><div style="color:#888; font-size:11px;">Tanggal</div><strong style="color:#fff;"><?= $createdAt ?></strong></div>
                                <div><div style="color:#888; font-size:11px;">Deadline</div><strong style="color:#fff;"><?= !empty($order['target_deadline']) ? date('d M Y', strtotime($order['target_deadline'])) : '-' ?></strong></div>
                            </div>
                            <p style="margin:0; color:#9a9a9a; font-size:14px; line-height:1.8;"><?= esc($order['design_description'] ?? 'Deskripsi belum tersedia.') ?></p>
                        </div>
                        <div style="display:flex; flex-direction:column; gap:12px; align-items:flex-start; min-width:220px;">
                            <button type="button" class="btn-luxury btn-luxury-outline order-detail-toggle" data-target="detail-<?= esc($orderAnchor) ?>" style="white-space: nowrap;">Lihat Detail</button>
                            <?php if (($order['payment_status'] ?? 'unpaid') === 'unpaid'): ?>
                                <button type="button" class="btn-luxury btn-luxury-solid history-pay-button" data-order-code="<?= esc($orderCode) ?>" data-order-total="<?= esc($order['total_price'] ?? 0) ?>" style="white-space: nowrap;">Lakukan Pembayaran</button>
                            <?php endif; ?>
                            <button type="button" class="btn-luxury btn-luxury-outline history-download-button" data-order-code="<?= esc($orderCode) ?>" data-order-total="<?= esc($order['total_price'] ?? 0) ?>" data-order-status="<?= esc($order['payment_status'] ?? 'unpaid') ?>" data-order-method="<?= esc($order['payment_method'] ?? 'whatsapp') ?>" data-order-created="<?= esc($createdAt) ?>" data-order-deadline="<?= esc(!empty($order['target_deadline']) ? date('d M Y', strtotime($order['target_deadline'])) : '-') ?>" data-order-description="<?= esc($order['design_description'] ?? 'Deskripsi belum tersedia.') ?>">Download Struk</button>
                            <span style="font-size:12px; color:#888;">Metode pembayaran: <?= esc(strtoupper($order['payment_method'] ?? 'WA')) ?></span>
                        </div>
                        <div id="detail-<?= esc($orderAnchor) ?>" class="order-detail-panel" style="width:100%; margin-top:18px; padding:18px 22px; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; display:none;">
                            <div style="display:grid; gap:14px; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); margin-bottom:18px; font-size:13px; color:#ddd;">
                                <div><span style="display:block; color:#888; font-size:11px;">Order Code</span><strong style="color:#fff;"><?= $orderCode ?></strong></div>
                                <div><span style="display:block; color:#888; font-size:11px;">Status Order</span><strong style="color:<?= $badgeColor ?>;"><?= $statusLabel ?></strong></div>
                                <div><span style="display:block; color:#888; font-size:11px;">Status Pembayaran</span><strong style="color:#38bdf8;"><?= $paymentLabel ?></strong></div>
                                <div><span style="display:block; color:#888; font-size:11px;">Metode Bayar</span><strong style="color:#fff;"><?= esc(strtoupper($order['payment_method'] ?? 'WA')) ?></strong></div>
                            </div>
                            <div style="display:grid; gap:12px; font-size:13px; color:#ccc; margin-bottom:16px;">
                                <div><span style="color:#888; font-size:11px;">Total Bayar</span><strong style="color:#22c55e;"><?= $totalPrice ?></strong></div>
                                <div><span style="color:#888; font-size:11px;">Tanggal Dibuat</span><strong style="color:#fff;"><?= $createdAt ?></strong></div>
                                <div><span style="color:#888; font-size:11px;">Target Deadline</span><strong style="color:#fff;"><?= !empty($order['target_deadline']) ? date('d M Y', strtotime($order['target_deadline'])) : '-' ?></strong></div>
                            </div>
                            <div style="font-size:13px; color:#ccc; line-height:1.8;">
                                <div style="color:#888; font-size:11px; margin-bottom:8px;">Deskripsi Order</div>
                                <p style="margin:0; color:#ddd;"><?= nl2br(esc($order['design_description'] ?? 'Tidak ada deskripsi tambahan.')) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php else: ?>
        <section class="history-empty-state">
            <div class="history-note">
                <p>Belum ada riwayat pesanan.</p>
                <p>Silakan pesan custom design untuk melihat status order Anda.</p>
            </div>
        </section>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function () {
  const toggles = document.querySelectorAll('.order-detail-toggle');
  if (!toggles.length) return;

  function setPanelState(button, open) {
    const target = document.getElementById(button.dataset.target);
    if (!target) return;
    target.style.display = open ? 'block' : 'none';
    button.textContent = open ? 'Sembunyikan Detail' : 'Lihat Detail';
    if (open) {
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }

  toggles.forEach(function (btn) {
    btn.addEventListener('click', function () {
      const target = document.getElementById(this.dataset.target);
      if (!target) return;
      const isOpen = target.style.display === 'block';
      setPanelState(this, !isOpen);
    });
  });

  function formatIndoRupiah(value) {
    const number = Number(value || 0);
    return number.toLocaleString('id-ID');
  }

  function openDetailFromHash() {
    const hash = window.location.hash.replace('#', '');
    if (!hash) return;
    const orderCode = decodeURIComponent(hash);
    const matchingToggle = Array.from(toggles).find(function (btn) {
      return btn.dataset.target === 'detail-order-' + orderCode.replace(/[^A-Za-z0-9_-]/g, '');
    });
    if (matchingToggle) {
      setPanelState(matchingToggle, true);
    }
  }

  function buildReceiptData(button) {
    const orderCode = button.dataset.orderCode || 'UNKNOWN';
    const orderTotal = Number(button.dataset.orderTotal || 0);
    const orderStatus = button.dataset.orderStatus || 'unpaid';
    const orderMethod = button.dataset.orderMethod || 'whatsapp';
    const orderCreated = button.dataset.orderCreated || '';
    const orderDeadline = button.dataset.orderDeadline || '';
    const orderDescription = button.dataset.orderDescription || '';

    return {
      order_code: orderCode,
      total_price: orderTotal,
      payment_status: orderStatus,
      payment_method: orderMethod,
      created_at: orderCreated,
      target_deadline: orderDeadline,
      description: orderDescription,
    };
  }

  function downloadOrderReceipt(button) {
    const data = buildReceiptData(button);
    const orderCode = data.order_code;
    const width = 1000;
    const height = 1240;
    const canvas = document.createElement('canvas');
    canvas.width = width;
    canvas.height = height;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    ctx.fillStyle = '#080808';
    ctx.fillRect(0, 0, width, height);
    ctx.fillStyle = '#121212';
    ctx.fillRect(40, 40, width - 80, 260);

    ctx.fillStyle = '#f8fafc';
    ctx.font = 'bold 44px Montserrat, sans-serif';
    ctx.fillText('BATOM RECEIPT', 70, 110);
    ctx.font = '18px Montserrat, sans-serif';
    ctx.fillStyle = '#94a3b8';
    ctx.fillText('One-of-One Wearable Art', 70, 145);

    ctx.fillStyle = '#f8fafc';
    ctx.font = '16px Montserrat, sans-serif';
    ctx.fillText(`Order Code: ${orderCode}`, 70, 200);
    ctx.fillText(`Status: ${data.payment_status}`, 70, 230);
    ctx.fillText(`Metode: ${data.payment_method}`, 70, 260);
    ctx.fillText(`Tanggal: ${data.created_at}`, 70, 290);
    ctx.fillText(`Deadline: ${data.target_deadline}`, 70, 320);
    ctx.fillText(`Total: Rp ${formatIndoRupiah(data.total_price)}`, 70, 350);

    ctx.fillStyle = '#334155';
    ctx.fillRect(70, 380, width - 140, 2);

    ctx.font = 'bold 20px Montserrat, sans-serif';
    ctx.fillStyle = '#f8fafc';
    ctx.fillText('Deskripsi Order:', 70, 420);

    ctx.font = '16px Montserrat, sans-serif';
    ctx.fillStyle = '#cbd5e1';
    const textLines = data.description.split(/\r?\n|\|/).map(line => line.trim()).filter(Boolean);
    let textY = 450;
    const maxTextWidth = width - 160; // left+right padding (80px each side)
    const lineHeight = 26;

    function wrapAndDraw(text, x, y, prefix = '- ') {
      const words = text.split(' ');
      let line = '';
      let first = true;
      for (let i = 0; i < words.length; i++) {
        const testLine = line ? line + ' ' + words[i] : words[i];
        const metrics = ctx.measureText((first ? prefix : '') + testLine);
        if (metrics.width > maxTextWidth) {
          // draw current line
          ctx.fillText((first ? prefix : '') + line, x, y);
          y += lineHeight;
          if (y > height - 140) return { y, done: true };
          line = words[i];
          first = false;
        } else {
          line = testLine;
        }
      }
      if (line) {
        ctx.fillText((first ? prefix : '') + line, x, y);
        y += lineHeight;
      }
      return { y, done: false };
    }

    for (const ln of textLines) {
      if (textY > height - 140) break;
      const res = wrapAndDraw(ln, 80, textY, '- ');
      textY = res.y;
      if (res.done) break;
    }

    ctx.fillStyle = '#334155';
    ctx.fillRect(70, height - 220, width - 140, 2);
    ctx.font = '16px Montserrat, sans-serif';
    ctx.fillStyle = '#94a3b8';
    ctx.fillText('Struk ini dapat dipakai sebagai bukti pembayaran dan proses order.', 70, height - 180);

    const link = document.createElement('a');
    link.href = canvas.toDataURL('image/jpeg', 0.92);
    link.download = `BATOM_Struk_${orderCode.replace(/[^A-Za-z0-9\-_]/g, '_')}.jpg`;
    document.body.appendChild(link);
    link.click();
    link.remove();
  }

  document.querySelectorAll('.history-download-button').forEach(function (btn) {
    btn.addEventListener('click', function () {
      downloadOrderReceipt(this);
    });
  });

  document.querySelectorAll('.history-pay-button').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const orderCode = this.dataset.orderCode;
      const phone = '6281361073822';
      const message = encodeURIComponent(`Halo, saya ingin melanjutkan pembayaran untuk order ${orderCode}. Mohon bantu proses selanjutnya.`);
      window.open(`https://wa.me/${phone}?text=${message}`, '_blank');
    });
  });

  openDetailFromHash();
})();
</script>
<?= $this->endSection() ?>
