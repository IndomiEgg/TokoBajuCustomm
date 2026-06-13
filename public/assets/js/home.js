/* ==========================================================================
           MOCK DATABASES FOR APP STATE MANAGEMENTS
           ========================================================================== */
const RECOMMENDATIONS_DB = [
  {
    id: "rec-item-01",
    name: "Ascension Gothic Trench Coat",
    category: "Limited Edition",
    price: 7800000,
    badge: "Rare Artifact",
    image:
      "https://images.unsplash.com/photo-1544022613-e87ca75a784a?auto=format&fit=crop&w=300&q=80",
  },
  {
    id: "rec-item-02",
    name: "Abyssal Roses Distressed Mohair",
    category: "Knitwear",
    price: 3100000,
    badge: "One of One",
    image:
      "https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&w=300&q=80",
  },
  {
    id: "rec-item-03",
    name: "Omen Iron Chain Heavy Vest",
    category: "Custom Denim",
    price: 2600000,
    badge: "Restock Item",
    image:
      "https://images.unsplash.com/photo-1516257984-b1b4d707412e?auto=format&fit=crop&w=300&q=80",
  },
  {
    id: "rec-item-04",
    name: "Tattoo Archival Linen Overshirt",
    category: "Painted Artwear",
    price: 1850000,
    badge: "1:1 Artwork",
    image:
      "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='400'%3E%3Crect width='300' height='400' fill='%23000000'/%3E%3Ctext x='50%25' y='45%25' fill='%23ffffff' font-family='Arial,sans-serif' font-size='24' text-anchor='middle' dominant-baseline='middle'%3ETattoo%20Art%3C/text%3E%3Ctext x='50%25' y='60%25' fill='%23ffffff' font-family='Arial,sans-serif' font-size='16' text-anchor='middle' dominant-baseline='middle'%3EPreview%3C/text%3E%3C/svg%3E",
  },
];

let cartArray = JSON.parse(localStorage.getItem("batom_cart")) || [];
let savedArray = JSON.parse(localStorage.getItem("batom_saved")) || [];
let selectedPortfolioItem = null;
let latestCheckoutOrder = null;

/* ==========================================================================
           LIFECYCLE INITIALIZER & SPA ROUTING
           ========================================================================== */
document.addEventListener("DOMContentLoaded", () => {
  // Remove Loader screen after initial delays
  setTimeout(() => {
    const preloader = document.getElementById("preloader");
    if (preloader) {
      preloader.style.opacity = "0";
      preloader.style.visibility = "hidden";
      setTimeout(() => preloader.remove(), 1000);
    }
  }, 1800);

  // Ambient graphics activation loops
  initHeroEmbersCanvas();
  initAuthEmbersCanvas();
  initMouseSpotlightTracker();
  initHeaderScrollBehavior();
  initMobileMenuDrawer();

  // Portfolio controls
  initPortfolioFilterTabs();
  initPortfolioModals();

  // Form configurators & submissions
  initCustomFormConfigurator();
  initContactFormSubmission();
  initClientAuthForms();

  // Cart render loop initially
  renderCartLedger();
  renderSavedForLaterGrid();
  renderRecommendationsCrossSells();
  initCheckoutSecureAction();
  initReceiptDownloadAction();
  initScrollRevealObserver();
  handleHashNavigation();
  window.addEventListener("hashchange", handleHashNavigation);
});

function handleHashNavigation() {
  const pathName = window.location.pathname.toLowerCase();
  if (
    /\bcart\/?$/.test(pathName) ||
    /(^|\/)index\.php\/cart\/?$/.test(pathName)
  ) {
    switchView("cart");
    return;
  }

  const rawHash = window.location.hash.replace("#", "");
  if (!rawHash) {
    switchView("landing");
    return;
  }

  if (rawHash === "view-cart" || rawHash === "cart") {
    switchView("cart");
    return;
  }

  if (rawHash === "view-auth" || rawHash === "auth") {
    switchView("auth");
    return;
  }

  const landingHashes = [
    "home",
    "gallery",
    "pricing",
    "process",
    "custom",
    "about",
    "contact",
  ];

  if (landingHashes.includes(rawHash)) {
    switchView("landing");
    const section = document.getElementById(rawHash);
    if (section) {
      setTimeout(() => {
        section.scrollIntoView({ behavior: "smooth" });
      }, 50);
    }
    return;
  }

  switchView("landing");
}

function switchView(viewName) {
  document.querySelectorAll(".app-view-section").forEach((view) => {
    view.classList.remove("active");
  });

  const target = document.getElementById(`view-${viewName}`);
  if (target) {
    target.classList.add("active");
    if (viewName !== "landing") {
      target.scrollIntoView({ behavior: "smooth" });
    }
  }
}

/**
 * Dynamic sticky glass navbar shifts on vertical scrolls
 */
function initHeaderScrollBehavior() {
  const header = document.getElementById("site-header");
  window.addEventListener("scroll", () => {
    if (window.scrollY > 40) {
      header.classList.add("scrolled");
    } else {
      header.classList.remove("scrolled");
    }
  });
}

/**
 * Collapses hamburger navigations on tablets/mobile viewports
 */
function initMobileMenuDrawer() {
  const trigger = document.getElementById("mobile-hamburger-trigger");
  const menu = document.getElementById("nav-dropdown-menu");

  trigger.addEventListener("click", () => {
    menu.classList.toggle("active");
    const icon = trigger.querySelector("i");
    if (menu.classList.contains("active")) {
      icon.className = "fa-solid fa-xmark";
    } else {
      icon.className = "fa-solid fa-bars-staggered";
    }
  });

  // Collapse if clicking anywhere
  document.querySelectorAll(".nav-link").forEach((link) => {
    link.addEventListener("click", () => {
      menu.classList.remove("active");
      trigger.querySelector("i").className = "fa-solid fa-bars-staggered";
    });
  });
}

/**
 * Interpolates pointer coordinates with damping multiplier (Desktop cursor spotlight follow)
 */
function initMouseSpotlightTracker() {
  const spotlight = document.getElementById("laser-glow-pointer");
  let mouseX = 0,
    mouseY = 0;
  let targetX = 0,
    targetY = 0;

  window.addEventListener("mousemove", (e) => {
    mouseX = e.clientX;
    mouseY = e.clientY;
  });

  function renderLoop() {
    const dampingFactor = 0.08;
    targetX += (mouseX - targetX) * dampingFactor;
    targetY += (mouseY - targetY) * dampingFactor;

    spotlight.style.transform = `translate(${targetX}px, ${targetY}px)`;
    requestAnimationFrame(renderLoop);
  }
  renderLoop();
}

