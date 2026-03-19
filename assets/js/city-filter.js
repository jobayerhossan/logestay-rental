jQuery(function ($) {

  function initNearbySlider($section) {
    const $wrap = $section.find("#js-nearby-wrap");
    const $prev = $section.find(".js-nearby-prev");
    const $next = $section.find(".js-nearby-next");

    if (!$wrap.length) return;

    function getStep() {
      const $first = $wrap.children().first();
      if (!$first.length) return 0;
      return $first.outerWidth(true) || 0; // card width + gap
    }

    function setBtnState() {
      const el = $wrap.get(0);
      if (!el) return;

      const maxScroll = el.scrollWidth - el.clientWidth;
      const left = el.scrollLeft;

      // small tolerance to avoid float issues
      const atStart = left <= 2;
      const atEnd = left >= (maxScroll - 2);

      $prev.prop("disabled", atStart);
      $next.prop("disabled", atEnd);

      // toggle disabled UI classes (match your style)
      $prev.toggleClass("opacity-50 cursor-not-allowed bg-gray-100", atStart)
           .toggleClass("bg-white hover:bg-gray-50 hover:shadow-lg", !atStart);

      $next.toggleClass("opacity-50 cursor-not-allowed bg-gray-100", atEnd)
           .toggleClass("bg-white hover:bg-gray-50 hover:shadow-lg", !atEnd);
    }

    function scrollByStep(dir) {
      const step = getStep();
      if (!step) return;

      const el = $wrap.get(0);
      const target = el.scrollLeft + (dir * step);

      $wrap.stop().animate({ scrollLeft: target }, 320, setBtnState);
    }

    $prev.off("click.logestayNearby").on("click.logestayNearby", function (e) {
      e.preventDefault();
      if ($(this).prop("disabled")) return;
      scrollByStep(-1);
    });

    $next.off("click.logestayNearby").on("click.logestayNearby", function (e) {
      e.preventDefault();
      if ($(this).prop("disabled")) return;
      scrollByStep(1);
    });

    // Update state on scroll + resize
    $wrap.off("scroll.logestayNearby").on("scroll.logestayNearby", function () {
      setBtnState();
    });

    $(window).off("resize.logestayNearby").on("resize.logestayNearby", function () {
      setBtnState();
    });

    // Initial
    setBtnState();
  }

  $(function () {
    $(".nearby_section").each(function () {
      initNearbySlider($(this));
    });
  });

  function setSelectedCityUI($btn) {
    // Remove "selected" state from all
    $('.city_item')
      .removeClass('ring-4 ring-amber-500 shadow-2xl city_item_selected')
      .find('.selected_item')
      .addClass('opacity-0')
      .removeClass('opacity-100');

    // Add to clicked
    $btn.addClass('ring-4 ring-amber-500 shadow-2xl city_item_selected');
    $btn.find('.selected_item')
      .removeClass('opacity-0')
      .addClass('opacity-100');
  }

  function scrollToApartments() {
	  var $target = $('#apartments');
	  if (!$target.length) return;

	  $('html, body').animate({
	    scrollTop: $target.offset().top - 40 // small offset for header
	  }, 600);
	}

  function loadCityListings(cityId) {
    var $wrap = $('#logestay-listings-wrap');
    var $section = $('#apartments');

    $section.attr('data-selected-city', cityId);
    $wrap.addClass('opacity-60 pointer-events-none');

    return $.ajax({
      url: logstaycity.ajax_url,
      type: 'POST',
      dataType: 'json',
      data: {
        action: 'logestay_get_listings_by_city',
        nonce: logstaycity.nonce,
        city_id: cityId
      }
    }).done(function (res) {
      if (!res || !res.success) return;

      $('.city_title').text(res.data.city_title || '');
      $wrap.html(res.data.html || '');
      logestayRenderLucide("#logestay-listings-wrap");
    }).always(function () {
      $wrap.removeClass('opacity-60 pointer-events-none');
    });
  }

  function logestayRenderLucide(scope) {
    if (!window.lucide || !window.lucide.createIcons) return;

    // If scope is a selector or jQuery object
    var el = scope;

    if (typeof scope === "string") el = document.querySelector(scope);
    if (scope && scope.jquery) el = scope.get(0);

    // Best: only scan inside the updated area
    if (el) {
      // Replace icons inside el only
      window.lucide.createIcons({ root: el });
    } else {
      // Fallback: scan entire page
      window.lucide.createIcons();
    }
  }

  // Click handler
  $(document).on('click', '.city_item', function (e) {
    e.preventDefault();

    var $btn = $(this);
    var cityId = parseInt($btn.data('id'), 10);

    if (!cityId) return;

    // If already selected, do nothing
    var current = parseInt($('#apartments').attr('data-selected-city'), 10);
    if (current === cityId) {
      setSelectedCityUI($btn); // still ensure UI correct
      return;
    }
    scrollToApartments();

    setSelectedCityUI($btn);
    loadCityListings(cityId);


      // (B) call AJAX for location+nearby
    $.ajax({
      url: logestay.ajaxUrl,
      type: "POST",
      dataType: "json",
      data: {
        action: "logestay_get_city_location",
        nonce: logestay.nonce,
        city_id: cityId
      }
    }).done(function (res) {
      if (!res || !res.success) return;

      var data = res.data || {};

      // 1) Title
      $(".js-city-location-title").text(data.city_title || "");

      // 2) Map iframe + open link
      if (data.map_embed) $("#js-city-map-iframe").attr("src", data.map_embed);
      if (data.map_open) $("#js-city-map-open").attr("href", data.map_open);

      // 3) Nearby items
      if (Array.isArray(data.nearby)) {
        $('.nearby_section').show();
        $("#js-nearby-wrap").html(buildNearbyHtml(data.nearby));

      }else{
        $('.nearby_section').hide();
      }

      // (C) scroll to section (your previous request)
      var target = document.getElementById("apartments");
      if (target) target.scrollIntoView({ behavior: "smooth", block: "start" });

      $(".nearby_section").each(function () {
        initNearbySlider($(this));
      });
      logestayRenderLucide("#logestay-listings-wrap");

      console.log('hello');
      

    });


  });


  function buildNearbyHtml(items) {
    var html = "";

    items.forEach(function (it) {
      var img = it.image || "https://via.placeholder.com/800x500?text=Nearby";
      var title = escapeHtml(it.title || "");
      var subtitle = escapeHtml(it.subtitle || "");
      var distance = escapeHtml(it.distance || "");
      var url = it.url || "#";

      html += `
        <div class="flex-shrink-0 w-[85%] sm:w-[70%] md:w-80 snap-center group/card">
          <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 h-full">
            <div class="relative h-48 overflow-hidden">
              <img src="${img}" alt="${title}" class="w-full h-full object-cover group-hover/card:scale-110 transition-transform duration-700">
              <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
            </div>
            <div class="p-6">
              <h4 class="text-xl font-bold text-gray-900 mb-2">${title}</h4>
              <p class="text-gray-600 mb-4">${subtitle}</p>
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500">${distance}</span>
                <a href="${url}" target="_blank" rel="noopener noreferrer"
                   class="flex items-center gap-2 text-emerald-600 hover:text-emerald-700 font-semibold text-sm transition-colors duration-300 group/link">
                  <span>${logstaycity.i18n.itinerary}</span>
                  <i data-lucide="navigation" class="w-4 h-4 group-hover/link:translate-x-1 transition-transform duration-300"></i>
                </a>
              </div>
            </div>
          </div>
        </div>
      `;
    });

    return html;
  }

  function escapeHtml(str) {
    return String(str || "")
      .replaceAll("&", "&amp;")
      .replaceAll("<", "&lt;")
      .replaceAll(">", "&gt;")
      .replaceAll('"', "&quot;")
      .replaceAll("'", "&#039;");
  }

  function renderLucide(scopeEl) {
    if (window.lucide && window.lucide.createIcons) {
      window.lucide.createIcons({ root: scopeEl || document });
    }
  }



  
});


