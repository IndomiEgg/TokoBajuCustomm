<?= $this->extend('layout/template') ?>

<?= $this->section('styles') ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/auth.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="auth-container">
    <section class="brand-showcase">
        <div class="showcase-bg-image"></div>
        <div class="smoke-overlay"></div>
        <canvas id="smoke-canvas"></canvas>

        <div class="showcase-content">
            <div class="tattoo-divider-top">
                <svg viewBox="0 0 100 20" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0,10 Q25,18 50,10 T100,10 M20,10 Q50,0 80,10" stroke="#8c0d0d" stroke-width="0.75" fill="none" />
                    <polygon points="50,4 53,10 50,16 47,10" fill="#b30f0f" />
                </svg>
            </div>

            <div class="brand-branding">
                <h1 class="editorial-brand-name">BATOM<span>ADMIN</span></h1>
                <h2 class="editorial-tagline">Private Atelier Control Panel</h2>
            </div>

            <p class="showcase-desc">
                Login with your admin credentials to manage orders, reports, and analytics.
            </p>

            <div class="badge-exclusive">Administrator Access</div>

            <div class="tattoo-divider-bottom">
                <svg viewBox="0 0 100 20" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0,10 Q25,18 50,10 T100,10 M20,10 Q50,0 80,10" stroke="#8c0d0d" stroke-width="0.75" fill="none" />
                    <polygon points="50,4 53,10 50,16 47,10" fill="#b30f0f" />
                </svg>
            </div>
        </div>
    </section>

    <section class="auth-panel">
        <div class="auth-panel-wrapper">
            <div class="mobile-branding-header">
                <h1 class="mobile-brand-title">BATOM<span>ADMIN</span></h1>
            </div>

            <div class="auth-pane-viewport">
                <div class="form-pane-item active" id="signin-pane">
                    <form id="signin-interactive-form" action="<?= base_url('admin/login') ?>" method="POST" autocomplete="off">
                        <?= csrf_field() ?>

                        <div class="form-stack-grid">
                            <div class="form-group-relative">
                                <input type="email" id="signin-email" name="email" class="input-luxury-field" placeholder=" " required autofocus>
                                <label for="signin-email" class="label-luxury-floating">Email Address</label>
                                <span class="input-bottom-border-grow"></span>
                            </div>

                            <div class="form-group-relative">
                                <input type="password" id="signin-password" name="password" class="input-luxury-field" placeholder=" " required>
                                <label for="signin-password" class="label-luxury-floating">Password</label>
                                <span class="input-bottom-border-grow"></span>
                                <button type="button" class="eye-toggle-btn" aria-label="Toggle password view state">
                                    <i class="fa-regular fa-eye-slash"></i>
                                </button>
                            </div>
                        </div>

                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-error">
                                <i class="fa-solid fa-exclamation-circle"></i>
                                <?= session()->getFlashdata('error') ?>
                            </div>
                        <?php endif; ?>
                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success">
                                <i class="fa-solid fa-circle-check"></i>
                                <?= session()->getFlashdata('success') ?>
                            </div>
                        <?php endif; ?>

                        <div class="form-utility-row">
                            <div></div>
                            <a href="<?= base_url('admin/forgot-password') ?>" class="forgot-pass-anchor">Forgot Password?</a>
                        </div>

                        <button type="submit" class="btn-luxury-submit">
                            <span class="btn-submit-text">Admin Login</span>
                            <span class="btn-shimmer-sweep"></span>
                        </button>
                    </form>
                </div>
            </div>

            <div class="security-notice-panel">
                <div class="security-notice-icon">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div class="security-notice-desc">
                    Access only for authorized administrators. All activity is monitored and audited.
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script src="<?= base_url('assets/js/auth.js') ?>"></script>
<?= $this->endSection() ?>
