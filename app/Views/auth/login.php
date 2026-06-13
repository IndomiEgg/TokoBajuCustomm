<?= $this->extend('layout/template') ?>

<?= $this->section('styles') ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/auth.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

    <div class="auth-container">
        
        <!-- ==================== LEFT PANEL: BRAND SHOWCASE ==================== -->
        <section class="brand-showcase">
            <div class="showcase-bg-image"></div>
            <div class="smoke-overlay"></div>
            
            <!-- Canvas drawing delicate floating embers / tattoo smoke -->
            <canvas id="smoke-canvas"></canvas>
            
            <div class="tattoo-corner-decor"></div>
            
            <div class="showcase-content">
                
                <!-- Tattoo Fine Line Art SVG (Top) -->
                <div class="tattoo-divider-top">
                    <svg viewBox="0 0 100 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0,10 Q25,18 50,10 T100,10 M20,10 Q50,0 80,10" stroke="#8c0d0d" stroke-width="0.75" fill="none" />
                        <polygon points="50,4 53,10 50,16 47,10" fill="#b30f0f" />
                    </svg>
                </div>

                <div class="brand-branding">
                    <h1 class="editorial-brand-name">BATOM<span>1:1</span></h1>
                    <h2 class="editorial-tagline">"Created Once. Owned Forever."</h2>
                </div>
                
                <p class="showcase-desc">
                    Exclusive handmade custom fashion and wearable art designed to express identity without limits. Complete your registration to register designs, consult privately, and secure your personal archival commissions.
                </p>

                <div class="badge-exclusive">Private Atelier Access</div>

                <!-- Tattoo Fine Line Art SVG (Bottom) -->
                <div class="tattoo-divider-bottom">
                    <svg viewBox="0 0 100 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0,10 Q25,18 50,10 T100,10 M20,10 Q50,0 80,10" stroke="#8c0d0d" stroke-width="0.75" fill="none" />
                        <polygon points="50,4 53,10 50,16 47,10" fill="#b30f0f" />
                    </svg>
                </div>

            </div>
        </section>

        <!-- ==================== RIGHT PANEL: CUSTOMER PORTAL INTERACTION ==================== -->
        <section class="auth-panel">
            <div class="auth-panel-wrapper">
                
                <!-- Mobile Branding (Hidden on Desktop) -->
                <div class="mobile-branding-header">
                    <h1 class="mobile-brand-title">BATOM<span>1:1</span></h1>
                </div>

                <!-- Tab Switching Panel Navigation -->
                <div class="auth-tabs-navigation">
                    <button class="tab-btn active" data-tab-id="signin-pane">
                        <span>Sign In</span>
                        <div class="tab-indicator-bar"></div>
                    </button>
                    <button class="tab-btn" data-tab-id="signup-pane">
                        <span>Create Account</span>
                        <div class="tab-indicator-bar"></div>
                    </button>
                </div>

                <!-- Panes Viewport -->
                <div class="auth-pane-viewport">
                    
                    <!-- Pane: SIGN IN FORM -->
                    <div class="form-pane-item active" id="signin-pane">
                        <form id="signin-interactive-form" action="<?= base_url('auth/login') ?>" method="POST" autocomplete="off">
                            <?= csrf_field() ?>
                            
                            <div class="form-stack-grid">
                                
                                <div class="form-group-relative">
                                    <input type="email" id="signin-email" name="email" class="input-luxury-field" placeholder=" " required>
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

                            <div class="form-utility-row">
                                <label class="checkbox-luxury-label">
                                    <input type="checkbox" id="signin-remember" name="remember_me">
                                    <span class="checkbox-luxury-box"></span>
                                    <span class="checkbox-label-text">Remember device</span>
                                </label>
                                <a href="<?= base_url('auth/forgot-password') ?>" class="forgot-pass-anchor" id="forgot-password-trigger">Forgot Password?</a>
                            </div>

                            <!-- Display Flash Messages -->
                            <?php if (session()->getFlashdata('error')): ?>
                                <div class="alert alert-error">
                                    <i class="fa-solid fa-exclamation-circle"></i>
                                    <?= session()->getFlashdata('error') ?>
                                </div>
                            <?php endif; ?>

                            <button type="submit" class="btn-luxury-submit">
                                <span class="btn-submit-text">Sign In</span>
                                <span class="btn-shimmer-sweep"></span>
                            </button>
                        </form>
                    </div>

                    <!-- Pane: CREATE ACCOUNT FORM -->
                    <div class="form-pane-item" id="signup-pane">
                        <form id="signup-interactive-form" action="<?= base_url('auth/register') ?>" method="POST" autocomplete="off">
                            <?= csrf_field() ?>
                            
                            <div class="form-stack-grid">
                                
                                <div class="form-row-double">
                                    <div class="form-group-relative">
                                        <input type="text" id="signup-name" name="full_name" class="input-luxury-field" placeholder=" " required>
                                        <label for="signup-name" class="label-luxury-floating">Full Name</label>
                                        <span class="input-bottom-border-grow"></span>
                                    </div>
                                    <div class="form-group-relative">
                                        <input type="text" id="signup-username" name="username" class="input-luxury-field" placeholder=" " required>
                                        <label for="signup-username" class="label-luxury-floating">Username</label>
                                        <span class="input-bottom-border-grow"></span>
                                    </div>
                                </div>

                                <div class="form-group-relative">
                                    <input type="email" id="signup-email" name="email" class="input-luxury-field" placeholder=" " required>
                                    <label for="signup-email" class="label-luxury-floating">Email Address</label>
                                    <span class="input-bottom-border-grow"></span>
                                </div>

                                <div class="form-group-relative">
                                    <input type="tel" id="signup-phone" name="phone_number" class="input-luxury-field" placeholder=" " required>
                                    <label for="signup-phone" class="label-luxury-floating">Phone Number (WhatsApp)</label>
                                    <span class="input-bottom-border-grow"></span>
                                </div>

                                <div class="form-row-double">
                                    <div class="form-group-relative">
                                        <input type="password" id="signup-password" name="password" class="input-luxury-field" placeholder=" " required>
                                        <label for="signup-password" class="label-luxury-floating">Password</label>
                                        <span class="input-bottom-border-grow"></span>
                                        <button type="button" class="eye-toggle-btn" aria-label="Toggle password view state">
                                            <i class="fa-regular fa-eye-slash"></i>
                                        </button>
                                    </div>
                                    <div class="form-group-relative">
                                        <input type="password" id="signup-confirm" name="password_confirm" class="input-luxury-field" placeholder=" " required>
                                        <label for="signup-confirm" class="label-luxury-floating">Confirm Pass</label>
                                        <span class="input-bottom-border-grow"></span>
                                        <button type="button" class="eye-toggle-btn" aria-label="Toggle password view state">
                                            <i class="fa-regular fa-eye-slash"></i>
                                        </button>
                                    </div>
                                </div>

                            </div>

                            <!-- Display Flash Messages -->
                            <?php if (session()->getFlashdata('register_error')): ?>
                                <div class="alert alert-error">
                                    <i class="fa-solid fa-exclamation-circle"></i>
                                    <?= session()->getFlashdata('register_error') ?>
                                </div>
                            <?php endif; ?>

                            <div class="form-utility-row">
                                <label class="checkbox-luxury-label">
                                    <input type="checkbox" id="signup-agree-rules" name="agree_terms" required>
                                    <span class="checkbox-luxury-box"></span>
                                    <span class="checkbox-label-text">I agree to the <a href="#" id="terms-rules-anchor">Terms & Conditions</a></span>
                                </label>
                            </div>

                            <button type="submit" class="btn-luxury-submit">
                                <span class="btn-submit-text">Create Account</span>
                                <span class="btn-shimmer-sweep"></span>
                            </button>
                        </form>
                    </div>

                </div>

                <!-- Privileges / Benefits Showcase -->
                <div class="privileges-container">
                    <h3 class="privileges-heading">Collector Benefits</h3>
                    <div class="privileges-grid">
                        
                        <div class="privilege-card">
                            <div class="privilege-icon-box">
                                <i class="fa-solid fa-compass-drafting"></i>
                            </div>
                            <div class="privilege-text-content">
                                <h4>Track Custom Commissions</h4>
                                <p>Monitor real-time progress steps of your customized wearable art pieces directly from the atelier.</p>
                            </div>
                        </div>

                        <div class="privilege-card">
                            <div class="privilege-icon-box">
                                <i class="fa-solid fa-bookmark"></i>
                            </div>
                            <div class="privilege-text-content">
                                <h4>Archive Design References</h4>
                                <p>Bookmark favorite concepts, upload design templates, and draft your customized request sheets safely.</p>
                            </div>
                        </div>

                        <div class="privilege-card">
                            <div class="privilege-icon-box">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                            </div>
                            <div class="privilege-text-content">
                                <h4>Order & Private Delivery Logs</h4>
                                <p>Fast checkout processing, priority waitlists, and high-security courier dispatch logs.</p>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Security Encrypted Guarantee Notice -->
                <div class="security-notice-panel">
                    <div class="security-notice-icon">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div class="security-notice-desc">
                        Your information is securely protected and encrypted. We maintain zero-knowledge storage configurations for your uploads, drafts, and designs.
                    </div>
                </div>

                <!-- Custom Elegant Decorative Footer Panel -->
                <footer class="panel-footer-area">
                    <div class="tattoo-footer-divider">
                        <svg viewBox="0 0 120 10" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0,5 L45,5 Q50,0 55,5 T65,5 L120,5" stroke="rgba(255,255,255,0.05)" stroke-width="0.5" fill="none" />
                            <path d="M48,5 Q60,10 72,5" stroke="rgba(140, 13, 13, 0.3)" stroke-width="0.5" fill="none" />
                            <polygon points="60,2 62,5 60,8 58,5" fill="#8c0d0d" />
                        </svg>
                    </div>
                    <p class="copyright-tag">&copy; 2026 Batom Studio. All Rights Reserved.</p>
                    <p class="slogan-tag">Created Once. Owned Forever.</p>
                </footer>

            </div>
        </section>

    </div>

    <!-- Custom Toast Alerts Overlay Container -->
    <div class="toast-banner" id="atelier-toast-portal">
        <div class="toast-side-line"></div>
        <div class="toast-header-info">
            <h4 id="toast-title">AESTHETIC PORTAL</h4>
            <p id="toast-description">Authenticating cryptographic session detail...</p>
        </div>
    </div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script src="<?= base_url('assets/js/auth.js') ?>"></script>
<?= $this->endSection() ?>