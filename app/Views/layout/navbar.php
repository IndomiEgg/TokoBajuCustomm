<header id="site-header">
    <div class="nav-container">
        <?php if (session()->get('is_admin_logged_in')) : ?>
            <a href="<?= base_url('admin/dashboard') ?>" class="logo">BaTom<span>1:1</span></a>
            
            <ul class="nav-menu" id="nav-dropdown-menu">
                <li><a href="<?= base_url('admin/dashboard') ?>" class="nav-link active">Dashboard</a></li>
            </ul>
        <?php else: ?>
            <a href="<?= base_url('/') ?>" class="logo">BaTom<span>1:1</span></a>
            
            <ul class="nav-menu" id="nav-dropdown-menu">
                <?php if (session()->get('is_logged_in')) : ?>
                    <li><a href="<?= base_url('user/dashboard') ?>" class="nav-link active">Dashboard</a></li>
                    <li><a href="<?= base_url('user/history') ?>" class="nav-link">History</a></li>
                    <li><a href="<?= base_url('user/commission-form') ?>" class="nav-link">Custom Design</a></li>
                    <li><a href="<?= base_url('cart') ?>" class="nav-link">Cart</a></li>
                <?php else: ?>
                    <li><a href="<?= base_url('/') ?>#home" class="nav-link active">Home</a></li>
                    <li><a href="<?= base_url('/') ?>#gallery" class="nav-link">Gallery</a></li>
                    <li><a href="<?= base_url('/') ?>#pricing" class="nav-link">Pricing Guide</a></li>
                    <li><a href="<?= base_url('/') ?>#process" class="nav-link">Process</a></li>
                    <li><a href="<?= base_url('shop/custom') ?>" class="nav-link">Custom Design</a></li>
                    <li><a href="<?= base_url('/') ?>#about" class="nav-link">About</a></li>
                    <li><a href="<?= base_url('/') ?>#contact" class="nav-link">Contact</a></li>
                <?php endif; ?>
            </ul>
        <?php endif; ?>

        <div class="nav-utilities">
            <?php if (session()->get('is_admin_logged_in')) : ?>
                <a href="<?= base_url('admin/logout') ?>" class="icon-nav-btn logout-icon" title="Logout Admin">
                    <i class="fa-solid fa-power-off"></i>
                </a>
            <?php else: ?>
                <a href="<?= session()->get('is_logged_in') ? base_url('user/commission-form') : base_url('shop/custom') ?>" class="btn-commission-link">
                    <i class="fa-solid fa-compass-drafting"></i> Commission
                </a>
                
                <a href="<?= base_url('cart') ?>" class="icon-nav-btn nav-cart-badge-container" aria-label="View Active Cart">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span class="nav-cart-badge" id="navigation-cart-badge">0</span>
                </a>

                <?php if (session()->get('is_logged_in')) : ?>
                    <a href="<?= base_url('user/dashboard') ?>" title="Dashboard">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=80&q=80" alt="Profile Avatar" class="nav-avatar">
                    </a>

                    <a href="<?= base_url('auth/logout') ?>" class="icon-nav-btn logout-icon" title="Logout">
                        <i class="fa-solid fa-power-off"></i>
                    </a>
                <?php else: ?>
                    <a href="<?= base_url('login') ?>">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=80&q=80" alt="Collector Profile Avatar" class="nav-avatar" title="Collector Vault">
                    </a>
                <?php endif; ?>
            <?php endif; ?>
            
            <div class="hamburger-btn" id="mobile-hamburger-trigger" aria-label="Toggle Menu">
                <i class="fa-solid fa-bars-staggered"></i>
            </div>
        </div>
    </div>
</header>