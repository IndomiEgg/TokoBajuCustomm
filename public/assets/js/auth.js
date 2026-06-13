/* ==========================================================================
   BATOM 1:1 AUTHENTICATION PORTAL JAVASCRIPT
   ========================================================================== */

document.addEventListener("DOMContentLoaded", () => {
  // 0. Hide preloader when page loads
  const preloader = document.getElementById("preloader");
  if (preloader) {
    preloader.style.opacity = "0";
    preloader.style.visibility = "hidden";
    setTimeout(() => {
      if (preloader && preloader.parentNode) {
        preloader.parentNode.removeChild(preloader);
      }
    }, 1000);
  }

  // 1. Initialise the smoke particle systems
  initShowcaseSmokeAtmosphere();

  // 2. Initialise interactive tab switching
  initFormTabNavigation();

  // 3. Password field eye displays toggling logic
  initPasswordVisibilityTogglers();

  // 4. Form Submission events & feedback loops
  initFormSubmissionControllers();

  // 5. Ambient Mouse Spot Tracking
  initMouseSpotTracker();
});

/**
 * Dynamic glowing red spotlight tracking mouse motion (Desktop only)
 */
function initMouseSpotTracker() {
  const cursorGlow = document.getElementById("mouse-glow-element");
  if (!cursorGlow) return;

  let mouseX = 0,
    mouseY = 0;
  let currentX = 0,
    currentY = 0;

  window.addEventListener("mousemove", (e) => {
    mouseX = e.clientX;
    mouseY = e.clientY;
  });

  // Linear interpolation to make the track motion look ultra smooth and lazy
  function smoothMoveGlow() {
    const interpolationCoeff = 0.08;
    currentX += (mouseX - currentX) * interpolationCoeff;
    currentY += (mouseY - currentY) * interpolationCoeff;

    cursorGlow.style.transform = `translate(${currentX}px, ${currentY}px)`;
    requestAnimationFrame(smoothMoveGlow);
  }
  smoothMoveGlow();
}

/**
 * Navigation controls for tab switching between Sign In and Create Account
 */
function initFormTabNavigation() {
  const tabs = document.querySelectorAll(".tab-btn");
  const panes = document.querySelectorAll(".form-pane-item");

  tabs.forEach((tab) => {
    tab.addEventListener("click", () => {
      const targetPaneId = tab.getAttribute("data-tab-id");

      // Set target active buttons
      tabs.forEach((t) => t.classList.remove("active"));
      tab.classList.add("active");

      // Switch panels with smooth transition frames
      panes.forEach((pane) => {
        pane.classList.remove("active");
        if (pane.id === targetPaneId) {
          // Read element offset height to force reflow sequence before adding active class transitions
          pane.offsetHeight;
          pane.classList.add("active");
        }
      });
    });
  });
}

/**
 * Eye-slash icon switcher inside password controls
 */
function initPasswordVisibilityTogglers() {
  const togglers = document.querySelectorAll(".eye-toggle-btn");

  togglers.forEach((toggler) => {
    toggler.addEventListener("click", () => {
      const relativeInputField = toggler.parentElement.querySelector(
        ".input-luxury-field",
      );
      const iconElement = toggler.querySelector("i");

      if (relativeInputField.type === "password") {
        relativeInputField.type = "text";
        iconElement.className = "fa-regular fa-eye";
      } else {
        relativeInputField.type = "password";
        iconElement.className = "fa-regular fa-eye-slash";
      }
    });
  });
}

/**
 * Submitting inputs triggers high-fidelity simulated progress bar loader
 */
function initFormSubmissionControllers() {
  // REMOVED: All form submission interceptors
  // Forms now submit naturally to server via POST
  // No JavaScript prevents or delays form submission

  // Social & Utility triggers
  const googleAuth = document.getElementById("google-auth-btn");
  const facebookAuth = document.getElementById("facebook-auth-btn");
  const forgotPass = document.getElementById("forgot-password-trigger");
  const termsRules = document.getElementById("terms-rules-anchor");

  if (googleAuth) {
    googleAuth.addEventListener("click", () => {
      showToastNotification(
        "SECURITY ROUTER",
        "Establishing OAuth pipeline link with Google servers...",
      );
    });
  }
  if (facebookAuth) {
    facebookAuth.addEventListener("click", () => {
      showToastNotification(
        "SECURITY ROUTER",
        "Establishing OAuth pipeline link with Facebook servers...",
      );
    });
  }
  if (forgotPass) {
    forgotPass.addEventListener("click", (e) => {
      e.preventDefault();
      showToastNotification(
        "VAULT ASSIST",
        "Password reset keys have been routed to your registered email directory.",
      );
    });
  }
  if (termsRules) {
    termsRules.addEventListener("click", (e) => {
      e.preventDefault();
      showToastNotification(
        "ATELIER ARCHIVE",
        "Terms of Service parameters display complete in the custom index directory.",
      );
    });
  }
}