/* ==========================================================================
           HTML5 CANVAS ATMOSPHERIC RENDERERS (SMOKE & EMBERS)
           ========================================================================== */
function setupDriftingEmbersSystem(canvasId) {
  const canvas = document.getElementById(canvasId);
  if (!canvas) return;

  const ctx = canvas.getContext("2d");
  let particles = [];

  function recomputeBounds() {
    canvas.width = canvas.parentElement.offsetWidth;
    canvas.height = canvas.parentElement.offsetHeight;
  }
  recomputeBounds();
  window.addEventListener("resize", recomputeBounds);

  class AshEmber {
    constructor() {
      this.reset(true);
    }

    reset(initialPopulation = false) {
      this.x = Math.random() * canvas.width;
      this.y = initialPopulation
        ? Math.random() * canvas.height
        : canvas.height + 15;
      this.size = Math.random() * 2 + 0.4;
      this.speedX = Math.random() * 0.2 - 0.1;
      this.speedY = Math.random() * -0.4 - 0.1; // rise up
      this.opacity = Math.random() * 0.45 + 0.1;
      this.decay = Math.random() * 0.0003 + 0.0001;
    }

    update() {
      this.x += this.speedX;
      this.y += this.speedY;
      this.opacity -= this.decay;

      if (
        this.y < 0 ||
        this.opacity <= 0 ||
        this.x < 0 ||
        this.x > canvas.width
      ) {
        this.reset(false);
      }
    }

    draw() {
      ctx.fillStyle = `rgba(140, 13, 13, ${this.opacity})`;
      ctx.shadowBlur = this.size * 3;
      ctx.shadowColor = "#cc162f";
      ctx.beginPath();
      ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
      ctx.fill();
      ctx.shadowBlur = 0; // optimized reset
    }
  }

  function populate() {
    particles = [];
    const maxCount = Math.floor((canvas.width * canvas.height) / 13000);
    for (let i = 0; i < maxCount; i++) {
      particles.push(new AshEmber());
    }
  }
  populate();

  function frame() {
    ctx.fillStyle = "rgba(2, 2, 2, 0.05)";
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    particles.forEach((p) => {
      p.update();
      p.draw();
    });
    requestAnimationFrame(frame);
  }
  frame();
}

function initHeroEmbersCanvas() {
  setupDriftingEmbersSystem("hero-smoke-canvas");
}
function initAuthEmbersCanvas() {
  setupDriftingEmbersSystem("auth-ambient-canvas");
}

/* ==========================================================================
           PORTFOLIO VAULT GALLERY INTERACTIONS
           ========================================================================== */
function initPortfolioFilterTabs() {
  const tabs = document.querySelectorAll(".filter-tab");
  const items = document.querySelectorAll(".masonry-item");

  tabs.forEach((tab) => {
    tab.addEventListener("click", () => {
      tabs.forEach((t) => t.classList.remove("active"));
      tab.classList.add("active");

      const filterValue = tab.getAttribute("data-filter");

      items.forEach((item) => {
        const cat = item.getAttribute("data-category");
        if (filterValue === "all" || cat === filterValue) {
          item.style.display = "block";
          setTimeout(() => {
            item.style.opacity = "1";
            item.style.transform = "scale(1)";
          }, 50);
        } else {
          item.style.opacity = "0";
          item.style.transform = "scale(0.92)";
          setTimeout(() => {
            item.style.display = "none";
          }, 350);
        }
      });
    });
  });
}

/**
 * Details blueprint Modal popup sheet logic - FIXED
 */
function initPortfolioModals() {
  const modal = document.getElementById("artwork-modal");
  const modalImg = document.getElementById("modal-img-source");
  const modalTag = document.getElementById("modal-tag-type");
  const modalTitle = document.getElementById("modal-title-text");
  const modalNarrative = document.getElementById("modal-narrative-text");
  const modalYear = document.getElementById("modal-spec-year");
  const closeBtn = document.getElementById("modal-close-btn");
  const actionBtn = document.getElementById("modal-action-commission");
  const addCartBtn = document.getElementById("modal-action-addcart");

  const masonryItems = document.querySelectorAll(".masonry-item");

  masonryItems.forEach((card) => {
    const overlay = card.querySelector(".masonry-item-overlay");
    if (overlay) {
      const quickAddBtn = document.createElement("button");
      quickAddBtn.className = "gallery-add-cart-btn";
      quickAddBtn.textContent = "Add to Cart";
      quickAddBtn.setAttribute("type", "button");

      // Ensure underlying image does not intercept pointer events so overlay controls are clickable
      const underlyingImg = card.querySelector(".masonry-image-box img");
      if (underlyingImg) {
        underlyingImg.style.pointerEvents = "none";
      }

      // Ensure overlay allows pointer events for its children
      overlay.style.pointerEvents = "auto";

      // Style the quick add button for visibility and interactivity
      quickAddBtn.style.pointerEvents = "auto";
      quickAddBtn.style.zIndex = "50";

      quickAddBtn.addEventListener("click", (event) => {
        event.stopPropagation();
        const title = card.getAttribute("data-title");
        const price = parseInt(card.getAttribute("data-price") || "0", 10) || 0;
        const tag =
          card.querySelector(".item-overlay-tag")?.textContent || "Ready Made";
        const src = card.getAttribute("data-img");
        const productId = `product-${title.toLowerCase().replace(/[^a-z0-9]+/g, "-")}`;
        const existing = cartArray.find((item) => item.id === productId);
        if (existing) {
          existing.quantity += 1;
          showToastNotification(
            "Artifact Added",
            `Increased quantity for ${title}.`,
          );
        } else {
          cartArray.push({
            id: productId,
            name: title,
            category: tag,
            price,
            quantity: 1,
            size: "M",
            color: "Core Black",
            material: "Atelier Premium",
            image: src,
            isCustom: false,
            description: card.getAttribute("data-desc") || "",
          });
          showToastNotification("Artifact Added", `${title} added to cart.`);
        }
        persistCartState();
        renderCartLedger();
      });
      overlay.appendChild(quickAddBtn);
    }
    card.addEventListener("click", () => {
      const src = card.getAttribute("data-img");
      const title = card.getAttribute("data-title");
      const year = card.getAttribute("data-year");
      const price = card.getAttribute("data-price");
      const desc = card.getAttribute("data-desc");
      const tag = card.querySelector(".item-overlay-tag").textContent;

      modalImg.src = src;
      modalTitle.textContent = title;
      modalNarrative.textContent = desc;
      modalYear.textContent = year;
      modalTag.textContent = tag;

      actionBtn.setAttribute("data-target-theme", title);
      actionBtn.setAttribute("data-target-tag", tag.replace("Custom ", ""));
      actionBtn.setAttribute("data-target-price", price);

      selectedPortfolioItem = {
        id: `product-${title.toLowerCase().replace(/[^a-z0-9]+/g, "-")}`,
        name: title,
        category: tag || "Ready Made",
        price: parseInt(price, 10) || 0,
        quantity: 1,
        size: "M",
        color: "Core Black",
        material: "Atelier Premium",
        image: src,
        isCustom: false,
        description: desc,
      };

      modal.classList.add("active");
    });
  });

  if (addCartBtn) {
    addCartBtn.addEventListener("click", () => {
      if (!selectedPortfolioItem) return;
      const existing = cartArray.find(
        (item) => item.id === selectedPortfolioItem.id,
      );
      if (existing) {
        existing.quantity += 1;
        showToastNotification(
          "Artifact Added",
          `Quantity updated for ${selectedPortfolioItem.name}.`,
        );
      } else {
        cartArray.push({ ...selectedPortfolioItem });
        showToastNotification(
          "Artifact Added",
          `${selectedPortfolioItem.name} added to cart.`,
        );
      }
      persistCartState();
      renderCartLedger();
      modal.classList.remove("active");
    });
  }

  closeBtn.addEventListener("click", () => modal.classList.remove("active"));
  modal.addEventListener("click", (e) => {
    if (e.target === modal) modal.classList.remove("active");
  });

  // Perbaikan: Redirect langsung ke route custom tanpa fungsi error
  actionBtn.addEventListener("click", () => {
    modal.classList.remove("active");

    // Simpan data ke localStorage agar bisa dibaca di halaman custom nanti
    localStorage.setItem(
      "pending_commission",
      JSON.stringify({
        theme: actionBtn.getAttribute("data-target-theme"),
        product: actionBtn.getAttribute("data-target-tag"),
      }),
    );

    window.location.href = "/shop/custom";
  });
}

