<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<div class="edit-profile-container" style="max-width: 900px; margin: 0 auto; padding: 40px 0;">
    <section class="section-title-block" style="text-align: center; margin-bottom: 40px;">
        <span class="section-accent-tag">Pengaturan Profil</span>
        <h1 class="section-heading">Edit Profil Anda</h1>
        <p class="section-subtitle">Perbarui nama, email, nomor telepon, alamat, dan foto profil Anda.</p>
    </section>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success" style="margin-bottom: 24px; padding: 18px; background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); border-radius: 12px; color: #22c55e;">
            <i class="fa-solid fa-check-circle"></i>
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-error" style="margin-bottom: 24px; padding: 18px; background: rgba(220, 38, 38, 0.08); border: 1px solid rgba(220, 38, 38, 0.2); border-radius: 12px; color: #f87171;">
            <i class="fa-solid fa-exclamation-circle"></i>
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= base_url('user/update-profile') ?>" enctype="multipart/form-data" style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); border: 1px solid rgba(204, 22, 47, 0.3); border-radius: 18px; padding: 32px;">
        <?= csrf_field() ?>

        <div style="display: grid; grid-template-columns: 180px 1fr; gap: 32px; align-items: start; margin-bottom: 32px;">
            <div style="text-align: center;">
                <div style="width: 180px; height: 180px; display: flex; align-items: center; justify-content: center; background: rgba(204, 22, 47, 0.08); border: 2px solid rgba(204, 22, 47, 0.25); border-radius: 20px;">
                    <i class="fa-solid fa-user" style="font-size: 56px; color: #cc162f; opacity: 0.6;"></i>
                </div>
                <p style="margin-top: 12px; color: #888; font-size: 13px;">Fitur upload foto profil dinonaktifkan untuk menyederhanakan pengalaman.</p>
            </div>

            <div style="display: grid; gap: 20px;">
                <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px;">
                    <div>
                        <label style="display:block; color:#888; font-size:12px; text-transform:uppercase; letter-spacing:1px; margin-bottom:8px;">Nama Lengkap</label>
                        <input type="text" name="full_name" value="<?= esc(old('full_name', $user['full_name'] ?? '')) ?>" required style="width:100%; padding:14px 16px; border-radius:12px; border:1px solid rgba(204,22,47,0.2); background:rgba(0,0,0,0.3); color:#fff;">
                    </div>
                    <div>
                        <label style="display:block; color:#888; font-size:12px; text-transform:uppercase; letter-spacing:1px; margin-bottom:8px;">Email</label>
                        <input type="email" name="email" value="<?= esc(old('email', $user['email'] ?? '')) ?>" required style="width:100%; padding:14px 16px; border-radius:12px; border:1px solid rgba(204,22,47,0.2); background:rgba(0,0,0,0.3); color:#fff;">
                    </div>
                </div>

                <div>
                    <label style="display:block; color:#888; font-size:12px; text-transform:uppercase; letter-spacing:1px; margin-bottom:8px;">Nomor WhatsApp</label>
                    <input type="text" name="phone_number" value="<?= esc(old('phone_number', $user['phone_number'] ?? '')) ?>" placeholder="Contoh: 08123456789" style="width:100%; padding:14px 16px; border-radius:12px; border:1px solid rgba(204,22,47,0.2); background:rgba(0,0,0,0.3); color:#fff;">
                </div>

                <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px;">
                    <div>
                        <label style="display:block; color:#888; font-size:12px; text-transform:uppercase; letter-spacing:1px; margin-bottom:8px;">Kota</label>
                        <input type="text" name="city" value="<?= esc(old('city', $user['city'] ?? '')) ?>" style="width:100%; padding:14px 16px; border-radius:12px; border:1px solid rgba(204,22,47,0.2); background:rgba(0,0,0,0.3); color:#fff;">
                    </div>
                    <div>
                        <label style="display:block; color:#888; font-size:12px; text-transform:uppercase; letter-spacing:1px; margin-bottom:8px;">Kode Pos</label>
                        <input type="text" name="postal_code" value="<?= esc(old('postal_code', $user['postal_code'] ?? '')) ?>" style="width:100%; padding:14px 16px; border-radius:12px; border:1px solid rgba(204,22,47,0.2); background:rgba(0,0,0,0.3); color:#fff;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label style="display:block; color:#888; font-size:12px; text-transform:uppercase; letter-spacing:1px; margin-bottom:8px;">Negara</label>
                        <input type="text" name="country" value="<?= esc(old('country', $user['country'] ?? '')) ?>" style="width:100%; padding:14px 16px; border-radius:12px; border:1px solid rgba(204,22,47,0.2); background:rgba(0,0,0,0.3); color:#fff;">
                    </div>
                    <div>
                        <label style="display:block; color:#888; font-size:12px; text-transform:uppercase; letter-spacing:1px; margin-bottom:8px;">Alamat</label>
                        <input type="text" name="address" value="<?= esc(old('address', $user['address'] ?? '')) ?>" style="width:100%; padding:14px 16px; border-radius:12px; border:1px solid rgba(204,22,47,0.2); background:rgba(0,0,0,0.3); color:#fff;">
                    </div>
                </div>

                <div style="display:flex; gap: 16px; flex-wrap: wrap; margin-top: 16px;">
                    <button type="submit" class="btn-luxury btn-luxury-solid" style="padding: 16px 32px;">Simpan Perubahan</button>
                    <a href="<?= base_url('user/dashboard') ?>" class="btn-luxury btn-luxury-outline" style="padding: 16px 32px;">Kembali ke Dashboard</a>
                </div>
            </div>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
