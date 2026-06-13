<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token-name" content="<?= csrf_token() ?>">
    <meta name="csrf-token-value" content="<?= csrf_hash() ?>">
    <title><?= $title ?? 'BATOM — One of One Wearable Art' ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700;800;900&family=Montserrat:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <?= $this->renderSection('styles') ?>
</head>
<body class="bg-black text-white">

    <!-- Preloader and ambient spotlight (required by inline scripts) -->
    <div id="preloader">
        <div class="loader-logo">BATOM</div>
        <div class="loader-line"></div>
        <div class="loader-tagline">Created Once. Owned Forever.</div>
    </div>

    <div id="laser-glow-pointer" class="laser-spotlight" aria-hidden="true"></div>

    <?= $this->include('layout/navbar') ?>

    <main>
        <?= $this->renderSection('content') ?>
    </main>

    <?= $this->include('layout/footer') ?>

    <!-- Global Gallery Modal (available on pages with .masonry-item) -->
    <div class="modal-backdrop" id="artwork-modal">
        <button class="modal-close-trigger" id="modal-close-btn" aria-label="Close modal"><i class="fa-solid fa-xmark"></i></button>
        <div class="modal-inner-card">
            <div class="modal-visual">
                <img src="" id="modal-img-source" alt="Portfolio Highlight">
            </div>
            <div class="modal-details-sheet">
                <span class="modal-tag" id="modal-tag-type">Custom</span>
                <h3 class="modal-title" id="modal-title-text">PROJECT NAME</h3>
                <p class="modal-narrative" id="modal-narrative-text"></p>
                <ul class="modal-specs-list">
                    <li><span>Project Nature</span><span>One-of-One</span></li>
                    <li><span>Year Compiled</span><span id="modal-spec-year">2026</span></li>
                    <li><span>Pigment Integrity</span><span>Textile Ink</span></li>
                </ul>
                    <button class="btn-luxury btn-luxury-solid w-full" id="modal-action-commission">Commission Similar Project</button>
                    <button class="btn-luxury btn-luxury-outline w-full" id="modal-action-addcart" style="margin-top:12px;">Add to Cart</button>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            const preloader = document.getElementById('preloader');
            if (!preloader) return;

            preloader.style.opacity = '0';
            preloader.style.visibility = 'hidden';
            setTimeout(function() {
                if (preloader.parentNode) {
                    preloader.parentNode.removeChild(preloader);
                }
            }, 800);
        });
    </script>

    <script>
    (function(){
        function initPortfolioFilterTabs(){
            const root = document.getElementById('portfolio-gallery');
            if(!root) return;
            const tabs = root.querySelectorAll('.filter-tab');
            const items = root.querySelectorAll('.masonry-item');
            tabs.forEach(t => t.addEventListener('click', function(){
                tabs.forEach(x => x.classList.remove('active'));
                this.classList.add('active');
                const f = this.dataset.filter;
                items.forEach(it => {
                    if(f === 'all' || it.dataset.category === f) it.style.display = '';
                    else it.style.display = 'none';
                });
            }));
        }

        function initPortfolioModals(){
            const root = document.getElementById('portfolio-gallery');
            if(!root) return;
            const modal = document.getElementById('artwork-modal');
            if(!modal) return;
            const modalImg = document.getElementById('modal-img-source');
            const modalTag = document.getElementById('modal-tag-type');
            const modalTitle = document.getElementById('modal-title-text');
            const modalNarr = document.getElementById('modal-narrative-text');
            const modalYear = document.getElementById('modal-spec-year');
            const closeBtn = document.getElementById('modal-close-btn');
            const actionBtn = document.getElementById('modal-action-commission');

            const masonryItems = root.querySelectorAll('.masonry-item');
            masonryItems.forEach(card => {
                const overlay = card.querySelector('.masonry-item-overlay');
                if(overlay) overlay.style.pointerEvents = 'none';
                card.addEventListener('click', function(e){
                    const img = this.querySelector('img');
                    const titleEl = this.querySelector('.item-overlay-title');
                    const tagEl = this.querySelector('.item-overlay-tag');
                    const src = this.dataset.img || (img ? img.src : '') || '';
                    const title = this.dataset.title || (titleEl ? titleEl.textContent : '') || '';
                    const desc = this.dataset.desc || '';
                    const year = this.dataset.year || '';
                    const tag = tagEl ? tagEl.textContent : '';
                    if(modalImg && src) modalImg.src = src;
                    if(modalTitle) modalTitle.textContent = title;
                    if(modalNarr) modalNarr.textContent = desc;
                    if(modalYear) modalYear.textContent = year;
                    if(modalTag) modalTag.textContent = tag;
                    modal.classList.add('active');
                });
            });

            if(closeBtn) closeBtn.addEventListener('click', () => modal.classList.remove('active'));
            modal.addEventListener('click', (e) => { if(e.target === modal) modal.classList.remove('active'); });
            if(actionBtn) actionBtn.addEventListener('click', function(){ window.location.href = '<?= base_url('user/commission-form') ?>'; });
        }

        document.addEventListener('DOMContentLoaded', function(){
            initPortfolioFilterTabs();
            initPortfolioModals();
        });
    })();
    </script>

    <script>
        window.USER_PHONE_NUMBER = "<?= esc(session()->get('phone_number') ?? '') ?>";
    </script>
    <?= $this->renderSection('scripts') ?>

</body>
