jQuery(function ($) {
  $('.logestay-hero-slider').each(function () {
    var $slider = $(this);
    var $slides = $slider.find('.logestay-hero-slide');
    var $prev = $slider.find('.logestay-hero-prev');
    var $next = $slider.find('.logestay-hero-next');
    var $dotsWrap = $slider.find('.logestay-hero-dots');

    if (!$slides.length) return;

    var autoplay = String($slider.data('autoplay')) === '1';
    var interval = parseInt($slider.data('interval'), 10) || 6000;
    var current = 0;
    var timer = null;

    // Build dots dynamically
    $dotsWrap.empty();
    $slides.each(function (i) {
      var $btn = $('<button/>', {
        type: 'button',
        class:
          'logestay-hero-dot w-2 h-2 rounded-full transition-all duration-300 bg-white/50 hover:bg-white/75',
        'aria-label': 'Go to image ' + (i + 1),
        'data-index': i
      });
      $dotsWrap.append($btn);
    });

    var $dots = $dotsWrap.find('.logestay-hero-dot');

    function setActiveDot(index) {
      $dots.removeClass('bg-white w-6').addClass('bg-white/50 w-2');
      $dots.eq(index).removeClass('bg-white/50 w-2').addClass('bg-white w-6');
    }

    function showSlide(index) {
      if (index < 0) index = $slides.length - 1;
      if (index >= $slides.length) index = 0;

      // Fade transition using your opacity classes
      $slides.removeClass('opacity-100').addClass('opacity-0');
      $slides.eq(index).removeClass('opacity-0').addClass('opacity-100');

      current = index;
      setActiveDot(current);
    }

    function nextSlide() {
      showSlide(current + 1);
    }

    function prevSlide() {
      showSlide(current - 1);
    }

    function startAutoplay() {
      if (!autoplay) return;
      stopAutoplay();
      timer = setInterval(nextSlide, interval);
    }

    function stopAutoplay() {
      if (timer) {
        clearInterval(timer);
        timer = null;
      }
    }

    // Init
    showSlide(0);
    startAutoplay();

    // Events
    $next.on('click', function (e) {
      e.preventDefault();
      nextSlide();
      startAutoplay(); // reset timer after manual nav
    });

    $prev.on('click', function (e) {
      e.preventDefault();
      prevSlide();
      startAutoplay();
    });

    $dotsWrap.on('click', '.logestay-hero-dot', function (e) {
      e.preventDefault();
      var idx = parseInt($(this).data('index'), 10);
      showSlide(idx);
      startAutoplay();
    });

    // Optional: pause on hover (nice UX)
    $slider.on('mouseenter', stopAutoplay);
    $slider.on('mouseleave', startAutoplay);
  });


   lucide.createIcons();

});