function prefillOptionButtons(containerId, valueToSelect) {
  const buttons = document.querySelectorAll(`#${containerId} .choice-btn`);
  buttons.forEach((btn) => {
    btn.classList.remove("active");
    if (
      btn.getAttribute("data-value").toLowerCase() ===
        valueToSelect.toLowerCase() ||
      valueToSelect
        .toLowerCase()
        .includes(btn.getAttribute("data-value").toLowerCase())
    ) {
      btn.classList.add("active");
    }
  });
}

/* ==========================================================================
           CUSTOM CONFIGURATOR & FORMS
           ========================================================================== */
function initCustomFormConfigurator() {
  const form = document.getElementById("bespoke-commission-form");
  const successCard = document.getElementById("success-view-card");

  // Set default date limits to minimum tomorrow
  const dateField = document.getElementById("deadline-date");
  if (dateField) {
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    dateField.min = tomorrow.toISOString().split("T")[0];
  }

  // A. Handle Pill Single-choices selects groups
  const singleSelectContainers = [
    "product-type-choices",
    "size-choices",
    "color-choices",
    "material-choices",
    "theme-choices",
    "budget-choices",
  ];
  singleSelectContainers.forEach((id) => {
    const buttons = document.querySelectorAll(`#${id} .choice-btn`);
    buttons.forEach((btn) => {
      btn.addEventListener("click", () => {
        buttons.forEach((b) => b.classList.remove("active"));
        btn.classList.add("active");
      });
    });
  });

  // B. Multi-choices select groups
  const multiButtons = document.querySelectorAll(
    "#placement-choices .multi-btn",
  );
  multiButtons.forEach((btn) => {
    btn.addEventListener("click", () => {
      btn.classList.toggle("active");
      // Ensure at least one checked
      const actives = document.querySelectorAll(
        "#placement-choices .multi-btn.active",
      );
      if (actives.length === 0) btn.classList.add("active");
    });
  });

  // C. Reference simulated files upload triggers
  const refBox = document.getElementById("ref-image-box");
  const refInput = document.getElementById("ref-files");
  const refNotif = document.getElementById("ref-notif");

  const sketchBox = document.getElementById("sketch-image-box");
  const sketchInput = document.getElementById("sketch-file");
  const sketchNotif = document.getElementById("sketch-notif");

  if (refBox && refInput) {
    refBox.addEventListener("click", () => refInput.click());
    refInput.addEventListener("change", () => {
      const cnt = refInput.files.length;
      refNotif.textContent =
        cnt > 0 ? `${cnt} files selected for upload.` : "No files selected";
    });
  }

  if (sketchBox && sketchInput) {
    sketchBox.addEventListener("click", () => sketchInput.click());
    sketchInput.addEventListener("change", () => {
      sketchNotif.textContent =
        sketchInput.files.length > 0
          ? `Sketch: ${sketchInput.files[0].name}`
          : "No sketch selected";
    });
  }

  // D. Form submission compiler
  if (!form) {
    return;
  }

  form.addEventListener("submit", (e) => {
    e.preventDefault();

    const product = document
      .querySelector("#product-type-choices .choice-btn.active")
      .getAttribute("data-value");
    const size = document
      .querySelector("#size-choices .choice-btn.active")
      .getAttribute("data-value");
    const theme = document
      .querySelector("#theme-choices .choice-btn.active")
      .getAttribute("data-value");
    const budgetStr = document
      .querySelector("#budget-choices .choice-btn.active")
      .getAttribute("data-value");

    // Generate custom project Hex id
    const randomHex = Math.random().toString(36).substring(2, 6).toUpperCase();
    const projectCode = `SIM-2026-${randomHex}`;

    // Populating receipt text panels
    document.getElementById("receipt-id-field").textContent = projectCode;
    document.getElementById("receipt-canvas-field").textContent =
      `${product} (${size})`;
    document.getElementById("receipt-theme-field").textContent = theme;
    document.getElementById("receipt-budget-field").textContent = budgetStr;

    // Cache configured values globally inside success action parameters to allow direct checkout transitions
    const submitBtn = form.querySelector(".btn-submit-commission");
    const submitText = submitBtn.querySelector(".btn-text");

    submitBtn.style.pointerEvents = "none";
    submitBtn.style.opacity = "0.7";
    submitText.innerHTML =
      '<i class="fa-solid fa-hourglass-half fa-spin"></i> Registering Project...';

    setTimeout(() => {
      submitBtn.style.pointerEvents = "auto";
      submitBtn.style.opacity = "1";
      submitText.textContent = "Register Project Blueprint";

      form.style.display = "none";
      successCard.style.display = "block";

      showToastNotification(
        "Directory Update",
        "Configured blueprints registered under project code: " + projectCode,
      );
    }, 1500);
  });

  // Reset back to input fields form
  const resetBtn = document.getElementById("reset-form-btn");
  if (resetBtn) {
    resetBtn.addEventListener("click", () => {
      successCard.style.display = "none";
      form.style.display = "block";
      form.reset();
      refNotif.textContent = "No files selected";
      sketchNotif.textContent = "No sketch selected";
    });
  }
}