jQuery(function ($) {
  const $track = $("#logestay-city-mobile-track");
  const $dotsWrap = $("#logestay-city-mobile-dots");

  if (!$track.length || !$dotsWrap.length) return;

  const $items = $track.find(".city_item");
  const $wrappers = $track.children(); // each ".min-w-[85%] snap-center pt-1"
  const $dots = $dotsWrap.find(".logestay-city-dot");


  let currentIndex = 0;
  let ticking = false;
  let programmaticScroll = false;

  // --- Helpers ---
  function setDotActive(index) {
    $dots.each(function () {
      const $d = $(this);
      const i = parseInt($d.attr("data-index"), 10);

      if (i === index) {
        $d.removeClass("w-2 bg-gray-300 hover:bg-gray-400")
          .addClass("w-8 bg-amber-500");
      } else {
        $d.removeClass("w-8 bg-amber-500")
          .addClass("w-2 bg-gray-300 hover:bg-gray-400");
      }
    });
  }

  function setSelectedUI(index) {

    $items.each(function () {
      const $btn  = $(this);
      const $wrap = $btn.parent();           // wrapper is direct parent in your markup
      const i     = $wrappers.index($wrap);  // actual index in the track

      if (i === index) {
        $btn.addClass("ring-4 ring-amber-500 shadow-2xl");
        $btn.find(".absolute.top-4.right-4")
            .removeClass("opacity-0")
            .addClass("opacity-100");
      } else {
        $btn.removeClass("ring-4 ring-amber-500 shadow-2xl");
        $btn.find(".absolute.top-4.right-4")
            .removeClass("opacity-100")
            .addClass("opacity-0");
      }
    });
  }

  function scrollToApartments() {
    const el = document.getElementById("apartments");
    if (!el) return;
    el.scrollIntoView({ behavior: "smooth", block: "start" });
  }

  function centerIndex(index, smooth = true) {
    const track = $track.get(0);
    const $wrap = $wrappers.eq(index);
    if (!$wrap.length) return;

    const wrapEl = $wrap.get(0);

    // Center the wrapper in the track
    const trackWidth = track.clientWidth;
    const wrapWidth = wrapEl.offsetWidth;
    const wrapLeft = wrapEl.offsetLeft;

    const targetLeft = wrapLeft - (trackWidth - wrapWidth) / 2;

    programmaticScroll = true;
    if (smooth && "scrollTo" in track) {
      track.scrollTo({ left: targetLeft, behavior: "smooth" });
    } else {
      track.scrollLeft = targetLeft;
    }

    // allow scroll handler again after animation settles
    setTimeout(() => {
      programmaticScroll = false;
    }, 350);
  }

  function getCenteredIndex() {
    const track = $track.get(0);
    const center = track.scrollLeft + track.clientWidth / 2;

    let bestIndex = 0;
    let bestDist = Infinity;

    $wrappers.each(function (idx) {
      const el = this;
      const elCenter = el.offsetLeft + el.offsetWidth / 2;
      const dist = Math.abs(center - elCenter);
      if (dist < bestDist) {
        bestDist = dist;
        bestIndex = idx;
      }
    });

    return bestIndex;
  }

  function selectIndex(index, shouldScrollApartments = true) {
    currentIndex = index;

    setSelectedUI(index);
    setDotActive(index);
    centerIndex(index, true);

    if (shouldScrollApartments) {
      setTimeout(scrollToApartments, 300);
    }
  }

  // --- Initial sync (based on your existing ring/selected)
  const $preSelected = $items.filter(".ring-4");
  if ($preSelected.length) {
    const idx = parseInt($preSelected.first().attr("data-index"), 10);
    if (!isNaN(idx)) currentIndex = idx;
  }
  setSelectedUI(currentIndex);
  setDotActive(currentIndex);

  // --- Dot click
  $dots.on("click", function () {
    const idx = parseInt($(this).attr("data-index"), 10);
    if (isNaN(idx)) return;
    selectIndex(idx, true);
  });

  // --- City click (your “city_item already scrolls to #apartments” -> now also center + dot sync)
  $(document).on("click", "#logestay-city-mobile-track .city_item", function (e) {
    e.preventDefault();

    // Find the wrapper (.min-w-[85%]) that contains this city
    const $wrap = $(this).parent(); 
    const idx = $wrappers.index($wrap);
 
    if (idx < 0) return;

    selectIndex(idx, true);
  });

  // --- Swipe/scroll updates dots + selected when user scrolls manually
  $track.on("scroll", function () {
    if (programmaticScroll) return;

    if (!ticking) {
      ticking = true;
      window.requestAnimationFrame(function () {
        ticking = false;

        const idx = getCenteredIndex();
        if (idx !== currentIndex) {
          currentIndex = idx;
          setSelectedUI(idx);
          setDotActive(idx);
        }
      });
    }
  });
});