(function () {
  // Helper: lock/unlock body scroll when modal open
  function lockScroll(lock) {
    document.documentElement.style.overflow = lock ? "hidden" : "";
    document.body.style.overflow = lock ? "hidden" : "";
  }

  // Build modal markup (same as your overlay)
  function buildModal() {
    const modal = document.createElement("div");
    modal.className =
      "logestay-lightbox fixed inset-0 bg-black/95 z-50 flex items-center justify-center p-4";
    modal.innerHTML = `
      <button class="logestay-lb-close absolute top-4 right-4 bg-white/10 backdrop-blur-sm p-3 rounded-full hover:bg-white/20 transition-all duration-300 z-10" aria-label="Fermer">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x w-6 h-6 text-white">
          <path d="M18 6 6 18"></path><path d="m6 6 12 12"></path>
        </svg>
      </button>

      <div class="relative w-full h-full flex items-center justify-center max-w-7xl mx-auto">
        <img class="logestay-lb-img max-w-full max-h-full object-contain rounded-lg" alt="" src="" />

        <button class="logestay-lb-prev absolute left-4 top-1/2 -translate-y-1/2 bg-white/10 backdrop-blur-sm p-4 rounded-full hover:bg-white/20 transition-all duration-300" aria-label="Image précédente">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left w-8 h-8 text-white">
            <path d="m15 18-6-6 6-6"></path>
          </svg>
        </button>

        <button class="logestay-lb-next absolute right-4 top-1/2 -translate-y-1/2 bg-white/10 backdrop-blur-sm p-4 rounded-full hover:bg-white/20 transition-all duration-300" aria-label="Image suivante">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-8 h-8 text-white">
            <path d="m9 18 6-6-6-6"></path>
          </svg>
        </button>

        <div class="logestay-lb-dots absolute bottom-8 left-1/2 -translate-x-1/2 flex gap-3"></div>
        <div class="logestay-lb-count absolute bottom-8 right-8 bg-black/50 backdrop-blur-sm px-4 py-2 rounded-full text-white text-sm"></div>
      </div>
    `;
    modal.style.display = "none";
    document.body.appendChild(modal);
    return modal;
  }

  const modal = buildModal();
  const modalImg = modal.querySelector(".logestay-lb-img");
  const modalDots = modal.querySelector(".logestay-lb-dots");
  const modalCount = modal.querySelector(".logestay-lb-count");

  let currentImages = [];
  let currentIndex = 0;

  function renderModal() {
    const total = currentImages.length;
    if (!total) return;

    // set image
    modalImg.src = currentImages[currentIndex].src;
    modalImg.alt = currentImages[currentIndex].alt || "";

    // count
    modalCount.textContent = `${currentIndex + 1} / ${total}`;

    // dots
    modalDots.innerHTML = "";
    for (let i = 0; i < total; i++) {
      const btn = document.createElement("button");
      btn.type = "button";
      btn.setAttribute("aria-label", `Aller à l'image ${i + 1}`);
      btn.className =
        "h-2 rounded-full transition-all duration-300 " +
        (i === currentIndex ? "bg-white w-8" : "bg-white/50 hover:bg-white/75 w-2");
      btn.addEventListener("click", () => {
        currentIndex = i;
        renderModal();
      });
      modalDots.appendChild(btn);
    }
  }

  function openModal(images, startIndex) {
    currentImages = images;
    currentIndex = Math.max(0, Math.min(startIndex || 0, images.length - 1));
    renderModal();
    modal.style.display = "flex";
    lockScroll(true);
  }

  function closeModal() {
    modal.style.display = "none";
    lockScroll(false);
    currentImages = [];
    currentIndex = 0;
  }

  function next() {
    if (!currentImages.length) return;
    currentIndex = (currentIndex + 1) % currentImages.length;
    renderModal();
  }

  function prev() {
    if (!currentImages.length) return;
    currentIndex = (currentIndex - 1 + currentImages.length) % currentImages.length;
    renderModal();
  }

  // Modal events
  modal.querySelector(".logestay-lb-close").addEventListener("click", closeModal);
  modal.querySelector(".logestay-lb-next").addEventListener("click", next);
  modal.querySelector(".logestay-lb-prev").addEventListener("click", prev);

  // Click outside image panel closes (only if click background)
  modal.addEventListener("click", (e) => {
    if (e.target === modal) closeModal();
  });

  // ESC + arrow keys
  document.addEventListener("keydown", (e) => {
    if (modal.style.display !== "flex") return;
    if (e.key === "Escape") closeModal();
    if (e.key === "ArrowRight") next();
    if (e.key === "ArrowLeft") prev();
  });

  // ===== Card slider state (stacked images) =====
  // We’ll keep active image on the card by toggling opacity classes.
  function setCardIndex(galleryEl, index) {
    const imgs = Array.from(galleryEl.querySelectorAll(".aspect-video img"));
    if (!imgs.length) return;

    const total = imgs.length;
    const i = ((index % total) + total) % total;

    imgs.forEach((img, idx) => {
      // Your classes already use opacity-100 / opacity-0
      img.classList.toggle("opacity-100", idx === i);
      img.classList.toggle("opacity-0", idx !== i);
    });

    galleryEl.dataset.index = String(i);

    // If you have dot buttons inside the card, update them too (optional)
    const dotsWrap = galleryEl.querySelector(".bottom-4.left-1\\/2");
    if (dotsWrap) {
      const dots = Array.from(dotsWrap.querySelectorAll("button"));
      dots.forEach((d, idx) => {
        d.classList.toggle("bg-white", idx === i);
        d.classList.toggle("bg-white/50", idx !== i);
        d.classList.toggle("w-6", idx === i);
        d.classList.toggle("w-2", idx !== i);
      });
    }
  }

  // Delegate clicks (works for many cards)
  document.addEventListener("click", (e) => {
    const gallery = e.target.closest(".logestay-card-gallery");
    if (!gallery) return;

    const imgs = Array.from(gallery.querySelectorAll(".aspect-video img"));
    if (!imgs.length) return;

    const current = parseInt(gallery.dataset.index || "0", 10) || 0;

    // Card next/prev buttons
    if (e.target.closest('[aria-label="Image suivante"]')) {
      e.preventDefault();
      setCardIndex(gallery, current + 1);
      return;
    }

    if (e.target.closest('[aria-label="Image précédente"]')) {
      e.preventDefault();
      setCardIndex(gallery, current - 1);
      return;
    }

    // Card dots
    const dotBtn = e.target.closest('[aria-label^="Aller à l\'image"]');
    if (dotBtn) {
      e.preventDefault();
      const label = dotBtn.getAttribute("aria-label") || "";
      const m = label.match(/(\d+)/);
      if (m) setCardIndex(gallery, parseInt(m[1], 10) - 1);
      return;
    }

    // Click on the image area => open modal at current index
    const clickedImageArea = e.target.closest(".aspect-video");
    if (clickedImageArea) {
      e.preventDefault();
      openModal(imgs, current);
      return;
    }
  });

  // Init all galleries default index 0
  document.querySelectorAll(".logestay-card-gallery").forEach((g) => setCardIndex(g, 0));
})();