function initContactFormSubmission() {
  const form = document.getElementById("contact-interactive-form");
  if (!form) return;
  form.addEventListener("submit", (e) => {
    e.preventDefault();
    const nameEl = document.getElementById("contact-name");
    const clientName = nameEl ? nameEl.value : "Collector";
    showToastNotification(
      "Curator Handshake",
      `Thank you ${clientName}. Message routed safely to our Jakarta design atelier.`,
    );
    form.reset();
  });
}

/* ==========================================================================
           CLIENT AUTHENTICATIONS PANELS CONTROL
           ========================================================================== */
function initClientAuthForms() {
  const tabTriggers = document.querySelectorAll(".tab-trigger-btn");
  const authPanes = document.querySelectorAll(".auth-form-pane");

  tabTriggers.forEach((trigger) => {
    trigger.addEventListener("click", () => {
      const targetPaneId = trigger.getAttribute("data-auth-tab");

      tabTriggers.forEach((t) => t.classList.remove("active"));
      trigger.classList.add("active");

      authPanes.forEach((pane) => {
        pane.classList.remove("active");
        if (pane.id === `pane-${targetPaneId}`) {
          pane.offsetHeight; // force repaint
          pane.classList.add("active");
        }
      });
    });
  });

  // Eyeball toggle passwords display
  const togglers = document.querySelectorAll(".password-toggle-anchor");
  togglers.forEach((btn) => {
    btn.addEventListener("click", () => {
      const input = btn.parentElement.querySelector(".atelier-input");
      const icon = btn.querySelector("i");
      if (input.type === "password") {
        input.type = "text";
        icon.className = "fa-regular fa-eye";
      } else {
        input.type = "password";
        icon.className = "fa-regular fa-eye-slash";
      }
    });
  });

  // Login submission handler
  const loginForm = document.getElementById("auth-signin-form");
  if (loginForm) {
    loginForm.addEventListener("submit", () => {
      const email = document.getElementById("signin-email").value;
      showToastNotification(
        "Cryptography handshake",
        "Verifying cryptographic security keys for: " + email,
      );

      const btn = loginForm.querySelector(".btn-submit-commission");
      const text = btn.querySelector(".btn-text");
      if (btn && text) {
        btn.style.pointerEvents = "none";
        btn.style.opacity = "0.7";
        text.innerHTML =
          '<i class="fa-solid fa-compass-drafting fa-spin"></i> Authenticating...';
      }
    });
  }

  // Register submission handler
  const registerForm = document.getElementById("auth-signup-form");
  if (registerForm) {
    registerForm.addEventListener("submit", (e) => {
      const pass = document.getElementById("signup-password").value;
      const confirm = document.getElementById("signup-confirm").value;

      if (pass !== confirm) {
        e.preventDefault();
        showToastNotification(
          "Security Warning",
          "Blueprints security keys mismatch. Confirm again.",
          true,
        );
        return;
      }

      const btn = registerForm.querySelector(".btn-submit-commission");
      const text = btn.querySelector(".btn-text");
      if (btn && text) {
        btn.style.pointerEvents = "none";
        btn.style.opacity = "0.7";
        text.innerHTML =
          '<i class="fa-solid fa-compass-drafting fa-spin"></i> Registering identity...';
      }
    });
  }

  // Extra buttons
  const forgotTrigger = document.getElementById("forgot-trigger");
  if (forgotTrigger) {
    forgotTrigger.addEventListener("click", (e) => {
      e.preventDefault();
      showToastNotification(
        "Vault Assist",
        "Recovery keys targeted to email directory.",
      );
    });
  }

  const googleTrigger = document.getElementById("google-trigger");
  if (googleTrigger) {
    googleTrigger.addEventListener("click", () =>
      showToastNotification(
        "Secure Router",
        "Handshaking OAuth connection with Google...",
      ),
    );
  }

  const facebookTrigger = document.getElementById("facebook-trigger");
  if (facebookTrigger) {
    facebookTrigger.addEventListener("click", () =>
      showToastNotification(
        "Secure Router",
        "Handshaking OAuth connection with Facebook...",
      ),
    );
  }
}

/* ==========================================================================
           INTERACTIVE CART LEDGER CALCULATIONS SYSTEM
           ========================================================================== */
function formatIndoRupiah(value) {
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    minimumFractionDigits: 0,
  }).format(value);
}

/**
 * Fungsi untuk menyimpan pesanan custom ke keranjang
 * dan memindahkannya ke halaman Cart (menggunakan localStorage)
 */
function injectCommissionToCart() {
  // 1. Mengambil data dari form/interface Anda
  // Pastikan ID elemen di HTML Anda sesuai dengan yang ada di bawah ini
  const productElement = document.querySelector(
    "#product-type-choices .choice-btn.active",
  );
  const sizeElement = document.querySelector(
    "#size-choices .choice-btn.active",
  );
  const colorElement = document.querySelector(
    "#color-choices .choice-btn.active",
  );
  const materialElement = document.querySelector(
    "#material-choices .choice-btn.active",
  );
  const themeElement = document.querySelector(
    "#theme-choices .choice-btn.active",
  );

  // Validasi dasar jika user belum memilih salah satu opsi
  if (!productElement || !sizeElement) {
    showToastNotification(
      "Atelier Pipeline",
      "Mohon lengkapi pilihan desain Anda terlebih dahulu.",
      true,
    );
    return;
  }

  const product = productElement.getAttribute("data-value");
  const size = sizeElement.getAttribute("data-value");
  const color = colorElement.getAttribute("data-value");
  const material = materialElement.getAttribute("data-value");
  const theme = themeElement.getAttribute("data-value");

  const pCode = document.getElementById("receipt-id-field").textContent;
  const targetDesc = document.getElementById("design-desc").value;

  // Formula harga sederhana
  let calculatedBasePrice = 3500000;
  if (product === "T-Shirt") calculatedBasePrice = 950000;
  else if (product === "Tote Bag") calculatedBasePrice = 650000;
  else if (product === "Jacket") calculatedBasePrice = 8500000;

  const customProductBlock = {
    id: pCode,
    name: `Bespoke custom painted ${product}`,
    category: "Custom Design Room",
    price: calculatedBasePrice,
    quantity: 1,
    size: size,
    color: color,
    material: material,
    image:
      "https://images.unsplash.com/photo-1576995853123-5a10305d93c0?auto=format&fit=crop&w=350&q=80",
    isCustom: true,
    customDetails: {
      theme: theme,
      placement: "Back panel fully customized styling",
      status: "Blueprints registered inside Atelier ledger",
      description: targetDesc,
    },
  };

  // 2. LOGIKA PENYIMPANAN KE BROWSER (Agar data tidak hilang saat pindah halaman)
  // Ambil data yang sudah ada di localStorage, jika belum ada maka buat array kosong
  cartArray = JSON.parse(localStorage.getItem("batom_cart")) || [];

  // Filter duplikat berdasarkan ID (pCode)
  cartArray = cartArray.filter((i) => i.id !== pCode);

  // Masukkan pesanan baru
  cartArray.push(customProductBlock);

  // Simpan kembali ke localStorage
  persistCartState();

  // 3. MEMBERIKAN FEEDBACK & PINDAH HALAMAN
  showToastNotification(
    "Atelier Pipeline",
    `Loaded bespoke commission ${pCode} into checkout ledger.`,
  );

  // Redirect to cart page after localStorage state has been saved
  setTimeout(() => {
    window.location.href = "/cart";
  }, 1500);
}

