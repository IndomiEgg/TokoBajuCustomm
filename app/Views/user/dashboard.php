<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<?php
// Ensure $user is available; fall back to session values for dev routes
if (empty($user) || !is_array($user)) {
    $user = [
        'full_name'    => session()->get('full_name') ?: session()->get('username') ?: 'Collector',
        'username'     => session()->get('username') ?: 'collector',
        'email'        => session()->get('email') ?: (session()->get('username') ? session()->get('username') . '@example.local' : 'collector@example.local'),
        'phone_number' => session()->get('phone_number') ?: null,
        'created_at'   => session()->get('created_at') ?: date('Y-m-d'),
    ];
}
?>

<div class="user-dashboard-container">
    <section class="dashboard-hero">
        <div class="dashboard-hero-copy">
            <span class="section-accent-tag">Portal Pelanggan</span>
            <h1 class="section-heading">Halo, <?= esc($user['full_name'] ?? session()->get('full_name') ?: session()->get('username') ?: 'Collector') ?></h1>
            <p class="section-subtitle">Kelola pesanan custom Anda, lihat gallery referensi, dan pesan karya baru dari atelier kami.</p>
        </div>
    </section>

    <!-- Success Flash Message -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success" style="max-width: 1120px; margin: 0 auto 40px; padding: 20px; background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); border-radius: 12px; color: #22c55e;">
            <i class="fa-solid fa-check-circle"></i>
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <!-- Account Summary Cards -->
    <section class="dashboard-summary-cards" style="max-width: 1120px; margin: 0 auto 60px; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
        
        <div style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); border: 1px solid rgba(204, 22, 47, 0.3); border-radius: 12px; padding: 24px; position: relative;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                <h3 style="font-size: 14px; color: #888; text-transform: uppercase; letter-spacing: 1px; margin: 0;">Pesanan Aktif</h3>
                <i class="fa-solid fa-box-open" style="color: #cc162f; font-size: 20px;"></i>
            </div>
            <div style="font-size: 36px; font-weight: bold; color: #fff; margin-bottom: 8px;">
                <?= ($orderStats['pending'] ?? 0) + ($orderStats['approved'] ?? 0) + ($orderStats['in_progress'] ?? 0) + ($orderStats['ready_to_ship'] ?? 0) + ($orderStats['shipped'] ?? 0) ?>
            </div>
            <p style="margin: 0; color: #888; font-size: 12px;">Menunggu, Disetujui, atau Sedang Dikerjakan</p>
        </div>

        <div style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); border: 1px solid rgba(204, 22, 47, 0.3); border-radius: 12px; padding: 24px; position: relative;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                <h3 style="font-size: 14px; color: #888; text-transform: uppercase; letter-spacing: 1px; margin: 0;">Siap Dikirim</h3>
                <i class="fa-solid fa-truck" style="color: #22c55e; font-size: 20px;"></i>
            </div>
            <div style="font-size: 36px; font-weight: bold; color: #fff; margin-bottom: 8px;">
                <?= ($orderStats['ready_to_ship'] ?? 0) + ($orderStats['shipped'] ?? 0) ?>
            </div>
            <p style="margin: 0; color: #888; font-size: 12px;">Dalam perjalanan atau siap diambil</p>
        </div>

        <div style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); border: 1px solid rgba(204, 22, 47, 0.3); border-radius: 12px; padding: 24px; position: relative;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                <h3 style="font-size: 14px; color: #888; text-transform: uppercase; letter-spacing: 1px; margin: 0;">Selesai</h3>
                <i class="fa-solid fa-check-circle" style="color: #3b82f6; font-size: 20px;"></i>
            </div>
            <div style="font-size: 36px; font-weight: bold; color: #fff; margin-bottom: 8px;">
                <?= ($orderStats['delivered'] ?? 0) + ($orderStats['finished'] ?? 0) ?>
            </div>
            <p style="margin: 0; color: #888; font-size: 12px;">Pesanan selesai, diterima, atau telah selesai diproses</p>
        </div>

        <div style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); border: 1px solid rgba(204, 22, 47, 0.3); border-radius: 12px; padding: 24px; position: relative;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                <h3 style="font-size: 14px; color: #888; text-transform: uppercase; letter-spacing: 1px; margin: 0;">Total Belanja</h3>
                <i class="fa-solid fa-wallet" style="color: #f59e0b; font-size: 20px;"></i>
            </div>
            <div style="font-size: 28px; font-weight: bold; color: #fff; margin-bottom: 8px;">
                Rp <?= number_format($totalSpending ?? 0, 0, ',', '.') ?>
            </div>
            <p style="margin: 0; color: #888; font-size: 12px;">Total pembayaran yang telah diproses</p>
        </div>

    </section>

    <!-- Portfolio Gallery Section -->
    <section id="portfolio-gallery" class="gallery-section" style="margin-bottom: 80px;">
        <div class="atelier-container">
            
            <div class="section-title-block">
                <span class="section-accent-tag">Inspirasi Koleksi</span>
                <h2 class="section-heading">Gallery Referensi</h2>
                <p class="section-subtitle">Lihat koleksi masterpiece kami sebelumnya untuk mendapatkan inspirasi untuk pesanan custom Anda berikutnya.</p>
            </div>

            <!-- Dynamic Category Filter Tabs -->
            <div class="gallery-filter-panel">
                <button class="filter-tab active" data-filter="all">Semua Koleksi</button>
                <button class="filter-tab" data-filter="jacket">Custom Jacket</button>
                <button class="filter-tab" data-filter="hoodie">Custom Hoodie</button>
                <button class="filter-tab" data-filter="denim">Custom Denim</button>
                <button class="filter-tab" data-filter="shirt">Custom T-Shirt</button>
                <button class="filter-tab" data-filter="tattoo">Tattoo Inspired</button>
                <button class="filter-tab" data-filter="religious">Religious Art</button>
            </div>

            <!-- Masonry Portfolio Layout Grid -->
            <div class="masonry-gallery-grid" id="masonry-view">
                
                <!-- Project 1 -->
                <div class="masonry-item" data-category="jacket" data-img="https://images.unsplash.com/photo-1551028719-00167b16eac5?auto=format&fit=crop&w=800&q=80" data-title="WRATH OF SATAN LEATHER" data-year="2026" data-price="12500000" data-desc="Vintage distressed cowhide leather jacket with dynamic hand-painted dark theological art on the entire back panel. High-heat cured with cracked metallic accents.">
                    <div class="masonry-image-box">
                        <img src="https://images.unsplash.com/photo-1551028719-00167b16eac5?auto=format&fit=crop&w=800&q=80" alt="Wrath of Satan Leather" onerror="this.src='https://placehold.co/800x1100/020202/a61c3c?text=JACKET+1'">
                    </div>
                    <div class="masonry-item-overlay">
                        <span class="item-overlay-tag">Custom Jacket</span>
                        <h3 class="item-overlay-title">Wrath of Satan Leather</h3>
                        <span class="item-overlay-spec">2026 — View Specifications</span>
                    </div>
                </div>

                <!-- Project 2 -->
                <div class="masonry-item" data-category="hoodie" data-img="https://images.unsplash.com/photo-1543163521-1bf539c55dd2?auto=format&fit=crop&w=800&q=80" data-title="LAMENT ARCHIVE HOODIE" data-year="2025" data-price="2150000" data-desc="500GSM ultra-dense French Terry cotton hoodie featuring raw fine-line sleeve tattoo art drawings, raw distressed hems, and heavy hardware details.">
                    <div class="masonry-image-box">
                        <img src="https://images.unsplash.com/photo-1543163521-1bf539c55dd2?auto=format&fit=crop&w=800&q=80" alt="Lament Archive Hoodie" onerror="this.src='https://placehold.co/800x1100/020202/a61c3c?text=HOODIE+1'">
                    </div>
                    <div class="masonry-item-overlay">
                        <span class="item-overlay-tag">Custom Hoodie</span>
                        <h3 class="item-overlay-title">Lament Archive Hoodie</h3>
                        <span class="item-overlay-spec">2025 — View Specifications</span>
                    </div>
                </div>

                <!-- Project 3 -->
                <div class="masonry-item" data-category="denim" data-img="https://images.unsplash.com/photo-1576995853123-5a10305d93c0?auto=format&fit=crop&w=800&q=80" data-title="FALLEN ANGELS DENIM" data-year="2026" data-price="4950000" data-desc="15oz rigid Japanese indigo selvedge denim jacket painted with white ink, highlighting gothic angel graphics cascading into a scratched crimson wash.">
                    <div class="masonry-image-box">
                        <img src="https://images.unsplash.com/photo-1576995853123-5a10305d93c0?auto=format&fit=crop&w=800&q=80" alt="Fallen Angels Blue Denim" onerror="this.src='https://placehold.co/800x1100/020202/a61c3c?text=DENIM+1'">
                    </div>
                    <div class="masonry-item-overlay">
                        <span class="item-overlay-tag">Custom Denim</span>
                        <h3 class="item-overlay-title">Fallen Angels Denim</h3>
                        <span class="item-overlay-spec">2026 — View Specifications</span>
                    </div>
                </div>

                <!-- Project 4 -->
                <div class="masonry-item" data-category="shirt" data-img="https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=800&q=80" data-title="SACRED HEART GOTHIC TEE" data-year="2026" data-price="950000" data-desc="Raw-edge combed heavyweight long tee featuring hand-printed distressed religious cathedral lines and ink splatter details down both sleeves.">
                    <div class="masonry-image-box">
                        <img src="https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=800&q=80" alt="Sacred Heart Gothic Shirt" onerror="this.src='https://placehold.co/800x1100/020202/a61c3c?text=SHIRT+1'">
                    </div>
                    <div class="masonry-item-overlay">
                        <span class="item-overlay-tag">Custom T-Shirt</span>
                        <h3 class="item-overlay-title">Sacred Heart Gothic Tee</h3>
                        <span class="item-overlay-spec">2026 — View Specifications</span>
                    </div>
                </div>

                <!-- Project 5 -->
                <div class="masonry-item" data-category="tattoo" data-img="https://images.unsplash.com/photo-1508186227413-df1f5ecdeff1?auto=format&fit=crop&w=800&q=80" data-title="TRIBAL BLADE LINEN OVERCOAT" data-year="2025" data-price="8500000" data-desc="Raw organic black linen overcoat featuring deep tribal tattoo ink curves trailing from the neck, down the spine, wrapping around the cuffs.">
                    <div class="masonry-image-box">
                        <img src="https://images.unsplash.com/photo-1508186227413-df1f5ecdeff1?auto=format&fit=crop&w=800&q=80" alt="Tribal Blade Linen Overcoat" onerror="this.src='https://placehold.co/800x1100/020202/a61c3c?text=TATTOO+1'">
                    </div>
                    <div class="masonry-item-overlay">
                        <span class="item-overlay-tag">Tattoo Inspired</span>
                        <h3 class="item-overlay-title">Tribal Blade Linen</h3>
                        <span class="item-overlay-spec">2025 — View Specifications</span>
                    </div>
                </div>

                <!-- Project 6 -->
                <div class="masonry-item" data-category="religious" data-img="https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=800&q=80" data-title="OUR LADY OF SORROWS TRENCH" data-year="2026" data-price="14000000" data-desc="A full double-breasted premium dark grey gabardine trench coat featuring high-fidelity religious realism murals, finished with hand-burnished details.">
                    <div class="masonry-image-box">
                        <img src="https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=800&q=80" alt="Our Lady of Sorrows Trench" onerror="this.src='https://placehold.co/800x1100/020202/a61c3c?text=RELIGIOUS+1'">
                    </div>
                    <div class="masonry-item-overlay">
                        <span class="item-overlay-tag">Religious Art</span>
                        <h3 class="item-overlay-title">Lady of Sorrows Trench</h3>
                        <span class="item-overlay-spec">2026 — View Specifications</span>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Pricing Guide Section -->
    <section id="pricing-dashboard" class="pricing-guide-section section-darker" style="margin-bottom: 80px;">
        <div class="atelier-container">
            
            <div class="section-title-block">
                <span class="section-accent-tag">Panduan Harga</span>
                <h2 class="section-heading">Tier Komisi Custom</h2>
                <p class="section-subtitle">Lihat estimasi harga berdasarkan kompleksitas desain dan jangka waktu pengerjaan.</p>
            </div>

            <div class="price-reference-grid">
                
                <!-- Category 1 -->
                <div class="price-reference-card">
                    <div class="pcard-visual">
                        <span class="pcard-budget-badge">Under Rp 1JT</span>
                        <img src="https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=600&q=80" alt="Minimal Custom Work">
                    </div>
                    <div class="pcard-info">
                        <span class="pcard-tier">Minimalist Tier</span>
                        <h3 class="pcard-heading">Small Accents</h3>
                        <ul class="pcard-specs">
                            <li><i class="fa-solid fa-gauge-high"></i> Difficulty: <span>Low (Line Art/Lettering)</span></li>
                            <li><i class="fa-solid fa-hourglass-half"></i> Timeframe: <span>2 - 4 Days</span></li>
                        </ul>
                        <p class="pcard-desc">Ideal untuk cetakan dada kecil, linework lengan sederhana, pola lengan minor, atau lettering single-color.</p>
                        <div class="pcard-examples-list">
                            <span>Simple sleeve artwork</span>
                            <span>Small chest artwork</span>
                            <span>Minimal custom design</span>
                        </div>
                    </div>
                </div>

                <!-- Category 2 -->
                <div class="price-reference-card">
                    <div class="pcard-visual">
                        <span class="pcard-budget-badge">Rp 1JT - Rp 3JT</span>
                        <img src="https://images.unsplash.com/photo-1516257984-b1b4d707412e?auto=format&fit=crop&w=600&q=80" alt="Medium Custom Work">
                    </div>
                    <div class="pcard-info">
                        <span class="pcard-tier">Symmetrical Tier</span>
                        <h3 class="pcard-heading">Bespoke Illustrations</h3>
                        <ul class="pcard-specs">
                            <li><i class="fa-solid fa-gauge-high"></i> Difficulty: <span>Medium (Detailed Shading)</span></li>
                            <li><i class="fa-solid fa-hourglass-half"></i> Timeframe: <span>5 - 8 Days</span></li>
                        </ul>
                        <p class="pcard-desc">Cocok untuk grafis pusat skala menengah, panel belakang setengah rumit, teks multi-layer, dan bungkus tribal cat tangan.</p>
                        <div class="pcard-examples-list">
                            <span>Half back artwork</span>
                            <span>Medium-size illustration</span>
                            <span>Detailed lettering</span>
                        </div>
                    </div>
                </div>

                <!-- Category 3 -->
                <div class="price-reference-card">
                    <div class="pcard-visual">
                        <span class="pcard-budget-badge">Rp 3JT - Rp 6JT</span>
                        <img src="https://images.unsplash.com/photo-1543163521-1bf539c55dd2?auto=format&fit=crop&w=600&q=80" alt="Full Back Custom Work">
                    </div>
                    <div class="pcard-info">
                        <span class="pcard-tier">Masterpiece Tier</span>
                        <h3 class="pcard-heading">Full Panel Artwork</h3>
                        <ul class="pcard-specs">
                            <li><i class="fa-solid fa-gauge-high"></i> Difficulty: <span>High (Realism / Multi-color)</span></li>
                            <li><i class="fa-solid fa-hourglass-half"></i> Timeframe: <span>9 - 14 Days</span></li>
                        </ul>
                        <p class="pcard-desc">Dirancang untuk panel belakang penuh, komposisi multi-warna, motif yang tumpang tindih, dan realism katedral kontras tinggi.</p>
                        <div class="pcard-examples-list">
                            <span>Full back artwork</span>
                            <span>Multi-color design</span>
                            <span>Complex custom concept</span>
                        </div>
                    </div>
                </div>

                <!-- Category 4 -->
                <div class="price-reference-card">
                    <div class="pcard-visual">
                        <span class="pcard-budget-badge">Rp 6JT+</span>
                        <img src="https://images.unsplash.com/photo-1551028719-00167b16eac5?auto=format&fit=crop&w=600&q=80" alt="Full Garment Project">
                    </div>
                    <div class="pcard-info">
                        <span class="pcard-tier">Atelier Elite Tier</span>
                        <h3 class="pcard-heading">Bespoke Masterpiece</h3>
                        <ul class="pcard-specs">
                            <li><i class="fa-solid fa-gauge-high"></i> Difficulty: <span>Extreme (Full Composition)</span></li>
                            <li><i class="fa-solid fa-hourglass-half"></i> Timeframe: <span>15 - 25 Days</span></li>
                        </ul>
                        <p class="pcard-desc">Layanan unggulan kami. Jaket cat tangan dengan cakupan penuh, set yang cocok, denim overalls terdistorsi, dan konsep tingkat museum.</p>
                        <div class="pcard-examples-list">
                            <span>Full garment artwork</span>
                            <span>Premium one-of-one masterpiece</span>
                            <span>Highly detailed custom project</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Recent Orders Section -->
    <section id="recent-orders" class="recent-orders-section" style="max-width: 1120px; margin: 0 auto 80px;">
        <div class="section-title-block" style="text-align: center; margin-bottom: 40px;">
            <span class="section-accent-tag">Riwayat Pesanan</span>
            <h2 class="section-heading">Pesanan Terbaru Anda</h2>
        </div>

        <?php if (!empty($recentOrders)): ?>
            <div style="display: grid; gap: 20px;">
                <?php foreach ($recentOrders as $order): 
                    $orderCode = esc($order['order_code'] ?? '—');
                    $orderStatus = $order['order_status'] ?? 'unknown';
                    $statusLabel = ucwords(str_replace('_', ' ', $orderStatus));
                    $badgeColor = '#cc162f';
                    if(in_array($orderStatus, ['delivered','completed'])) $badgeColor = '#22c55e';
                    elseif(in_array($orderStatus, ['in_progress','processing'])) $badgeColor = '#3b82f6';
                    elseif(in_array($orderStatus, ['approved'])) $badgeColor = '#f59e0b';
                    $productLabel = esc($order['product_type'] ?? 'Custom Item');
                    $themeLabel = esc($order['artwork_theme'] ?? '—');
                    $desc = esc($order['design_description'] ?? 'No description');
                    if(strlen($desc) > 180) $desc = substr($desc,0,177) . '...';
                    $budget = esc($order['budget_range'] ?? '—');
                    $deadline = '-';
                    if(!empty($order['target_deadline']) && strtotime($order['target_deadline'])) $deadline = date('d M Y', strtotime($order['target_deadline']));
                    $totalPriceRaw = isset($order['total_price']) ? (float)$order['total_price'] : 0;
                    $totalPrice = 'Rp ' . number_format($totalPriceRaw, 0, ',', '.');
                ?>
                    <div style="background: linear-gradient(135deg, #0f0f0f 0%, #1f1f1f 100%); border: 1px solid rgba(204,22,47,0.14); border-radius: 12px; padding: 20px; display: grid; grid-template-columns: 1fr 160px; gap: 18px; align-items: center;">
                        <div>
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                                <span style="font-size: 12px; color: #bdbdbd; text-transform: uppercase; letter-spacing: 1px;"><?= $orderCode ?></span>
                                <span style="display: inline-block; padding: 6px 12px; background: rgba(0,0,0,0.35); color: <?= $badgeColor ?>; font-size: 12px; text-transform: uppercase; border-radius: 999px; letter-spacing: 0.4px;">
                                    <?= $statusLabel ?>
                                </span>
                            </div>
                            <h3 style="margin: 0 0 8px 0; font-size: 17px; color: #fff;"><?= $productLabel ?> — <?= $themeLabel ?></h3>
                            <p style="margin: 0 0 12px 0; color: #9a9a9a; font-size: 13px;"><?= $desc ?></p>
                            <div style="display: flex; gap: 18px; font-size: 13px; color: #cfcfcf;">
                                <div>
                                    <div style="color: #888; font-size: 11px;">Budget</div>
                                    <strong style="color: #fff;"><?= $budget ?></strong>
                                </div>
                                <div>
                                    <div style="color: #888; font-size: 11px;">Deadline</div>
                                    <strong style="color: #fff;"><?= $deadline ?></strong>
                                </div>
                                <div>
                                    <div style="color: #888; font-size: 11px;">Total</div>
                                    <strong style="color: #22c55e;"><?= $totalPrice ?></strong>
                                </div>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <a href="<?= base_url('user/history#' . $orderCode) ?>" class="btn-luxury btn-luxury-outline" style="white-space: nowrap;">View Details →</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 60px 20px; background: rgba(0,0,0,0.3); border-radius: 12px;">
                <i class="fa-solid fa-inbox" style="font-size: 48px; color: #888; margin-bottom: 20px; display: block;"></i>
                <h3 style="color: #888; margin: 0 0 10px 0;">Belum Ada Pesanan</h3>
                <p style="color: #666; margin: 0;">Mulai pesanan custom pertama Anda hari ini!</p>
                <a href="<?= base_url('user/commission-form') ?>" class="btn-luxury btn-luxury-solid" style="margin-top: 20px; display: inline-block;">Pesan Sekarang</a>
            </div>
        <?php endif; ?>

        <div style="text-align: center; margin-top: 40px;">
            <a href="<?= base_url('user/history') ?>" class="btn-luxury btn-luxury-outline">Lihat Semua Pesanan →</a>
            <a href="<?= base_url('user/commission-form') ?>" class="btn-luxury btn-luxury-solid" style="margin-left: 16px;">Buat Pesanan Baru →</a>
        </div>
    </section>

    <!-- Profile Settings Section -->
    <section id="profile-settings" class="profile-settings-section" style="max-width: 1120px; margin: 0 auto;">
        <div class="section-title-block" style="text-align: center; margin-bottom: 40px;">
            <span class="section-accent-tag">Pengaturan Akun</span>
            <h2 class="section-heading">Profil Pelanggan</h2>
        </div>

        <div style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); border: 1px solid rgba(204, 22, 47, 0.3); border-radius: 12px; padding: 40px;">
            
            <div style="display: grid; grid-template-columns: 200px 1fr; gap: 40px; margin-bottom: 40px; padding-bottom: 40px; border-bottom: 1px solid rgba(204, 22, 47, 0.2);">
                <div style="text-align: center;">
                    <div style="width: 180px; height: 180px; background: rgba(204, 22, 47, 0.1); border: 2px dashed rgba(204, 22, 47, 0.3); border-radius: 18px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                        <i class="fa-solid fa-user" style="font-size: 64px; color: #cc162f; opacity: 0.5;"></i>
                    </div>
                    <a href="<?= base_url('user/edit-profile') ?>" class="btn-luxury btn-luxury-outline" style="margin-top: 16px; width: 100%;">Edit Profile</a>
                </div>

                <div>
                    <h3 style="margin: 0 0 24px 0; color: #fff; font-size: 20px;">Informasi Profil</h3>
                    
                    <div style="display: grid; gap: 20px;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div>
                                <label style="display: block; color: #888; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Full Name</label>
                                <div style="background: rgba(0,0,0,0.3); border: 1px solid rgba(204, 22, 47, 0.2); border-radius: 8px; padding: 12px 16px; color: #fff;">
                                    <?= esc($user['full_name']) ?>
                                </div>
                            </div>
                            <div>
                                <label style="display: block; color: #888; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Username</label>
                                <div style="background: rgba(0,0,0,0.3); border: 1px solid rgba(204, 22, 47, 0.2); border-radius: 8px; padding: 12px 16px; color: #fff;">
                                    <?= esc($user['username']) ?>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label style="display: block; color: #888; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Email Address</label>
                            <div style="background: rgba(0,0,0,0.3); border: 1px solid rgba(204, 22, 47, 0.2); border-radius: 8px; padding: 12px 16px; color: #fff;">
                                <?= esc($user['email']) ?>
                            </div>
                        </div>

                        <div>
                            <label style="display: block; color: #888; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Phone Number</label>
                            <div style="background: rgba(0,0,0,0.3); border: 1px solid rgba(204, 22, 47, 0.2); border-radius: 8px; padding: 12px 16px; color: #fff;">
                                <?= esc($user['phone_number'] ?? 'Not provided') ?>
                            </div>
                        </div>

                        <div>
                            <label style="display: block; color: #888; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Member Since</label>
                            <div style="background: rgba(0,0,0,0.3); border: 1px solid rgba(204, 22, 47, 0.2); border-radius: 8px; padding: 12px 16px; color: #fff;">
                                <?= date('d M Y', strtotime($user['created_at'])) ?>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 24px;">
                        <a href="<?= base_url('user/edit-profile') ?>" class="btn-luxury btn-luxury-solid">Edit Profile →</a>
                        <a href="<?= base_url('auth/logout') ?>" style="display: inline-block; margin-left: 12px; padding: 12px 24px; border: 1px solid rgba(204, 22, 47, 0.5); border-radius: 6px; color: #cc162f; text-decoration: none; font-size: 14px; font-weight: 600; transition: all 0.3s ease;">
                            Logout
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/home.js') ?>"></script>
<?= $this->endSection() ?>