(function () {
  var track = document.getElementById("logestay-reviews-track");
  var viewport = document.getElementById("logestay-reviews-viewport");
  var prevBtn = document.getElementById("logestay-reviews-prev");
  var nextBtn = document.getElementById("logestay-reviews-next");

  if (!track || !viewport || !prevBtn || !nextBtn) return;

  var index = 0;

  function slidesCount() {
    // Each slide = a direct child of track (min-w-full)
    return track.children.length;
  }

  function goTo(i) {
    var total = slidesCount();
    if (total <= 1) return;

    if (i < 0) i = total - 1;
    if (i >= total) i = 0;

    index = i;
    track.style.transform = "translateX(" + (-index * 100) + "%)";
  }

  prevBtn.addEventListener("click", function () {
    goTo(index - 1);
  });

  nextBtn.addEventListener("click", function () {
    goTo(index + 1);
  });

  // Ensure lucide renders <i> to svg (if needed)
  if (window.lucide && window.lucide.createIcons) {
    window.lucide.createIcons();
  }

  goTo(0);
})();




jQuery(function ($) {
  function renderLucide() {
    if (window.lucide && window.lucide.createIcons) {
      window.lucide.createIcons();
    }
  }

  $(document).on("click", ".logestay-faq-btn", function () {
    var $btn = $(this);
    var $item = $btn.closest(".logestay-faq-item");
    var $panel = $item.find(".logestay-faq-panel");
    var $icon = $item.find(".logestay-faq-icon");
    var $wrap = $btn.closest(".logestay-faq");

    var isOpen = $btn.attr("aria-expanded") === "true";

    // if accordion mode: close others
    if ($wrap.data("accordion") === true || $wrap.data("accordion") === "true") {
      $wrap.find(".logestay-faq-btn[aria-expanded='true']").not($btn).each(function () {
        var $b = $(this);
        var $it = $b.closest(".logestay-faq-item");
        $b.attr("aria-expanded", "false");
        $it.find(".logestay-faq-panel").attr("aria-hidden", "true").css("max-height", 0);
        $it.find(".logestay-faq-icon").attr("data-lucide", "plus");
      });
    }

    if (!isOpen) {
      // open
      $btn.attr("aria-expanded", "true");
      $panel.attr("aria-hidden", "false");

      // set max-height to actual content height for smooth animation
      var innerHeight = $panel.children().outerHeight(true);
      $panel.css("max-height", innerHeight + "px");

      $icon.attr("data-lucide", "minus");
    } else {
      // close
      $btn.attr("aria-expanded", "false");
      $panel.attr("aria-hidden", "true").css("max-height", 0);
      $icon.attr("data-lucide", "plus");
    }

    renderLucide();
  });

  // ensure icons render on load
  renderLucide();
});