/**
 * Core Cart View Renderer
 */
function renderCartLedger() {
  const listGrid = document.getElementById("active-cart-injection-grid");
  const emptyState = document.getElementById("cart-empty-layout");

  // If the cart view is not present on this page, only update totals/badge and bail out
  if (!listGrid || !emptyState) {
    updateCartTotals();
    return;
  }

  listGrid.innerHTML = "";

  if (cartArray.length === 0) {
    listGrid.style.display = "none";
    emptyState.style.display = "flex";
    updateCartTotals();
    return;
  }

  listGrid.style.display = "flex";
  listGrid.style.flexDirection = "column";
  listGrid.style.gap = "25px";
  emptyState.style.display = "none";

  cartArray.forEach((item) => {
    const card = document.createElement("div");
    card.className = `cart-item-card ${item.isCustom ? "cart-item-card-custom" : ""}`;
    card.dataset.itemId = item.id;

    let customBlockHTML = "";
    if (item.isCustom && item.customDetails) {
      customBlockHTML = `
                        <div class="cart-custom-details-panel">
                            <div class="cart-custom-blueprint-title">
                                <i class="fa-solid fa-compass-drafting"></i>
                                <span>Custom Atelier Specifications</span>
                            </div>
                            <div class="cart-custom-grid">
                                <div>Artwork Theme: <span>${item.customDetails.theme}</span></div>
                                <div>Placement: <span>${item.customDetails.placement}</span></div>
                                <div>Commission status: <span class="custom-badge-status">${item.customDetails.status}</span></div>
                            </div>
                            <div style="border-top: 1px solid rgba(255,255,255,0.03); padding-top:12px; font-size:0.8rem;">
                                <h5 style="color:#fff; margin-bottom:5px; font-family:var(--font-serif); text-transform:uppercase;">Collector Request Notes</h5>
                                <p style="font-style:italic; color:var(--color-grey-light)">"${item.customDetails.description}"</p>
                            </div>
                        </div>
                    `;
    }

    card.innerHTML = `
                    <div class="item-thumb-box">
                        <img src="${item.image}" alt="${item.name}" onerror="this.src='https://placehold.co/300x400/080808/8b0000?text=SIMONSTER'">
                    </div>
                    <div class="item-details-panel">
                        <div class="item-meta-header">
                            <div>
                                <span class="item-tag-category">${item.category}</span>
                                <h3 class="item-title-name">${item.name}</h3>
                                <ul class="item-options-list">
                                    <li>Size: <span>${item.size}</span></li>
                                    <li>Color: <span>${item.color}</span></li>
                                    <li>Material: <span>${item.material}</span></li>
                                </ul>
                            </div>
                            <span class="item-unit-cost">${formatIndoRupiah(item.price)}</span>
                        </div>

                        ${customBlockHTML}

                        <div class="item-options-actions-row">
                            <div class="item-quick-actions">
                                <button class="quick-action-btn" onclick="saveItemForLater('${item.id}')"><i class="fa-regular fa-bookmark"></i> Save For Later</button>
                                <button class="quick-action-btn" onclick="animateAndRemoveCartItem('${item.id}')" style="color:var(--color-grey-mid);"><i class="fa-solid fa-trash-can"></i> Remove</button>
                            </div>

                            <div style="display:flex; align-items:center; gap:20px;">
                                <div class="qty-adjuster-box">
                                    <button class="qty-trigger" onclick="adjustItemQty('${item.id}', -1)"><i class="fa-solid fa-minus"></i></button>
                                    <span class="qty-display">${item.quantity}</span>
                                    <button class="qty-trigger" onclick="adjustItemQty('${item.id}', 1)"><i class="fa-solid fa-plus"></i></button>
                                </div>
                                <span class="item-total-cost">${formatIndoRupiah(item.price * item.quantity)}</span>
                            </div>
                        </div>
                    </div>
                `;

    listGrid.appendChild(card);
  });

  updateCartTotals();
}

function animateAndRemoveCartItem(itemId) {
  const node = document.querySelector(
    `.cart-item-card[data-item-id="${itemId}"]`,
  );
  if (node) {
    node.classList.add("removing");
    setTimeout(() => deleteCartItem(itemId), 400);
  } else {
    deleteCartItem(itemId);
  }
}

function deleteCartItem(itemId) {
  const index = cartArray.findIndex((i) => i.id === itemId);
  if (index > -1) {
    const name = cartArray[index].name;
    cartArray.splice(index, 1);
    renderCartLedger();
    persistCartState();
    showToastNotification(
      "Vault Purged",
      `Removed "${name}" from active checkout list.`,
    );
  }
}

function adjustItemQty(itemId, delta) {
  const item = cartArray.find((i) => i.id === itemId);
  if (item) {
    const newQty = item.quantity + delta;
    if (newQty >= 1) {
      item.quantity = newQty;
      renderCartLedger();
      persistCartState();
    } else {
      animateAndRemoveCartItem(itemId);
    }
  }
}

/**
 * Subtotal math computations reflecting taxes, delivery fees & customized design surcharge
 */
