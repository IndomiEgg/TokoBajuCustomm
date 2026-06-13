<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<div class="commission-form-container">
    <section class="form-hero">
        <div class="form-hero-copy">
            <span class="section-accent-tag">Pesanan Custom</span>
            <h1 class="section-heading">Pesan Karya Custom Anda</h1>
            <p class="section-subtitle">Kolaborasi langsung dengan tim atelier kami untuk menciptakan masterpiece one-of-one sesuai visi Anda.</p>
        </div>
    </section>

    <!-- Error/Success Messages -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-error" style="max-width: 900px; margin: 0 auto 40px;">
            <i class="fa-solid fa-exclamation-circle"></i>
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success" style="max-width: 900px; margin: 0 auto 40px; padding: 20px; background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); border-radius: 12px; color: #22c55e;">
            <i class="fa-solid fa-check-circle"></i>
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <!-- Commission Form -->
    <section id="commission-form" class="form-section">
        <div class="atelier-container" style="max-width: 900px;">
            
            <form method="POST" action="<?= base_url('user/create-commission') ?>" id="commission-form-element" class="commission-form">
                <?= csrf_field() ?>

                <!-- Step 1: Product Selection -->
                <div class="form-section-block" style="margin-bottom: 60px;">
                    <h2 class="form-section-title">
                        <span class="step-number">1</span>
                        Pilih Jenis Produk
                    </h2>
                    <p style="color: #888; margin: 12px 0 24px 0;">Pilih jenis garment yang ingin Anda customize.</p>

                    <div class="product-type-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 16px; margin-bottom: 24px;">
                        <label class="product-option">
                            <input type="radio" name="product_type" value="jacket" required onchange="updateProductSelection()">
                            <div class="product-option-box">
                                <span>Custom Jacket</span>
                            </div>
                        </label>
                        <label class="product-option">
                            <input type="radio" name="product_type" value="hoodie" onchange="updateProductSelection()">
                            <div class="product-option-box">
                                <span>Custom Hoodie</span>
                            </div>
                        </label>
                        <label class="product-option">
                            <input type="radio" name="product_type" value="denim" onchange="updateProductSelection()">
                            <div class="product-option-box">
                                <span>Custom Denim</span>
                            </div>
                        </label>
                        <label class="product-option">
                            <input type="radio" name="product_type" value="shirt" onchange="updateProductSelection()">
                            <div class="product-option-box">
                                <span>Custom T-Shirt</span>
                            </div>
                        </label>
                        <label class="product-option">
                            <input type="radio" name="product_type" value="tattoo" onchange="updateProductSelection()">
                            <div class="product-option-box">
                                <span>Tattoo Inspired</span>
                            </div>
                        </label>
                        <label class="product-option">
                            <input type="radio" name="product_type" value="religious" onchange="updateProductSelection()">
                            <div class="product-option-box">
                                <span>Religious Art</span>
                            </div>
                        </label>
                    </div>

                    <style>
                        .product-option {
                            cursor: pointer;
                        }
                        .product-option input[type="radio"] {
                            display: none;
                        }
                        .product-option-box {
                            display: flex;
                            flex-direction: column;
                            align-items: center;
                            justify-content: center;
                            gap: 8px;
                            padding: 20px;
                            background: rgba(0, 0, 0, 0.3);
                            border: 2px solid rgba(204, 22, 47, 0.2);
                            border-radius: 12px;
                            transition: all 0.3s ease;
                            text-align: center;
                        }
                        .product-option-box i {
                            font-size: 24px;
                            color: #888;
                            transition: all 0.3s ease;
                        }
                        .product-option-box span {
                            font-size: 13px;
                            color: #aaa;
                            font-weight: 500;
                            transition: all 0.3s ease;
                        }
                        .product-option input[type="radio"]:checked + .product-option-box {
                            background: rgba(204, 22, 47, 0.15);
                            border-color: rgba(204, 22, 47, 0.6);
                        }
                        .product-option input[type="radio"]:checked + .product-option-box i {
                            color: #cc162f;
                        }
                        .product-option input[type="radio"]:checked + .product-option-box span {
                            color: #cc162f;
                        }
                    </style>
                </div>

                <!-- Step 2: Garment Details -->
                <div class="form-section-block" style="margin-bottom: 60px;">
                    <h2 class="form-section-title">
                        <span class="step-number">2</span>
                        Detail Garment
                    </h2>
                    <p style="color: #888; margin: 12px 0 24px 0;">Tentukan ukuran, warna, dan material dasar.</p>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 20px;">
                        <!-- Garment Size -->
                        <div class="form-group">
                            <label for="garment-size" style="display: block; color: #888; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">
                                <i class="fa-solid fa-ruler"></i> Ukuran Garment *
                            </label>
                            <select name="garment_size" id="garment-size" class="form-select" required style="width: 100%; padding: 12px 16px; background: rgba(0,0,0,0.3); border: 1px solid rgba(204, 22, 47, 0.3); border-radius: 8px; color: #fff; font-size: 14px;">
                                <option value="">-- Pilih Ukuran --</option>
                                <option value="XS">Extra Small (XS)</option>
                                <option value="S">Small (S)</option>
                                <option value="M">Medium (M)</option>
                                <option value="L">Large (L)</option>
                                <option value="XL">Extra Large (XL)</option>
                                <option value="XXL">Double XL (XXL)</option>
                                <option value="custom">Custom Measurement</option>
                            </select>
                        </div>

                        <!-- Base Color -->
                        <div class="form-group">
                            <label for="base-color" style="display: block; color: #888; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">
                                <i class="fa-solid fa-palette"></i> Warna Dasar *
                            </label>
                            <input type="text" name="base_color" id="base-color" placeholder="Contoh: Black, Navy Blue, Charcoal Grey" required class="form-input" style="width: 100%; padding: 12px 16px; background: rgba(0,0,0,0.3); border: 1px solid rgba(204, 22, 47, 0.3); border-radius: 8px; color: #fff; font-size: 14px;">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="material-type" style="display: block; color: #888; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">
                            <i class="fa-solid fa-shirt"></i> Jenis Material *
                        </label>
                        <select name="material_type" id="material-type" class="form-select" required style="width: 100%; padding: 12px 16px; background: rgba(0,0,0,0.3); border: 1px solid rgba(204, 22, 47, 0.3); border-radius: 8px; color: #fff; font-size: 14px;">
                            <option value="">-- Pilih Material --</option>
                            <option value="cotton">Premium Cotton</option>
                            <option value="premium_cotton">Premium Combed Cotton</option>
                            <option value="french_terry">French Terry (500GSM)</option>
                            <option value="selvedge_denim">Japanese Selvedge Denim (15oz)</option>
                            <option value="cowhide_leather">Vintage Cowhide Leather</option>
                            <option value="linen">Organic Black Linen</option>
                            <option value="gabardine">Premium Gabardine</option>
                            <option value="blend">Cotton-Polyester Blend</option>
                            <option value="custom">Custom Material (Detail di Notes)</option>
                        </select>
                    </div>
                </div>

                <!-- Step 3: Design & Artwork -->
                <div class="form-section-block" style="margin-bottom: 60px;">
                    <h2 class="form-section-title">
                        <span class="step-number">3</span>
                        Desain & Artwork
                    </h2>
                    <p style="color: #888; margin: 12px 0 24px 0;">Jelaskan tema, penempatan, dan visi kreatif Anda.</p>

                    <div class="form-group">
                        <label for="artwork-theme" style="display: block; color: #888; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">
                            <i class="fa-solid fa-palette"></i> Tema Artwork *
                        </label>
                        <input type="text" name="artwork_theme" id="artwork-theme" placeholder="Contoh: Gothic Angel, Sacred Heart, Tribal Blade" required class="form-input" style="width: 100%; padding: 12px 16px; background: rgba(0,0,0,0.3); border: 1px solid rgba(204, 22, 47, 0.3); border-radius: 8px; color: #fff; font-size: 14px;">
                    </div>

                    <div class="form-group">
                        <label for="placement-location" style="display: block; color: #888; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">
                            <i class="fa-solid fa-location-dot"></i> Lokasi Penempatan *
                        </label>
                        <p style="color: #666; font-size: 12px; margin-bottom: 12px;">Pilih area garment untuk artwork:</p>
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 20px;">
                            <?php 
                            $placements = [
                                'front_left' => 'Front Left Chest',
                                'front_center' => 'Front Center',
                                'front_right' => 'Front Right Chest',
                                'back_full' => 'Full Back Panel',
                                'back_upper' => 'Upper Back',
                                'back_lower' => 'Lower Back',
                                'left_sleeve' => 'Left Sleeve',
                                'right_sleeve' => 'Right Sleeve',
                                'collar' => 'Collar Area',
                                'hem' => 'Hem/Cuff'
                            ];
                            ?>
                            <?php foreach ($placements as $value => $label): ?>
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 8px; background: rgba(0,0,0,0.2); border-radius: 6px; transition: all 0.3s;">
                                    <input type="checkbox" name="placement_location[]" value="<?= $value ?>" style="cursor: pointer;">
                                    <span style="color: #aaa; font-size: 12px;"><?= $label ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="design-description" style="display: block; color: #888; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">
                            <i class="fa-solid fa-pen"></i> Deskripsi Desain Detail *
                        </label>
                        <textarea name="design_description" id="design-description" placeholder="Jelaskan visi kreatif Anda secara detail. Semakin detail, semakin baik tim kami memahami konsep Anda..." required rows="6" maxlength="600" class="form-textarea" style="width: 100%; padding: 16px; background: rgba(0,0,0,0.3); border: 1px solid rgba(204, 22, 47, 0.3); border-radius: 8px; color: #fff; font-size: 14px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; resize: vertical;"></textarea>
                        <small style="display: block; color: #666; margin-top: 8px;">Minimum 50 karakter. Sertakan referensi, inspirasi, atau tautan gambar jika ada.</small>
                    </div>
                </div>

                <!-- Step 4: Budget & Timeline -->
                <div class="form-section-block" style="margin-bottom: 60px;">
                    <h2 class="form-section-title">
                        <span class="step-number">4</span>
                        Budget & Timeline
                    </h2>
                    <p style="color: #888; margin: 12px 0 24px 0;">Tentukan range budget dan target deadline Anda.</p>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                        <!-- Budget Range -->
                        <div class="form-group">
                            <label for="budget-range" style="display: block; color: #888; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">
                                <i class="fa-solid fa-wallet"></i> Range Budget *
                            </label>
                            <select name="budget_range" id="budget-range" class="form-select" required style="width: 100%; padding: 12px 16px; background: rgba(0,0,0,0.3); border: 1px solid rgba(204, 22, 47, 0.3); border-radius: 8px; color: #fff; font-size: 14px;">
                                <option value="">-- Pilih Range Budget --</option>
                                <option value="under_1jt">Dibawah Rp 1 Juta</option>
                                <option value="1jt_3jt">Rp 1 Juta - Rp 3 Juta</option>
                                <option value="3jt_6jt">Rp 3 Juta - Rp 6 Juta</option>
                                <option value="6jt_10jt">Rp 6 Juta - Rp 10 Juta</option>
                                <option value="above_10jt">Diatas Rp 10 Juta</option>
                                <option value="flexible">Fleksibel (Konsultasi dengan Tim)</option>
                            </select>
                        </div>

                        <!-- Target Deadline -->
                        <div class="form-group">
                            <label for="target-deadline" style="display: block; color: #888; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">
                                <i class="fa-solid fa-calendar"></i> Target Deadline *
                            </label>
                            <input type="date" name="target_deadline" id="target-deadline" required class="form-input" style="width: 100%; padding: 12px 16px; background: rgba(0,0,0,0.3); border: 1px solid rgba(204, 22, 47, 0.3); border-radius: 8px; color: #fff; font-size: 14px;">
                            <small style="display: block; color: #666; margin-top: 8px;">Minimal 7 hari dari hari ini</small>
                        </div>
                    </div>
                </div>

                <!-- Step 5: Additional Notes -->
                <div class="form-section-block" style="margin-bottom: 60px;">
                    <h2 class="form-section-title">
                        <span class="step-number">5</span>
                        Catatan Tambahan
                    </h2>
                    <p style="color: #888; margin: 12px 0 24px 0;">Informasi tambahan atau permintaan khusus (opsional).</p>

                    <div class="form-group">
                        <label for="notes" style="display: block; color: #888; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">
                            <i class="fa-solid fa-sticky-note"></i> Catatan
                        </label>
                        <textarea name="notes" id="notes" placeholder="Contoh: Gunakan teknik hand-painted, bukan print. Preferensi warna tinta: merah & hitam saja..." rows="4" class="form-textarea" style="width: 100%; padding: 16px; background: rgba(0,0,0,0.3); border: 1px solid rgba(204, 22, 47, 0.3); border-radius: 8px; color: #fff; font-size: 14px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; resize: vertical;"></textarea>
                    </div>
                </div>

                <!-- Form Style -->
                <style>
                    .form-section-block {
                        padding: 40px 0;
                        border-bottom: 1px solid rgba(204, 22, 47, 0.1);
                    }
                    .form-section-block:last-child {
                        border-bottom: none;
                    }
                    .form-section-title {
                        display: flex;
                        align-items: center;
                        gap: 16px;
                        margin: 0 0 12px 0;
                        font-size: 22px;
                        color: #fff;
                    }
                    .step-number {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        width: 40px;
                        height: 40px;
                        background: rgba(204, 22, 47, 0.15);
                        border: 2px solid rgba(204, 22, 47, 0.4);
                        border-radius: 50%;
                        color: #cc162f;
                        font-weight: 700;
                        font-size: 18px;
                    }
                    .form-input,
                    .form-select,
                    .form-textarea {
                        transition: all 0.3s ease;
                    }
                    .form-input:focus,
                    .form-select:focus,
                    .form-textarea:focus {
                        outline: none;
                        background: rgba(0,0,0,0.5);
                        border-color: rgba(204, 22, 47, 0.6);
                        box-shadow: 0 0 20px rgba(204, 22, 47, 0.15);
                    }
                    .form-input::placeholder,
                    .form-textarea::placeholder {
                        color: #666;
                    }
                </style>

                <!-- Submit Button -->
                <div style="padding: 40px 0; text-align: center;">
                    <button type="submit" class="btn-luxury btn-luxury-solid" style="padding: 16px 48px; font-size: 16px; display: inline-block;">
                        <i class="fa-solid fa-paper-plane"></i> Kirim Pesanan Custom
                    </button>
                    <a href="<?= base_url('user/dashboard') ?>" class="btn-luxury btn-luxury-outline" style="margin-left: 16px; padding: 16px 48px; font-size: 16px; display: inline-block;">
                        Kembali ke Dashboard
                    </a>
                </div>
            </form>

        </div>
    </section>