jQuery(function ($) {
  function renderLucide(scope) {
    if (window.lucide && window.lucide.createIcons) {
      window.lucide.createIcons(scope ? { root: scope } : undefined);
    }
  }


  function getModalHtml() {
    return `
        <div id="logestay-ct-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div class="absolute inset-0 bg-black/60 backdrop-blur-sm logestay-ct-backdrop"></div>

          <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between rounded-t-2xl">
              <h2 class="text-2xl font-bold text-gray-900">${logestayc.i18n.ct_title}</h2>
              <button type="button" class="p-2 hover:bg-gray-100 rounded-full transition-colors duration-200 logestay-ct-close" aria-label="${logestayc.i18n.close}">
                <i data-lucide="x" class="w-6 h-6 text-gray-700"></i>
              </button>
            </div>

            <div class="p-6">
              <p class="text-gray-600 mb-6 leading-relaxed">
                ${logestayc.i18n.ct_intro}
              </p>

              <div class="logestay-ct-body">
                <form class="space-y-6 logestay-ct-form" novalidate>
                  <div>
                    <label for="ct_firstName" class="block text-sm font-semibold text-gray-900 mb-2">${logestayc.i18n.ct_first_name_label}</label>
                    <input type="text" id="ct_firstName" name="firstName" placeholder="${logestayc.i18n.ct_first_name_ph}"
                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all duration-200"
                      required>
                  </div>

                  <div>
                    <label for="ct_email" class="block text-sm font-semibold text-gray-900 mb-2">${logestayc.i18n.ct_email_label}</label>
                    <input type="email" id="ct_email" name="email" placeholder="${logestayc.i18n.ct_email_ph}"
                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all duration-200"
                      required>
                  </div>

                  <div>
                    <label for="ct_subject" class="block text-sm font-semibold text-gray-900 mb-2">${logestayc.i18n.ct_subject_label}</label>
                    <select id="ct_subject" name="subject"
                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all duration-200"
                      required>
                      <option value="">${logestayc.i18n.ct_subject_placeholder}</option>
                      <option value="pre-booking">${logestayc.i18n.ct_subject_prebooking}</option>
                      <option value="booking-issue">${logestayc.i18n.ct_subject_booking_issue}</option>
                      <option value="during-stay">${logestayc.i18n.ct_subject_during_stay}</option>
                      <option value="other">${logestayc.i18n.ct_subject_other}</option>
                    </select>
                  </div>

                  <div>
                    <label for="ct_message" class="block text-sm font-semibold text-gray-900 mb-2">${logestayc.i18n.ct_message_label}</label>
                    <textarea id="ct_message" name="message" placeholder="${logestayc.i18n.ct_message_ph}"
                      rows="5"
                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all duration-200 resize-none"
                      required></textarea>
                  </div>

                  <div class="flex items-start gap-3">
                    <input type="checkbox" id="ct_gdprConsent" name="gdprConsent"
                      class="mt-1 w-5 h-5 border-gray-300 rounded focus:ring-2 focus:ring-gray-900"
                      required>
                    <label for="ct_gdprConsent" class="text-sm text-gray-600">
                      ${logestayc.i18n.ct_gdpr_label}
                    </label>
                  </div>

                  <div class="bg-gray-50 rounded-xl p-4 flex items-start gap-3">
                    <i data-lucide="clock" class="w-5 h-5 text-gray-700 flex-shrink-0 mt-0.5"></i>
                    <p class="text-sm text-gray-700">${logestayc.i18n.ct_response_time}</p>
                  </div>

                  <div class="flex gap-4 pt-4">
                    <button type="button"
                      class="flex-1 px-6 py-3 border border-gray-300 rounded-xl font-semibold text-gray-700 hover:bg-gray-50 transition-all duration-200 logestay-ct-cancel">
                      ${logestayc.i18n.cancel}
                    </button>

                    <button type="submit" disabled
                      class="flex-1 px-6 py-3 bg-gray-900 text-white rounded-xl font-semibold hover:bg-gray-800 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 logestay-ct-submit">
                      <i data-lucide="send" class="w-5 h-5"></i>
                      ${logestayc.i18n.send}
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      `;
  }

  function getSuccessHtml() {
    return `
        <div class="bg-green-50 border border-green-200 rounded-xl p-6 text-center">
          <i data-lucide="check-circle" class="w-12 h-12 text-green-600 mx-auto mb-4"></i>
          <h3 class="text-xl font-semibold text-green-900 mb-2">
            ${logestayc.i18n.message_sent_title}
          </h3>
          <p class="text-green-700">
            ${logestayc.i18n.message_sent_desc}
          </p>
        </div>
      `;
  }

  function openModal() {
    // if already open, don't duplicate
    if ($("#logestay-ct-modal").length) return;

    $("body").append(getModalHtml());
    $("body").addClass("overflow-hidden");

    // render lucide icons inside modal
    renderLucide(document.getElementById("logestay-ct-modal"));

    // focus first field
    setTimeout(function () {
      $("#ct_firstName").trigger("focus");
    }, 50);
  }

  function closeModal() {
    $("#logestay-ct-modal").remove();
    $("body").removeClass("overflow-hidden");
  }

  function isEmailValid(email) {
    // simple and enough for UI validation
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }

  function validateForm($form) {
    var firstName = ($form.find("#ct_firstName").val() || "").trim();
    var email = ($form.find("#ct_email").val() || "").trim();
    var subject = ($form.find("#ct_subject").val() || "").trim();
    var message = ($form.find("#ct_message").val() || "").trim();
    var consent = $form.find("#ct_gdprConsent").is(":checked");

    if (!firstName) return false;
    if (!email || !isEmailValid(email)) return false;
    if (!subject) return false;
    if (!message) return false;
    if (!consent) return false;

    return true;
  }

  // Open
  $(document).on("click", ".open_ct_form", function (e) {
    e.preventDefault();
    openModal();
  });

  // Close: X button / Cancel
  $(document).on("click", ".logestay-ct-close, .logestay-ct-cancel", function (e) {
    e.preventDefault();
    closeModal();
  });

  // Close: click backdrop
  $(document).on("click", ".logestay-ct-backdrop", function () {
    closeModal();
  });

  // Close: ESC
  $(document).on("keydown", function (e) {
    if (e.key === "Escape" && $("#logestay-ct-modal").length) {
      closeModal();
    }
  });

  // Enable submit when valid
  $(document).on("input change", ".logestay-ct-form input, .logestay-ct-form select, .logestay-ct-form textarea", function () {
    var $form = $(this).closest(".logestay-ct-form");
    var ok = validateForm($form);

    $form.find(".logestay-ct-submit").prop("disabled", !ok);
  });

  // Submit: hide form and show success
  $(document).on("submit", ".logestay-ct-form", function (e) {
    e.preventDefault();

    var $form = $(this);
    if (!validateForm($form)) {
      $form.find(".logestay-ct-submit").prop("disabled", true);
      return;
    }

    var $btn = $form.find(".logestay-ct-submit");
    $btn.prop("disabled", true).addClass("opacity-60");

    var payload = {
      action: "logestay_submit_support_form",
      nonce: (window.logestay && logestayc.nonce) ? logestayc.nonce : "",
      firstName: ($form.find("#ct_firstName").val() || "").trim(),
      email: ($form.find("#ct_email").val() || "").trim(),
      subject: ($form.find("#ct_subject").val() || "").trim(),
      message: ($form.find("#ct_message").val() || "").trim(),
      gdprConsent: $form.find("#ct_gdprConsent").is(":checked") ? 1 : 0
    };


    $.ajax({
      url: logestayc.ajaxUrl,
      type: "POST",
      dataType: "json",
      data: payload
    })
      .done(function (res) {
        if (res && res.success) {
          var $body = $form.closest(".logestay-ct-body");
          $body.html(getSuccessHtml());
          renderLucide($body.get(0));
        } else {
          alert(
            (res && res.data && res.data.message)
              ? res.data.message
              : logestayc.i18n.generic_error
          );
          $btn.prop("disabled", false).removeClass("opacity-60");
        }
      })
      .fail(function () {
        alert(logestayc.i18n.network_error);
        $btn.prop("disabled", false).removeClass("opacity-60");
      });
  });
});