function updateCartTotals() {
  let subtotal = 0;
  let count = 0;
  let containsCustomCommission = false;

  cartArray.forEach((item) => {
    subtotal += item.price * item.quantity;
    count += item.quantity;
    if (item.isCustom) containsCustomCommission = true;
  });

  const VAT_TAX_RATE = 0.11;
  const customArtisanSurcharge = containsCustomCommission ? 500000 : 0;
  const computedTax = subtotal * VAT_TAX_RATE;
  const calculatedShipping =
    subtotal > 0 ? (subtotal > 8000000 ? 0 : 75000) : 0; // Free shipping over 8jt
  const evaluatedGrandtotal =
    subtotal + computedTax + calculatedShipping + customArtisanSurcharge;

  // Set UI updates safely (guard for pages that don't include cart summary)
  const elSubtotal = document.getElementById("summary-subtotal");
  if (elSubtotal) elSubtotal.textContent = formatIndoRupiah(subtotal);

  const elTax = document.getElementById("summary-tax");
  if (elTax) elTax.textContent = formatIndoRupiah(computedTax);

  // Surcharge panel handling
  const surchargeRow = document.getElementById("summary-surcharge-row");
  const elSurcharge = document.getElementById("summary-surcharge");
  if (surchargeRow && elSurcharge) {
    if (containsCustomCommission) {
      surchargeRow.style.display = "flex";
      elSurcharge.textContent = formatIndoRupiah(customArtisanSurcharge);
    } else {
      surchargeRow.style.display = "none";
    }
  }

  // Shipping panel representation text
  const shippingUI = document.getElementById("summary-shipping");
  if (shippingUI) {
    if (subtotal > 0) {
      shippingUI.textContent =
        calculatedShipping === 0
          ? "Free Shipping (Exclusive)"
          : formatIndoRupiah(calculatedShipping);
    } else {
      shippingUI.textContent = "Rp 0";
    }
  }

  const elGrand = document.getElementById("summary-grandtotal");
  if (elGrand) elGrand.textContent = formatIndoRupiah(evaluatedGrandtotal);

  // Navigation badge indicators
  const navBadge = document.getElementById("navigation-cart-badge");
  if (navBadge) {
    navBadge.textContent = count;
    navBadge.style.display = count > 0 ? "flex" : "none";
  }
}

/**
 * Shifting products list into Save Later section below
 */
function saveItemForLater(itemId) {
  const index = cartArray.findIndex((i) => i.id === itemId);
  if (index > -1) {
    const target = cartArray[index];
    savedArray.push(target);
    cartArray.splice(index, 1);

    renderCartLedger();
    renderSavedForLaterGrid();
    persistCartState();

    showToastNotification(
      "Secured In Archive",
      `Saved "${target.name}" for later evaluation.`,
    );
  }
}

function renderSavedForLaterGrid() {
  const grid = document.getElementById("saved-later-injection-grid");
  if (!grid) return;
  grid.innerHTML = "";

  if (savedArray.length === 0) {
    grid.innerHTML = `
                    <div style="border: 1px dashed rgba(255,255,255,0.03); padding: 40px; text-align: center; color: var(--color-grey-mid); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.1em;">
                        Your temporary profile archive is empty.
                    </div>
                `;
    return;
  }

  savedArray.forEach((item) => {
    const card = document.createElement("div");
    card.className = "saved-item-row-card";
    card.innerHTML = `
                    <div class="saved-item-info-thumb">
                        <div class="saved-item-thumb">
                            <img src="${item.image}" alt="${item.name}" onerror="this.src='https://placehold.co/300x400/080808/8b0000?text=SIMONSTER'">
                        </div>
                        <div>
                            <span style="font-size:0.65rem; color:var(--color-red-bright); text-transform:uppercase; letter-spacing:0.1em; display:block; margin-bottom:2px;">${item.category}</span>
                            <h4 style="font-family:var(--font-serif); font-size:1.05rem; font-weight:600; color:#fff; letter-spacing:0.05em;">${item.name}</h4>
                            <span style="font-family:var(--font-serif); font-size:0.95rem; color:var(--color-grey-light); margin-top:4px; display:block;">${formatIndoRupiah(item.price)}</span>
                        </div>
                    </div>
                    <div style="display:flex; gap:20px;">
                        <button class="quick-action-btn" onclick="restoreSavedToCart('${item.id}')" style="color:#fff;"><i class="fa-solid fa-cart-arrow-down"></i> Restore To Cart</button>
                        <button class="quick-action-btn" onclick="deleteSavedItem('${item.id}')">Remove</button>
                    </div>
                `;
    grid.appendChild(card);
  });
}

function restoreSavedToCart(itemId) {
  const index = savedArray.findIndex((i) => i.id === itemId);
  if (index > -1) {
    const target = savedArray[index];
    cartArray.push(target);
    savedArray.splice(index, 1);

    renderCartLedger();
    renderSavedForLaterGrid();
    persistCartState();
    showToastNotification(
      "Restored selections",
      `Moved "${target.name}" back to active list.`,
    );
  }
}

function deleteSavedItem(itemId) {
  const index = savedArray.findIndex((i) => i.id === itemId);
  if (index > -1) {
    const targetName = savedArray[index].name;
    savedArray.splice(index, 1);
    renderSavedForLaterGrid();
    persistCartState();
    showToastNotification(
      "Purged Permanently",
      `Permanently deleted saved item "${targetName}".`,
    );
  }
}

/**
 * Recommended Products Catalog cross-sells
 */
function renderRecommendationsCrossSells() {
  const grid = document.getElementById("recommendations-injection-grid");
  if (!grid) return;
  grid.innerHTML = "";

  RECOMMENDATIONS_DB.forEach((prod) => {
    const card = document.createElement("div");
    card.className = "rec-item-mini-card";
    card.innerHTML = `
                    <div class="rec-thumb">
                        <span class="rec-rare-badge">${prod.badge}</span>
                        <img src="${prod.image}" alt="${prod.name}">
                    </div>
                    <div style="margin-top:18px; flex-grow:1; display:flex; flex-direction:column; justify-content:space-between;">
                        <div style="margin-bottom:15px;">
                            <span style="font-size:0.65rem; text-transform:uppercase; letter-spacing:0.1em; color:var(--color-red-bright); display:block; margin-bottom:3px;">${prod.category}</span>
                            <h4 style="font-family:var(--font-serif); font-size:1rem; font-weight:600; color:#fff; letter-spacing:0.05em; line-height:1.3;">${prod.name}</h4>
                        </div>
                        <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid rgba(255,255,255,0.03); padding-top:12px;">
                            <span style="font-family:var(--font-serif); font-size:0.95rem; color:var(--color-silver); font-weight:500;">${formatIndoRupiah(prod.price)}</span>
                            <button class="rec-add-btn" onclick="appendCrossSellToCart('${prod.id}')" aria-label="Add artifact to cart"><i class="fa-solid fa-plus"></i></button>
                        </div>
                    </div>
                `;
    grid.appendChild(card);
  });
}