/**
 * Visual states manipulation helper for loading buttons
 */
function toggleButtonLoadingState(buttonElement, isLoadingState) {
  const buttonText = buttonElement.querySelector(".btn-submit-text");
  if (isLoadingState) {
    buttonElement.style.pointerEvents = "none";
    buttonElement.style.opacity = "0.65";
    buttonText.dataset.originalContent = buttonText.textContent;
    buttonText.innerHTML =
      '<i class="fa-solid fa-crosshairs fa-spin"></i> Secure Processing...';
  } else {
    buttonElement.style.pointerEvents = "auto";
    buttonElement.style.opacity = "1";
    buttonText.textContent = buttonText.dataset.originalContent || "Confirm";
  }
}

/**
 * Global toast banner triggers
 */
let toastActiveTimeout;
function showToastNotification(
  headerTitle,
  messageText,
  isWarningState = false,
) {
  const toastFrame = document.getElementById("atelier-toast-portal");
  const toastTitle = document.getElementById("toast-title");
  const toastDesc = document.getElementById("toast-description");
  const lateralBar = toastFrame.querySelector(".toast-side-line");

  clearTimeout(toastActiveTimeout);

  toastTitle.textContent = headerTitle;
  toastDesc.textContent = messageText;

  if (isWarningState) {
    lateralBar.style.backgroundColor = "#b30f0f";
    lateralBar.style.boxShadow = "0 0 10px #b30f0f";
    toastFrame.style.borderColor = "rgba(179, 15, 15, 0.4)";
  } else {
    lateralBar.style.backgroundColor = "#8c0d0d";
    lateralBar.style.boxShadow = "0 0 10px #8c0d0d";
    toastFrame.style.borderColor = "rgba(140, 13, 13, 0.3)";
  }

  toastFrame.classList.add("show");

  toastActiveTimeout = setTimeout(() => {
    toastFrame.classList.remove("show");
  }, 4500);
}

/**
 * Ambient HTML5 Canvas Smoke Particle System
 * Renders floating ash flakes and dark red/crimson hot soot embers upward
 */
function initShowcaseSmokeAtmosphere() {
  const smokeCanvas = document.getElementById("smoke-canvas");
  if (!smokeCanvas) return;

  const renderContext = smokeCanvas.getContext("2d");
  let systemEmbers = [];

  function recomputeCanvasViewport() {
    smokeCanvas.width = smokeCanvas.parentElement.offsetWidth;
    smokeCanvas.height = smokeCanvas.parentElement.offsetHeight;
  }
  recomputeCanvasViewport();
  window.addEventListener("resize", () => {
    recomputeCanvasViewport();
    populateEmberArray();
  });

  class EmberParticle {
    constructor() {
      this.resetEmberProperties(true);
    }

    resetEmberProperties(isInitialPopulation = false) {
      this.x = Math.random() * smokeCanvas.width;
      this.y = isInitialPopulation
        ? Math.random() * smokeCanvas.height
        : smokeCanvas.height + 15;
      this.size = Math.random() * 2 + 0.4;
      this.speedX = Math.random() * 0.25 - 0.125;
      this.speedY = Math.random() * -0.45 - 0.15; // float slow and upward
      this.opacity = Math.random() * 0.45 + 0.1;
      this.durationFactor = Math.random() * 0.00035 + 0.0001;
    }

    updatePosition() {
      this.x += this.speedX;
      this.y += this.speedY;
      this.opacity -= this.durationFactor;

      if (
        this.y < -15 ||
        this.opacity <= 0 ||
        this.x < -10 ||
        this.x > smokeCanvas.width + 10
      ) {
        this.resetEmberProperties(false);
      }
    }

    drawEmber() {
      renderContext.fillStyle = `rgba(140, 13, 13, ${this.opacity})`;
      renderContext.shadowBlur = this.size * 3.5;
      renderContext.shadowColor = "#8c0d0d";
      renderContext.beginPath();
      renderContext.arc(this.x, this.y, this.size, 0, Math.PI * 2);
      renderContext.fill();
      renderContext.shadowBlur = 0; // optimized reset
    }
  }

  function populateEmberArray() {
    systemEmbers = [];
    const maxEmbers = Math.floor(
      (smokeCanvas.width * smokeCanvas.height) / 12500,
    );
    for (let i = 0; i < maxEmbers; i++) {
      systemEmbers.push(new EmberParticle());
    }
  }
  populateEmberArray();

  function smokeRenderLoop() {
    // Smooth frame overlay clear
    renderContext.fillStyle = "rgba(2, 2, 2, 0.06)";
    renderContext.fillRect(0, 0, smokeCanvas.width, smokeCanvas.height);

    systemEmbers.forEach((ember) => {
      ember.updatePosition();
      ember.drawEmber();
    });

    requestAnimationFrame(smokeRenderLoop);
  }
  smokeRenderLoop();
}