jQuery(function ($) {
  // Toggle language dropdown
  $(document).on("click", ".logestay-lang-btn", function (e) {
    e.preventDefault();

    const $wrap = $(this).closest(".logestay-lang");
    const $menu = $wrap.find(".logestay-lang-menu");
    const $chev = $wrap.find(".logestay-lang-chevron");

    const isOpen = !$menu.hasClass("hidden");

    // close all first
    $(".logestay-lang-menu").addClass("hidden");
    $(".logestay-lang-chevron").removeClass("rotate-180");
    $(".logestay-lang-btn").attr("aria-expanded", "false");

    // open current if it was closed
    if (!isOpen) {
      $menu.removeClass("hidden");
      $chev.addClass("rotate-180");
      $(this).attr("aria-expanded", "true");
    }
  });

  // Close when clicking outside
  $(document).on("click", function (e) {
    if ($(e.target).closest(".logestay-lang").length) return;
    $(".logestay-lang-menu").addClass("hidden");
    $(".logestay-lang-chevron").removeClass("rotate-180");
    $(".logestay-lang-btn").attr("aria-expanded", "false");
  });

  // Close on ESC
  $(document).on("keydown", function (e) {
    if (e.key === "Escape") {
      $(".logestay-lang-menu").addClass("hidden");
      $(".logestay-lang-chevron").removeClass("rotate-180");
      $(".logestay-lang-btn").attr("aria-expanded", "false");
    }
  });

  // If you’re using Lucide i-tags, refresh icons after DOM ready
  if (window.lucide && window.lucide.createIcons) {
    window.lucide.createIcons();
  }
});