function appendCrossSellToCart(prodId) {
  const match = RECOMMENDATIONS_DB.find((p) => p.id === prodId);
  if (match) {
    // check if already inside cart array to just update qty instead
    const existing = cartArray.find((c) => c.id === prodId);
    if (existing) {
      existing.quantity += 1;
      showToastNotification(
        "Quantity Incremented",
        `Added secondary copy of "${match.name}" to cart.`,
      );
    } else {
      cartArray.push({
        id: match.id,
        name: match.name,
        category: match.category,
        price: match.price,
        quantity: 1,
        size: "M", // safe default
        color: "Aesthetic Black",
        material: "Selected Atelier Quality",
        image: match.image,
        isCustom: false,
      });
      showToastNotification(
        "Artifact Added",
        `Incorporated "${match.name}" into selections.`,
      );
    }
    renderCartLedger();
  }
}

/* ==========================================================================
           CHECKOUT TRANSACTION TRIGGER SYSTEM
           ========================================================================== */
function initCheckoutSecureAction() {
  const trigger = document.getElementById("cart-checkout-trigger");
  if (!trigger) return;
  const portalModal = document.getElementById("checkout-secure-modal");
  const closeBtn = document.getElementById("checkout-close-btn");

  trigger.addEventListener("click", () => {
    if (cartArray.length === 0) {
      showToastNotification(
        "Vault Empty",
        "Please incorporate at least one artifact to proceed with checking out.",
        true,
      );
      return;
    }

    const termsCheck = document.getElementById("terms-agree");
    if (!termsCheck.checked) {
      showToastNotification(
        "Policy Agreement Required",
        "Authorization check required. Accept terms to proceed.",
        true,
      );
      return;
    }

    let finalQuantities = 0;
    let hasCustom = false;
    let calculatedSubtotal = 0;

    cartArray.forEach((item) => {
      finalQuantities += item.quantity;
      calculatedSubtotal += item.price * item.quantity;
      if (item.isCustom) hasCustom = true;
    });

    // compute taxes & dispatch surcharges directly on checkout confirmation slip
    const computedTaxValue = calculatedSubtotal * 0.11;
    const shippingValue = calculatedSubtotal > 8000000 ? 0 : 75000;
    const surchargeValue = hasCustom ? 500000 : 0;
    const totalCalculatedGrandTotal =
      calculatedSubtotal + computedTaxValue + shippingValue + surchargeValue;

    // Load compiled checkout slip receipt
    document.getElementById("receipt-item-count").textContent =
      `${finalQuantities} Item${finalQuantities > 1 ? "s" : ""}`;
    document.getElementById("receipt-blueprint-status").textContent = hasCustom
      ? "Awaiting Artisan blueprints verification"
      : "None Active";
    document.getElementById("receipt-checkout-grandtotal").textContent =
      formatIndoRupiah(totalCalculatedGrandTotal);

    // Create random transaction key
    const randomCode = Math.random().toString(36).substring(2, 6).toUpperCase();
    document.getElementById("receipt-ref-code").textContent =
      `SMNSTR-2026-${randomCode}`;

    const btnText = trigger.querySelector(".btn-text");
    trigger.style.pointerEvents = "none";
    trigger.style.opacity = "0.7";
    btnText.innerHTML =
      '<i class="fa-solid fa-hourglass-half fa-spin"></i> Securing Connection...';

    setTimeout(async () => {
      trigger.style.pointerEvents = "auto";
      trigger.style.opacity = "1";
      btnText.textContent = "PROCEED TO CHECKOUT";

      const payload = buildCheckoutPayload();
      const response = await submitCheckout(payload);

      if (response && response.success) {
        latestCheckoutOrder = response;
        document.getElementById("receipt-ref-code").textContent =
          response.order_code || "SMNSTR-2026-XXXX";
        document.getElementById("receipt-checkout-grandtotal").textContent =
          formatIndoRupiah(response.total_price || 0);
        portalModal.classList.add("active");

        prepareWhatsAppShare(response);
      } else {
        showToastNotification(
          "Checkout Failed",
          response?.message ||
            "Tidak dapat menyelesaikan checkout. Silakan coba lagi.",
          true,
        );
      }
    }, 2000);
  });

  closeBtn.addEventListener("click", () =>
    portalModal.classList.remove("active"),
  );
  portalModal.addEventListener("click", (e) => {
    if (e.target === portalModal) portalModal.classList.remove("active");
  });
}

async function submitCheckout(payload) {
  try {
    const response = await fetch("/checkout/process", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(payload),
    });
    return await response.json();
  } catch (error) {
    console.error(error);
    return {
      success: false,
      message: "Terjadi kesalahan jaringan saat checkout.",
    };
  }
}

function buildCheckoutPayload() {
  const items = cartArray.map((item) => ({
    id: item.id,
    name: item.name,
    price: item.price,
    quantity: item.quantity,
    size: item.size || null,
    color: item.color || null,
    material: item.material || null,
    isCustom: item.isCustom || false,
    customDetails: item.customDetails || {},
  }));

  const totalPrice = cartArray.reduce(
    (sum, item) => sum + item.price * item.quantity,
    0,
  );
  const hasCustom = cartArray.some((item) => item.isCustom);
  const shipping = totalPrice > 8000000 ? 0 : 75000;
  const tax = totalPrice * 0.11;
  const surcharge = hasCustom ? 500000 : 0;
  const grandTotal = totalPrice + shipping + tax + surcharge;

  return {
    items,
    total_price: grandTotal,
    payment_method: "whatsapp",
    budget_range:
      document.getElementById("receipt-budget-field")?.textContent || "N/A",
    target_deadline: null,
    notes: cartArray
      .map(
        (item) =>
          `${item.quantity}x ${item.name} - ${item.size || "-"}, ${item.color || "-"}`,
      )
      .join(" | "),
  };
}

function prepareWhatsAppShare(order) {
  const phone = "6281361073822";
  const message = [
    "Halo, saya sudah melakukan checkout di BATOM.",
    "Order Code: " + (order.order_code || "—"),
    "Total: " + formatIndoRupiah(order.total_price || 0),
    "Detail pesanan: " +
      cartArray
        .map(
          (item) =>
            `${item.quantity}x ${item.name} (${item.size || "-"}, ${item.color || "-"})`,
        )
        .join("; "),
    "Silakan bantu proses selanjutnya.",
  ].join("\n");

  const encoded = encodeURIComponent(message);
  const waUrl = `https://wa.me/${phone}?text=${encoded}`;

  const existingBtn = document.getElementById("checkout-whatsapp-share");
  if (existingBtn) {
    existingBtn.setAttribute("href", waUrl);
  }
}