</div>

<script>
// Function to update product selection styling
function updateProductSelection() {
    const productOptions = document.querySelectorAll('input[name="product_type"]');
    productOptions.forEach(option => {
        const box = option.nextElementSibling;
        if (option.checked) {
            if (box) box.style.borderColor = '#cc162f';
            if (box) box.style.backgroundColor = 'rgba(204, 22, 47, 0.05)';
        } else {
            if (box) box.style.borderColor = 'rgba(204, 22, 47, 0.3)';
            if (box) box.style.backgroundColor = 'transparent';
        }
    });
}

// Initialize product selection on page load
document.addEventListener('DOMContentLoaded', function() {
    updateProductSelection();
});

// Add change listeners to all radio buttons
document.querySelectorAll('input[name="product_type"]').forEach(radio => {
    radio.addEventListener('change', updateProductSelection);
});

    const commissionForm = document.getElementById('commission-form-element');
    if (commissionForm) {
        commissionForm.addEventListener('submit', function (event) {
            event.preventDefault();
            const formData = new FormData(commissionForm);

            fetch(commissionForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const confirmed = window.confirm(`${data.message}\n\nTekan OK untuk lanjutkan ke WhatsApp dan konfirmasi pesanan Anda.`);
                    if (confirmed && data.whatsapp_url) {
                        window.open(data.whatsapp_url, '_blank');
                    }
                    window.location.href = '<?= base_url('user/history') ?>';
                } else {
                    alert(data.message || 'Terjadi kesalahan saat membuat pesanan. Silakan coba lagi.');
                }
            })
            .catch(() => {
                alert('Terjadi kesalahan jaringan. Silakan coba lagi.');
            });
        });
    }

        </script>

        <?= $this->endSection() ?>