function initReceiptDownloadAction() {
  const downloadBtn = document.getElementById("receipt-download-btn");
  if (!downloadBtn) return;

  downloadBtn.addEventListener("click", (e) => {
    e.preventDefault();
    const orderCode =
      document.getElementById("receipt-ref-code")?.textContent ||
      "BATOM-RECEIPT";
    downloadReceiptJPG(orderCode);
  });
}

function downloadReceiptJPG(orderCode) {
  const width = 1000;
  const height = 1200;
  const canvas = document.createElement("canvas");
  canvas.width = width;
  canvas.height = height;
  const ctx = canvas.getContext("2d");
  if (!ctx) return;

  ctx.fillStyle = "#080808";
  ctx.fillRect(0, 0, width, height);
  ctx.fillStyle = "#101010";
  ctx.fillRect(40, 40, width - 80, height - 80);
  ctx.fillStyle = "#1f1f1f";
  ctx.fillRect(60, 60, width - 120, 260);

  ctx.fillStyle = "#f9fafb";
  ctx.font = "bold 42px Montserrat, sans-serif";
  ctx.fillText("BATOM RECEIPT", 80, 130);
  ctx.font = "16px Montserrat, sans-serif";
  ctx.fillStyle = "#cbd5e1";
  ctx.fillText("One-of-One Wearable Art", 80, 160);

  ctx.fillStyle = "#ef4444";
  ctx.font = "bold 18px Montserrat, sans-serif";
  ctx.fillText("Order Reference:", 80, 210);
  ctx.fillStyle = "#f8fafc";
  ctx.fillText(orderCode, 320, 210);

  const orderDate = new Date().toLocaleDateString("id-ID", {
    day: "2-digit",
    month: "long",
    year: "numeric",
  });
  ctx.fillStyle = "#94a3b8";
  ctx.font = "14px Montserrat, sans-serif";
  ctx.fillText(`Date: ${orderDate}`, 80, 245);
  ctx.fillText(`Items: ${cartArray.length}`, 80, 270);
  ctx.fillText(
    `Grand Total: ${formatIndoRupiah(latestCheckoutOrder?.total_price || 0)}`,
    80,
    295,
  );

  ctx.fillStyle = "#334155";
  ctx.fillRect(80, 330, width - 160, 2);

  ctx.fillStyle = "#f8fafc";
  ctx.font = "bold 24px Montserrat, sans-serif";
  ctx.fillText("Itemized Order", 80, 370);

  ctx.font = "16px Montserrat, sans-serif";
  ctx.fillStyle = "#cbd5e1";
  let y = 410;
  cartArray.forEach((item, index) => {
    const itemText = `${index + 1}. ${item.quantity}x ${item.name}`;
    ctx.fillText(itemText, 80, y);
    y += 28;
    const itemMeta = `${item.size || "-"} / ${item.color || "-"} / ${item.material || "-"}`;
    ctx.font = "14px Montserrat, sans-serif";
    ctx.fillStyle = "#94a3b8";
    ctx.fillText(itemMeta, 100, y);
    y += 24;
    ctx.fillStyle = "#f8fafc";
    ctx.font = "16px Montserrat, sans-serif";
    if (y > height - 140) {
      ctx.fillText("...dan item lainnya", 80, y);
      y += 24;
      return;
    }
  });

  ctx.fillStyle = "#334155";
  ctx.fillRect(80, height - 220, width - 160, 2);
  ctx.font = "bold 20px Montserrat, sans-serif";
  ctx.fillStyle = "#f8fafc";
  ctx.fillText("Terima kasih sudah berbelanja dengan BATOM.", 80, height - 170);
  ctx.font = "14px Montserrat, sans-serif";
  ctx.fillStyle = "#94a3b8";
  ctx.fillText(
    "Silakan gunakan WhatsApp untuk melanjutkan proses pembayaran dan desain.",
    80,
    height - 140,
  );

  const link = document.createElement("a");
  link.href = canvas.toDataURL("image/jpeg", 0.92);
  link.download = `BATOM_Receipt_${orderCode.replace(/[^A-Za-z0-9\-_]/g, "_")}.jpg`;
  document.body.appendChild(link);
  link.click();
  link.remove();
}

function clearAndResetCartState() {
  // Empty Cart
  cartArray = [];
  savedArray = [];
  renderCartLedger();
  persistCartState();

  // Clear modal backdrop
  const checkoutModal = document.getElementById("checkout-secure-modal");
  if (checkoutModal) checkoutModal.classList.remove("active");
  switchView("landing");
  showToastNotification(
    "Atelier Ledger reset",
    "Your showroom session has been refreshed safely.",
  );
}

function persistCartState() {
  localStorage.setItem("batom_cart", JSON.stringify(cartArray));
  localStorage.setItem("batom_saved", JSON.stringify(savedArray));
}

/* ==========================================================================
           UTILITY COMPONENT INTERACTIONS (TOASTS & SCROLL REVEALS)
           ========================================================================== */
let toastActiveTimer;
function showToastNotification(headerTitle, messageText, isWarning = false) {
  const toastFrame = document.getElementById("system-toast-widget");
  const toastTitle = document.getElementById("toast-header-text");
  const toastDesc = document.getElementById("toast-description-text");

  // If toast components are not present on the current page, fallback to console log
  if (!toastFrame || !toastTitle || !toastDesc) {
    console.log(
      "[TOAST]",
      headerTitle,
      messageText,
      isWarning ? "WARN" : "INFO",
    );
    return;
  }

  const colorLine = toastFrame.querySelector(".toast-popup-line");

  clearTimeout(toastActiveTimer);

  toastTitle.textContent = headerTitle;
  toastDesc.textContent = messageText;

  if (isWarning) {
    if (colorLine) {
      colorLine.style.backgroundColor = "#cc162f";
      colorLine.style.boxShadow = "0 0 10px #cc162f";
    }
    toastFrame.style.borderColor = "rgba(204, 22, 47, 0.4)";
  } else {
    if (colorLine) {
      colorLine.style.backgroundColor = "#8c0d0d";
      colorLine.style.boxShadow = "0 0 8px #8c0d0d";
    }
    toastFrame.style.borderColor = "rgba(140, 13, 13, 0.35)";
  }

  toastFrame.classList.add("show");

  toastActiveTimer = setTimeout(() => {
    toastFrame.classList.remove("show");
  }, 4500);
}

/**
 * Initialize viewport intersections elements for timeline animations reveals
 */
function initScrollRevealObserver() {
  const revealNodes = document.querySelectorAll(".scroll-reveal");
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("visible");
        }
      });
    },
    {
      threshold: 0.15,
      rootMargin: "0px 0px -50px 0px",
    },
  );

  revealNodes.forEach((node) => observer.observe(node));
}
