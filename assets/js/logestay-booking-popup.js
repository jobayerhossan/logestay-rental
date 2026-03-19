/*!
 * LOGESTAY Availability Popup (jQuery)
 * - Opens on .view_av click (uses data-id as listing id)
 * - Pixel-style markup (Tailwind classes) + Lucide via <i data-lucide="...">
 * - Calendar + guests + payment selection (frontend only for now)
 *
 * Requires:
 * - jQuery
 * - lucide (already loaded on your site)
 * - logestay.ajaxUrl + logestay.nonce localized (already done)
 */

(function ($) {
  "use strict";

  console.log(logestay);

  // ----------------------------
  // Utils
  // ----------------------------
  const pad2 = (n) => String(n).padStart(2, "0");
  const fmtYmd = (d) => `${d.getFullYear()}-${pad2(d.getMonth() + 1)}-${pad2(d.getDate())}`;
  const cloneDate = (d) => new Date(d.getFullYear(), d.getMonth(), d.getDate());
  const isSameDay = (a, b) => a && b && a.getFullYear() === b.getFullYear() && a.getMonth() === b.getMonth() && a.getDate() === b.getDate();
  const isBeforeDay = (a, b) => cloneDate(a).getTime() < cloneDate(b).getTime();
  const isAfterDay = (a, b) => cloneDate(a).getTime() > cloneDate(b).getTime();
  const daysInMonth = (y, m) => new Date(y, m + 1, 0).getDate();
  const startOfMonth = (y, m) => new Date(y, m, 1);
  const endOfMonth = (y, m) => new Date(y, m, daysInMonth(y, m));
  const addMonths = (d, n) => new Date(d.getFullYear(), d.getMonth() + n, 1);
  function monthLabel(d) {
    if (logestay.i18n?.months) {
      const m = logestay.i18n.months[d.getMonth()];
      return `${m} ${d.getFullYear()}`;
    }

    // fallback
    return d.toLocaleString(undefined, { month: "long", year: "numeric" });
  }

  function fullDateLabel(d) {
    if (!d) return "";

    if (logestay.i18n?.months) {
      const month = logestay.i18n.months[d.getMonth()];
      return `${d.getDate()} ${month} ${d.getFullYear()}`;
    }

    // fallback
    return d.toLocaleDateString(undefined, {
      day: "numeric",
      month: "long",
      year: "numeric"
    });
  }

  function dayLabels() {
    return window.logestay.i18n?.days_short || ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];
  }

  function safeCallLucide() {
    try {
      if (window.lucide && typeof window.lucide.createIcons === "function") {
        window.lucide.createIcons();
      } else if (window.lucide && typeof window.lucide.replace === "function") {
        window.lucide.replace();
      }
    } catch (e) {}
  }

  function isPaymentEnabled(key) {
    const map = {
      card:   'logestay_payments_enabled_card',
      paypal: 'logestay_payments_enabled_paypal',
      bank:   'logestay_payments_enabled_bank',
      cash:   'logestay_payments_enabled_cash',
      link:   'logestay_payments_enabled_link',
    };

    const settingKey = map[key];
    if (!settingKey) return false;

    return String(logestay?.settings?.[settingKey]) === '1';
  }

  function tp(key, fallback) {
    return logestay?.i18n?.payments?.[key] || fallback;
  }

  const instantBadge  = tp("badge_instant", "Immediate");
  const deferredBadge = tp("badge_deferred", "Delayed");

  const allPayments = [
    { 
      key: "card",
      label: tp("card", "Credit card"),
      subtitle: tp("card_sub", "Secure card payment - Instant confirmation"),
      badge: instantBadge,
      badgeType: "instant",
      icon: "credit-card"
    },
    { 
      key: "paypal",
      label: tp("paypal", "PayPal"),
      subtitle: tp("paypal_sub", "PayPal payment - Instant confirmation"),
      badge: instantBadge,
      badgeType: "instant",
      icon: "wallet"
    },
    { 
      key: "bank",
      label: tp("bank", "Bank transfer"),
      subtitle: tp("bank_sub", "Transfer - Validation after receipt"),
      badge: deferredBadge,
      badgeType: "deferred",
      icon: "building2"
    },
    { 
      key: "cash",
      label: tp("cash", "Cash payment"),
      subtitle: tp("cash_sub", "Payment on site - Owner validation required"),
      badge: deferredBadge,
      badgeType: "deferred",
      icon: "banknote"
    },
    { 
      key: "link",
      label: tp("link", "Payment link"),
      subtitle: tp("link_sub", "Link sent by email - Validation after payment"),
      badge: deferredBadge,
      badgeType: "deferred",
      icon: "link"
    },
  ];

  const payments = allPayments.filter(p => isPaymentEnabled(p.key));


  // ----------------------------
  // State
  // ----------------------------
  const state = {
    open: false,
    listingId: null,
    listingTitle: "Apartment",
    cityTitle: "City",
    monthBase: new Date(new Date().getFullYear(), new Date().getMonth(), 1),
    startDate: null,
    endDate: null,
    guests: { adults: 1, children: 0, pets: 0 },
    payment: null,
    payments: payments,
    partner: {
      airbnbEnabled: true,
      airbnbUrl: "",
      bookingEnabled: true,
      bookingUrl: "",
    },
    disabledDates: [],
    cleaningFee: 0,
  };

  // ----------------------------
  // Markup (CLASSES kept consistent)
  // ----------------------------


  function logestayRefreshLucide(scope) {
    try {
      if (!window.lucide) return;

      // run once immediately
      if (typeof window.lucide.createIcons === "function") {
        window.lucide.createIcons();
      } else if (typeof window.lucide.replace === "function") {
        window.lucide.replace();
      }

      // run again after DOM settles (Swiper/other scripts often change nodes)
      requestAnimationFrame(function () {
        if (typeof window.lucide.createIcons === "function") {
          window.lucide.createIcons();
        } else if (typeof window.lucide.replace === "function") {
          window.lucide.replace();
        }
      });

      // and a final delayed pass (covers slow re-render)
      setTimeout(function () {
        if (typeof window.lucide.createIcons === "function") {
          window.lucide.createIcons();
        } else if (typeof window.lucide.replace === "function") {
          window.lucide.replace();
        }
      }, 50);
    } catch (e) {}
  }

  function syncGuestState($root) {
    state.guest = state.guest || {};

    state.guest.name  = ($root.find(".logestay-input-name").val() || "").trim();
    state.guest.email = ($root.find(".logestay-input-email").val() || "").trim();
    state.guest.phone = ($root.find(".logestay-input-phone").val() || "").trim();
    state.guest.note  = ($root.find(".logestay-input-note").val() || "").trim();
  }

  function toYMD(d) {
    if (!d) return "";
    const dt = (d instanceof Date) ? d : new Date(d);
    if (isNaN(dt.getTime())) return "";
    const y = dt.getFullYear();
    const m = String(dt.getMonth() + 1).padStart(2, "0");
    const day = String(dt.getDate()).padStart(2, "0");
    return `${y}-${m}-${day}`;
  }

  function loadDisabledDates(listingId, done) {
    $.ajax({
      url: logestay.ajaxUrl,
      type: "POST",
      dataType: "json",
      data: {
        action: "logestay_get_disabled_dates",
        nonce: logestay.nonce,
        listing_id: listingId,
      },
      success(res) {
        if (res.success && Array.isArray(res.data.dates)) {
          state.disabledDates = res.data.dates;
        } else {
          state.disabledDates = [];
        }
        done && done();
      },
      error() {
        state.disabledDates = [];
        done && done();
      },
    });
  }

  /**
   * Creates/updates a booking HOLD in backend and returns booking_id.
   * Call this after dates/guests change and before confirm.
   */
  function ensureBookingHold(done) {
    const $root = $(".logestay-av-modal");
    syncGuestState($root);
    
    const payload = {
      action: "logestay_check_availability",   // <-- your backend function name
      nonce: logestay.nonce,
      booking_id: state.bookingId || 0,        // optional if you support update
      listing_id: state.listingId,
      start_date: fmtYmd(state.startDate),
      end_date: fmtYmd(state.endDate),
      guests: state.guests,                    // {adults, children, pets}
      guest: state.guest || {},                // {name,email,phone,note}
      payment: state.payment || "",            // card/bank/cash/link/paypal
    };

    $.ajax({
      url: logestay.ajaxUrl,
      type: "POST",
      dataType: "json",
      data: payload,
      success(res) {
        if (!res || !res.success || !res.data.booking_id) {
          done(new Error(res?.data?.message || "Could not create booking hold"));
          return;
        }

        state.bookingId = parseInt(res.data.booking_id, 10);

        // (optional) store computed totals from backend if you return them
        if (res.data.total_amount != null) state.totalAmount = parseFloat(res.data.total_amount) || 0;
        if (res.data.price_per_night != null) state.pricePerNight = parseFloat(res.data.price_per_night) || 0;
        if (res.data.currency) state.currency = res.data.currency;

        done(null, res.data);
      },
      error() {
        done(new Error("Server error"));
      },
    });
  }

  function modalTemplate() {
  return `
<div class="logestay-av-modal fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-end md:items-center justify-center p-4 animate-fadeIn" aria-hidden="true">

  <div class="logestay-av-panel bg-white rounded-t-3xl md:rounded-3xl w-full max-w-4xl max-h-[90vh] overflow-y-auto shadow-2xl animate-slideUp">

    <div class="sticky top-0 bg-white border-b border-gray-200 p-6 flex items-center justify-between rounded-t-3xl z-10">
      <div class="">
        <h3 class="logestay-av-title text-2xl font-bold text-gray-900">${escapeHtml(
          state.listingTitle
        )}</h3>
        <div class="flex items-center gap-2 mt-1">
          <i data-lucide="map-pin" class="w-4 h-4 text-gray-500"></i>
          <span class="logestay-av-city text-gray-600 text-sm font-medium">${escapeHtml(state.cityTitle)}</span>
        </div>
        <p class="text-gray-600 text-sm mt-1">${logestay.i18n.choose_date}</p>
      </div>

      <button type="button" class="logestay-av-close p-2 hover:bg-gray-100 rounded-full transition-colors" aria-label="${logestay.i18n.close || 'Close'}">
        <i data-lucide="x" class="w-6 h-6 text-gray-700"></i>
      </button>
    </div>

    <div class="p-6 space-y-6">

      <!-- Calendar Part --> 
      <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl p-6">
        <div class="flex items-center gap-3 mb-2"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar w-6 h-6 text-emerald-600"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path></svg><h4 class="text-lg font-semibold text-gray-900">${logestay.i18n.select_stay_dates || 'Select your stay dates'}</h4></div>

          <p class="text-sm text-emerald-800 mb-6 bg-emerald-100 rounded-lg p-3">
            ${logestay.i18n.date_ins}
          </p>

          <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl p-4 shadow-sm">
              <div class="flex items-center justify-between mb-4">
                <button type="button" class="logestay-cal-prev p-2 hover:bg-gray-100 rounded-lg transition-colors" aria-label="${logestay.i18n.prev_month || 'Previous month'}">
                  <span class="text-lg">←</span>
                </button>
                 <h5 class="logestay-cal-label-0 font-semibold text-gray-900 flex-1 text-center"></h5>
              </div>
              <div class="grid grid-cols-7 gap-1 logestay-cal-month logestay-cal-month-0">
              </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm">
              <div class="flex items-center justify-between mb-4">
                
                 <h5 class="logestay-cal-label-1 font-semibold text-gray-900 flex-1 text-center"></h5>
                 <button type="button" class="logestay-cal-next p-2 hover:bg-gray-100 rounded-lg transition-colors" aria-label="${logestay.i18n.next_month || 'Next month'}">
                  <span class="text-lg">→</span>
                </button>
              </div>
              <div class="grid grid-cols-7 gap-1 logestay-cal-month logestay-cal-month-1">
              </div>
            </div>
          </div>

          <div class="logestay-dates-summary hidden"></div>

      </div>

      <!-- Guest Selector -->
      <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6">
        <div class="flex items-center gap-3 mb-6">
          <i data-lucide="users" class="w-6 h-6 text-blue-600"></i>
          <h4 class="text-lg font-semibold text-gray-900">${logestay.i18n.number_of_guests || 'Number of guests'}</h4>
        </div>
        <div class="space-y-4">
             ${guestRow(logestay.i18n.guest_adults_label || "Adults (13+)", "adults", "users", "bg-blue-100", "text-blue-600", logestay.i18n.guest_age_note_adult)}
            ${guestRow(logestay.i18n.guest_children_label || "Children (2–12)", "children", "baby", "bg-purple-100", "text-purple-600", logestay.i18n.guest_age_note_child)}
            ${guestRow(logestay.i18n.guest_pets_label || "Pets", "pets", "paw-print", "bg-amber-100", "text-amber-600", logestay.i18n.guest_age_note_pet)}
        </div>
      </div>

      <!-- Payment Gateways --> 
      <div class="logestay-pay-wrap bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl p-6 hidden">
        <div class="flex items-center gap-3 mb-6">
          <i data-lucide="credit-card" class="w-6 h-6 text-blue-600"></i>
          <h4 class="text-lg font-semibold text-gray-900">${logestay.i18n.payment_method || 'Payment method'}</h4>
          
        </div>

        <div class="logestay-pay-list space-y-3"></div>

        <div class="logestay-pay-instructions mt-4 hidden"></div>

      </div>

      <!-- Order Summary -->
      <div class="logestay-summary hidden"></div>

      <!-- Guest Details -->
      <div class="logestay-guest-details hidden"></div>

      <!-- Partner buttons -->
      <div class="border-t border-gray-200 pt-6 ${
  (!state.partner.airbnbUrl?.trim() && !state.partner.bookingUrl?.trim())
    ? 'hidden'
    : ''
}">
        <p class="text-center text-sm text-gray-600 mb-4">${logestay.i18n.or_partners || 'or through our partners'}</p>
        <div class="grid md:grid-cols-2 gap-4">
          <a href="${escapeAttr(state.partner.airbnbUrl)}" class="logestay-partner-airbnb flex items-center justify-center gap-3 bg-[#FF5A5F] text-white py-4 px-6 rounded-xl font-semibold hover:bg-[#E0484D] transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-[1.02] ${state.partner.airbnbUrl ? "" : "hidden"}">
              <i data-lucide="external-link" class="w-5 h-5"></i>${logestay.i18n.airbnb || 'Réserver sur Airbnb'}
          </a>
          <a href="${escapeAttr(state.partner.bookingUrl)}" class="logestay-partner-booking ${state.partner.bookingUrl ? "" : "hidden"} flex items-center justify-center gap-3 bg-[#003580] text-white py-4 px-6 rounded-xl font-semibold hover:bg-[#002A66] transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-[1.02]">
            <i data-lucide="external-link" class="w-5 h-5"></i>
            ${logestay.i18n.booking || 'Réserver sur Booking'}
          </a>
        </div>
      </div>


        </div>


      </div>
    </div>
  </div>
</div>
    `;
}

  function guestRow(label, key, icon = 'users', iconparentclasses = 'bg-blue-100', iconclass = 'text-blue-600', note = '') {
  return `
    <div class="bg-white rounded-xl p-4 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-full ${iconparentclasses}  flex items-center justify-center">
          <i data-lucide="${icon}" class="w-5 h-5 ${iconclass}"></i>
        </div>
        <div>
          <p class="font-semibold text-gray-900">${escapeHtml(label)}</p>
          <p class="text-xs text-gray-500">${note || "13 years and older"}</p>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <button type="button" class="logestay-guest-minus w-10 h-10 rounded-full border-2 border-gray-300 hover:border-blue-500 disabled:border-gray-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center transition-colors group" data-key="${escapeAttr(
          key
        )}">
          <i data-lucide="minus" class="w-4 h-4 text-gray-600 group-hover:text-blue-600 group-disabled:text-gray-400"></i>
        </button>

        <span class="logestay-guest-val w-8 text-center font-semibold text-lg text-gray-900" data-key="${escapeAttr(
          key
        )}">${state.guests[key]}</span>

        <button type="button" class="logestay-guest-plus w-10 h-10 rounded-full border-2 border-gray-300 hover:border-blue-500 disabled:border-gray-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center transition-colors group" data-key="${escapeAttr(
          key
        )}">
          <i data-lucide="plus" class="w-4 h-4 text-gray-600 group-hover:text-blue-600 group-disabled:text-gray-400"></i>
        </button>
      </div>
    </div>
  `;
}

  function paymentRow(p) {
    const badgeClass =
      p.badgeType === "instant"
        ? "bg-green-100 text-green-700"
        : p.badgeType === "deferred"
        ? "bg-amber-100 text-amber-700"
        : "bg-gray-100 text-gray-800";

    return `
      <button type="button"
        class="logestay-pay-item w-full bg-white rounded-xl p-4 border-2 transition-all duration-200 text-left border-gray-200 hover:border-blue-300 hover:shadow-md"
        data-key="${escapeAttr(p.key)}">
        <div class="flex items-start gap-4">
          <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0 bg-gray-100">
            <i data-lucide="${escapeAttr(p.icon)}" class="w-6 h-6 text-gray-600"></i>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1">
              <span class="font-semibold text-gray-900">${escapeHtml(p.label)}</span>
              <span class="text-xs px-2 py-0.5 rounded-full ${badgeClass}">
                ${escapeHtml(p.badge)}
              </span>
            </div>
            <p class="text-sm text-gray-600">
              ${escapeHtml(p.subtitle)}
            </p>
          </div>
          <div class="checked_selected hidden w-6 h-6 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0">
            <i data-lucide="check" class="w-4 h-4 text-white"></i>
          </div>
        </div>
      </button>
    `;
  }

  function paymentInstructions(paymentKey) {

    // simple calculation placeholder
    const nights = calcNights();
    const basePerNight = state.pricePerNight || 0;
    const subtotal = nights * basePerNight;
    const cleaningFee = parseFloat(state.cleaningFee || 0) || 0;
    const total = subtotal + cleaningFee;

    const amount = total; // use your existing total function

    switch (paymentKey) {

      case 'card':
        return `
  <div class="mt-4 bg-blue-50 border-2 border-blue-200 rounded-xl p-5">
    <div class="flex items-start gap-3">
      <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
        <i data-lucide="credit-card" class="w-5 h-5 text-blue-600"></i>
      </div>
      <div class="flex-1">
        <h5 class="font-semibold text-blue-900 mb-2">${logestay.i18n.credit_card}</h5>
        <p class="text-sm text-blue-800 leading-relaxed">${logestay.i18n.credit_card_desc}</p>
        <div class="mt-3 p-3 bg-white rounded-lg border border-blue-200">
          <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-gray-700">${logestay.i18n.amount_to_pay}</span>
            <span class="text-lg font-bold text-blue-600">${amount} €</span>
          </div>
        </div>
      </div>
    </div>
  </div>`;

      case 'paypal':
        return `
  <div class="mt-4 bg-blue-50 border-2 border-blue-200 rounded-xl p-5">
    <div class="flex items-start gap-3">
      <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
        <i data-lucide="paypal" class="w-5 h-5 text-blue-600"></i>
      </div>
      <div class="flex-1">
        <h5 class="font-semibold text-blue-900 mb-2">${logestay.i18n.paypal}</h5>
        <p class="text-sm text-blue-800 leading-relaxed">${logestay.i18n.paypal_desc}</p>
        <div class="mt-3 p-3 bg-white rounded-lg border border-blue-200">
          <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-gray-700">${logestay.i18n.amount_to_pay}</span>
            <span class="text-lg font-bold text-blue-600">${amount} €</span>
          </div>
        </div>
      </div>
    </div>
  </div>`;

      case 'bank':
        return `
  <div class="mt-4 bg-amber-50 border-2 border-amber-200 rounded-xl p-5">
    <div class="flex items-start gap-3 mb-4">
      <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-content-center flex-shrink-0">
        <i data-lucide="building-2" class="w-5 h-5 text-amber-600"></i>
      </div>
      <div class="flex-1">
        <h5 class="font-semibold text-amber-900 mb-2">${logestay.i18n.bank_details}</h5>
        <p class="text-sm text-amber-800 leading-relaxed">${logestay.i18n.bank_desc}</p>
      </div>
    </div>
    <div class="bg-white rounded-lg p-4 border border-amber-200 space-y-3">
      <div>
        <p class="text-xs text-gray-500">${logestay.i18n.beneficiary}</p>
        <p class="font-semibold text-gray-900">${logestay.settings.logestay_bank_beneficiary}</p>
      </div>
      <div>
        <p class="text-xs text-gray-500">${logestay.i18n.iban}</p>
        <p class="font-mono font-semibold text-gray-900">${logestay.settings.logestay_bank_iban}</p>
      </div>
      <div>
        <p class="text-xs text-gray-500">${logestay.i18n.bic}</p>
        <p class="font-mono font-semibold text-gray-900">${logestay.settings.logestay_bank_bic}</p>
      </div>
      <div class="pt-3 border-t flex justify-between">
        <span class="text-sm font-medium text-gray-700">${logestay.i18n.amount_to_pay}</span>
        <span class="text-xl font-bold text-amber-600">${amount} €</span>
      </div>
      <div class="flex items-start gap-2 p-3 bg-amber-100 rounded-lg">
        <i data-lucide="alert-circle" class="w-4 h-4 text-amber-700 flex-shrink-0 mt-0.5"></i>
        <p class="text-xs text-amber-800 leading-relaxed">${logestay.i18n.bank_instruction}</p>
      </div>
    </div>
  </div>`;

      case 'cash':
        return `
  <div class="mt-4 bg-amber-50 border-2 border-amber-200 rounded-xl p-5">
    <div class="flex items-start gap-3 mb-4">
      <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
        <i data-lucide="banknote" class="w-5 h-5 text-amber-600"></i>
      </div>
      <div class="flex-1">
        <h5 class="font-semibold text-amber-900 mb-2">${logestay.i18n.cash_title}</h5>
        <p class="text-sm text-amber-800 leading-relaxed">${logestay.i18n.cash_desc}</p>
      </div>
    </div>

    <div class="space-y-3">
      <div class="bg-white rounded-lg p-4 border border-amber-200">
        <div class="flex items-start gap-3 mb-3">
          <i data-lucide="map-pin" class="w-5 h-5 text-amber-600"></i>
          <div class="flex-1">
            <p class="font-semibold text-gray-900 mb-1">${logestay.settings.logestay_cash_office_name}</p>
            <p class="text-sm text-gray-700">${logestay.settings.logestay_cash_office_address}</p>
          </div>
        </div>
        <div class="flex items-start gap-3 pl-8">
          <i data-lucide="clock" class="w-4 h-4 text-gray-500"></i>
          <p class="text-sm text-gray-600">${logestay.settings.logestay_cash_hours}</p>
        </div>
      </div>

      <div class="bg-white rounded-lg p-4 border border-amber-200">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-medium text-gray-700">${logestay.i18n.amount_to_pay}</span>
          <span class="text-xl font-bold text-amber-600">${amount} €</span>
        </div>
        <p class="text-xs text-gray-600">${logestay.i18n.cash_prepare_exact}</p>
      </div>

      <div class="flex items-start gap-2 p-3 bg-amber-100 rounded-lg">
        <i data-lucide="alert-circle" class="w-4 h-4 text-amber-700 flex-shrink-0 mt-0.5"></i>
        <p class="text-xs text-amber-800 leading-relaxed">${logestay.i18n.cash_bring_id}</p>
      </div>
    </div>
  </div>`;

      case 'link':
        return `
  <div class="mt-4 bg-amber-50 border-2 border-amber-200 rounded-xl p-5">
    <div class="flex items-start gap-3">
      <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
        <i data-lucide="link" class="w-5 h-5 text-amber-600"></i>
      </div>
      <div class="flex-1">
        <h5 class="font-semibold text-amber-900 mb-2">${logestay.i18n.link_title}</h5>
        <p class="text-sm text-amber-800 leading-relaxed">${logestay.i18n.link_desc}</p>
        <div class="mt-3 bg-white rounded-lg p-4 border border-amber-200 flex justify-between">
          <span class="text-sm font-medium text-gray-700">${logestay.i18n.amount_to_pay}</span>
          <span class="text-xl font-bold text-amber-600">${amount} €</span>
        </div>
      </div>
    </div>
  </div>`;
    }

    return '';
  }

  function bookingSummary() {
    // simple calculation placeholder
    const nights = calcNights();
    const basePerNight = state.pricePerNight || 0;
    const subtotal = nights * basePerNight;
    const cleaningFee = parseFloat(state.cleaningFee || 0) || 0;
    const total = subtotal + cleaningFee;

    const arrivalDate   = fullDateLabel(state.startDate);
    const departureDate = fullDateLabel(state.endDate);

    const checkinTime  = state.checkinTime || "15:00";
    const checkoutTime = state.checkoutTime || "11:00";

    const nightsLabel = (nights === 1)
      ? t('night_singular', 'night')
      : t('night_plural', 'nights');

    const arrivalLabel   = t('arrival', 'Arrival');
    const departureLabel = t('departure', 'Departure');
    const fromLabel      = t('from', 'from');
    const beforeLabel    = t('before', 'before');

    return `
      <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-6 shadow-lg">
        <h4 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-3">
          <i data-lucide="shield-check" class="w-6 h-6 text-green-600"></i>
          ${logestay.i18n.stay_summary_title}
        </h4>

        <div class="space-y-4 mb-6">

          <!-- Property -->
          <div class="bg-white rounded-xl p-4">
            <div class="flex items-start gap-3">
              <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                <i data-lucide="home" class="w-5 h-5 text-gray-700"></i>
              </div>
              <div>
                <p class="text-xs text-gray-500 mb-1">${logestay.i18n.summary_property}</p>
                <p class="font-semibold text-gray-900">${state.listingTitle}</p>
              </div>
            </div>
          </div>

          <!-- Dates -->
          <div class="bg-white rounded-xl p-4">
            <div class="flex items-start gap-3">
              <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                <i data-lucide="calendar" class="w-5 h-5 text-blue-600"></i>
              </div>
              <div>
                <p class="text-xs text-gray-500 mb-1">${logestay.i18n.summary_stay_dates}</p>
                <p class="font-semibold text-gray-900 text-sm leading-relaxed">${escapeHtml(arrivalDate)} à ${escapeHtml(checkinTime)}<span class="mx-2 text-gray-400">→</span>${escapeHtml(departureDate)} à ${escapeHtml(checkoutTime)}</p>
                <div class="mt-2 pt-2 border-t border-gray-100">
                  <p class="text-xs text-gray-600 mb-1.5">${nights} ${logestay.i18n.summary_nights}</p>
                  <div class="space-y-1">
                    <div class="flex items-center gap-1.5 text-xs text-gray-700">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock w-3.5 h-3.5 text-green-600"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <span>${escapeHtml(fromLabel)} <strong>${escapeHtml(checkinTime)}</strong></span>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs text-gray-700">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock w-3.5 h-3.5 text-orange-600"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                      <span>${escapeHtml(beforeLabel)} <strong>${escapeHtml(checkoutTime)}</strong></span>
                    </div>
                  </div>
                </div>


              </div>
            </div>
          </div>

          <!-- Pricing -->
          <div class="bg-gradient-to-br from-emerald-100 to-green-100 rounded-xl p-4 border-2 border-emerald-200">
            <div class="flex items-start gap-3">
              <div class="w-10 h-10 rounded-full bg-emerald-200 flex items-center justify-center">
                <i data-lucide="euro" class="w-5 h-5 text-emerald-700"></i>
              </div>
              <div class="flex-1">
                <p class="text-xs text-emerald-700 mb-2">${logestay.i18n.summary_pricing}</p>

                <div class="flex justify-between text-sm text-gray-900">
                  <span>${nights} ${logestay.i18n.summary_nights} × ${basePerNight} €</span>
                  <span class="font-semibold">${subtotal} €</span>
                </div>

                ${cleaningFee > 0 ? `
                <div class="flex items-center justify-between text-sm text-gray-900">
                  <span class="flex items-center gap-1.5">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5 text-emerald-600"></i>
                    ${logestay.i18n.cleaning_fee || 'Cleaning fee'}
                  </span>
                  <span class="font-semibold">${cleaningFee} €</span>
                </div>
              ` : ``}


                <div class="mt-3 pt-3 border-t-2 border-emerald-300 flex justify-between">
                  <span class="font-bold text-lg">${logestay.i18n.summary_stay_total}</span>
                  <span class="font-bold text-emerald-700 text-2xl">${total} €</span>
                </div>
                <p class="text-xs text-emerald-700 mt-2 italic">
                ${escapeHtml(t('price_auto_update', 'Price updates automatically based on your dates'))}
              </p>
              <p class="text-xs text-gray-600 mt-1.5">
                ${escapeHtml(t('cleaning_fee_note', 'The cleaning fee covers the cleaning of the accommodation after your stay.'))}
              </p>
              </div>
            </div>
          </div>

          <!-- Guests -->
          <div class="bg-white rounded-xl p-4">
            <div class="flex items-start gap-3">
              <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                <i data-lucide="users" class="w-5 h-5 text-purple-600"></i>
              </div>
              <div>
                <p class="text-xs text-gray-500 mb-1">${logestay.i18n.summary_guests}</p>
                <p class="text-sm text-gray-900">
                  <strong>${state.guests.adults}</strong> ${logestay.i18n.summary_adults}
                </p>

                <p class="text-sm text-gray-900 flex items-center gap-2">
                  <i data-lucide="baby" class="w-3 h-3"></i>
                  <span class="font-semibold">${state.guests.children}</span> ${logestay.i18n.summary_children}
                </p>

                <p class="text-sm text-gray-900 flex items-center gap-2">
                  <i data-lucide="paw-print" class="w-3 h-3"></i>
                  <span class="font-semibold">${state.guests.pets}</span> ${logestay.i18n.summary_pets}
                </p>
              </div>
            </div>
          </div>

        </div>

        <button type="button" class="logestay-av-confirm w-full bg-green-600 text-white py-4 rounded-xl font-bold text-lg hover:bg-green-700 transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100 flex items-center justify-center gap-3">
          <i data-lucide="shield-check" class="w-5 h-5"></i>
          ${logestay.i18n.book_now}
        </button>

        <div class="mt-4 space-y-3">
          <div class="flex items-center gap-2 text-sm text-gray-700">
            <i data-lucide="shield-check" class="w-4 h-4 text-green-600 flex-shrink-0"></i>
            <span>${logestay.i18n.summary_secure_payment}</span>
          </div>
          <div class="flex items-center gap-2 text-sm text-gray-700">
            <i data-lucide="clock" class="w-4 h-4 text-green-600 flex-shrink-0"></i>
            <span>${logestay.i18n.summary_dates_blocked}</span>
          </div>
          <div class="flex items-center gap-2 text-sm text-gray-700">
            <i data-lucide="headphones" class="w-4 h-4 text-green-600 flex-shrink-0"></i>
            <span>${logestay.i18n.summary_owner_confirms}</span>
          </div>
        </div>

      </div>
    `;
  }

  function logestayRenderGuestDetails() {
    return `
      <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-6 mt-6">
        <h4 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-3">
          <i data-lucide="user" class="w-6 h-6 text-gray-700"></i>
          ${logestay.i18n.guest_details_title}
        </h4>

        <div class="space-y-4">

          <!-- Name -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              ${logestay.i18n.full_name} *
            </label>
            <div class="relative">
              <i data-lucide="user"
                 class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
              <input type="text"
                class="logestay-input-name w-full pl-12 pr-4 py-3 border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:outline-none"
                placeholder="${logestay.i18n.full_name_placeholder}"
                required>
            </div>
          </div>

          <!-- Email -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              ${logestay.i18n.email} *
            </label>
            <div class="relative">
              <i data-lucide="mail"
                 class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
              <input type="email"
                class="logestay-input-email w-full pl-12 pr-4 py-3 border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:outline-none"
                placeholder="${logestay.i18n.email_placeholder}"
                required>
            </div>
          </div>

          <!-- Phone -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              ${logestay.i18n.phone}
            </label>
            <div class="relative">
              <i data-lucide="phone"
                 class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
              <input type="tel"
                class="logestay-input-phone w-full pl-12 pr-4 py-3 border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:outline-none"
                placeholder="${logestay.i18n.phone_placeholder}">
            </div>
          </div>

          <!-- Special requests -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              ${logestay.i18n.special_requests}
            </label>
            <textarea
              class="logestay-input-note w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:outline-none resize-none"
              rows="3"
              placeholder="${logestay.i18n.special_requests_placeholder}"></textarea>
          </div>

          <div class="flex items-start gap-3 pt-2"><input type="checkbox" id="acceptTerms" class="mt-1 w-5 h-5 border-2 border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 text-emerald-600 transition-colors" required=""><label for="acceptTerms" class="text-sm text-gray-700 leading-relaxed">J'accepte les <a href="/conditions-generales-de-vente/" target="_blank" rel="noopener noreferrer" class="text-emerald-600 font-semibold hover:underline transition-all">Conditions Générales d'Utilisation</a></label></div>

          <!-- Actions -->
          <div class="flex gap-3 pt-4">
            <button type="button"
              class="logestay-back flex-1 bg-gray-200 text-gray-700 py-3 rounded-xl font-semibold hover:bg-gray-300">
              ${logestay.i18n.back}
            </button>

            <button type="button" disabled
              class="logestay-confirm flex-1 bg-green-600 text-white py-3 rounded-xl font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
              ${logestay.i18n.confirm_booking}
            </button>
          </div>

        </div>
      </div>
    `;
  }



  function calcNights() {
    if (!state.startDate || !state.endDate) return 0;
    const a = cloneDate(state.startDate).getTime();
    const b = cloneDate(state.endDate).getTime();
    const diff = Math.max(0, b - a);
    return Math.round(diff / (1000 * 60 * 60 * 24));
  }

  function escapeHtml(str) {
    return String(str ?? "")
      .replaceAll("&", "&amp;")
      .replaceAll("<", "&lt;")
      .replaceAll(">", "&gt;")
      .replaceAll('"', "&quot;")
      .replaceAll("'", "&#039;");
  }
  function escapeAttr(str) {
    return escapeHtml(str).replaceAll("`", "&#096;");
  }

  function formatDateFR(d) {
  if (!d) return "";
  // Example: "18 mars 2026"
  try {
    return d.toLocaleDateString("fr-FR", { day: "numeric", month: "long", year: "numeric" });
  } catch (e) {
    return toYMD(d);
  }
}

function t(key, fallback) {
  return (window.logestay && logestay.i18n && logestay.i18n[key]) ? logestay.i18n[key] : fallback;
}

function getCheckinTime() {
  return (logestay.listing.checkin_time)
    ? logestay.listing.checkin_time
    : "15:00";
}

function getCheckoutTime() {
  return (window.logestay && logestay.listing && logestay.listing.checkout_time)
    ? logestay.listing.checkout_time
    : "11:00";
}

function formatDateLocale(d) {
  if (!d) return "";
  // Uses browser locale. If you want forced EN: use "en-US" instead of undefined.
  return d.toLocaleDateString(undefined, { day: "numeric", month: "long", year: "numeric" });
}

function renderSelectedDatesMarkup() {
  const nights = calcNights();

  const arrivalDate   = fullDateLabel(state.startDate);
  const departureDate = fullDateLabel(state.endDate);

  const checkinTime  = state.checkinTime || "15:00";
  const checkoutTime = state.checkoutTime || "11:00";

  const nightsLabel = (nights === 1)
    ? t('night_singular', 'night')
    : t('night_plural', 'nights');

  // Text labels (translatable)
  const arrivalLabel   = t('arrival', 'Arrival');
  const departureLabel = t('departure', 'Departure');
  const fromLabel      = t('from', 'from');
  const beforeLabel    = t('before', 'before');

  // Summary line with placeholders
  const summaryTpl = t('stay_summary', 'Check-in from %1$s → Check-out before %2$s');
  const summaryText = summaryTpl
    .replace('%1$s', checkinTime)
    .replace('%2$s', checkoutTime);

  return `
    <div class="mt-4 space-y-3">
      <div class="flex items-center gap-4 text-sm">
        <div class="flex-1 bg-white rounded-lg p-3 shadow-sm">
          <p class="text-gray-600 text-xs mb-1">${escapeHtml(arrivalLabel)}</p>
          <p class="font-semibold text-gray-900">${escapeHtml(arrivalDate)}</p>
          <div class="flex items-center gap-1.5 mt-1.5 text-xs text-green-700">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>${escapeHtml(fromLabel)} <strong>${escapeHtml(checkinTime)}</strong></span>
          </div>
        </div>

        <div class="flex-1 bg-white rounded-lg p-3 shadow-sm">
          <p class="text-gray-600 text-xs mb-1">${escapeHtml(departureLabel)}</p>
          <p class="font-semibold text-gray-900">${escapeHtml(departureDate)}</p>
          <div class="flex items-center gap-1.5 mt-1.5 text-xs text-orange-700">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>${escapeHtml(beforeLabel)} <strong>${escapeHtml(checkoutTime)}</strong></span>
          </div>
        </div>
      </div>

      <div class="bg-gradient-to-r from-blue-50 to-emerald-50 rounded-lg p-3 border border-blue-100">
        <div class="flex items-center justify-between text-xs">
          <div class="flex items-center gap-2 text-gray-700">
            <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <span class="font-medium">${nights} ${escapeHtml(nightsLabel)}</span>
          </div>
          <div class="text-gray-600">${escapeHtml(summaryText)}</div>
        </div>
      </div>
    </div>
  `;
}

function updateSelectedDatesUI($root) {
  const $box = $root.find(".logestay-dates-summary");
  const hasDates = !!(state.startDate && state.endDate && calcNights() > 0);

  if (!hasDates) {
    $box.addClass("hidden").empty();
    return;
  }

  $box.removeClass("hidden").html(renderSelectedDatesMarkup());
}

  // ----------------------------
  // Calendar rendering
  // ----------------------------
  function renderMonthGrid(baseDate, monthIndex) {
    const d = addMonths(baseDate, monthIndex);
    const y = d.getFullYear();
    const m = d.getMonth();

    const first = startOfMonth(y, m);
    const last = endOfMonth(y, m);

    // Sunday-based week start (matches EN labels we show)
    const startDay = first.getDay(); // 0..6
    const totalDays = last.getDate();

    const today = cloneDate(new Date());

    // Build cells (6 rows * 7 days = 42)
    const cells = [];
    for (let i = 0; i < 42; i++) {
      const dayNum = i - startDay + 1;
      if (dayNum < 1 || dayNum > totalDays) {
        cells.push(`<div class="aspect-square"></div>`);
        continue;
      }

      const cellDate = new Date(y, m, dayNum);

      const ymd = fmtYmd(cellDate);


      // Disable past
      const isDisabled = isBeforeDay(cellDate, today);

      // Selected / range
      const isStart = isSameDay(cellDate, state.startDate);
      const isEnd = isSameDay(cellDate, state.endDate);
      const inRange =
        state.startDate &&
        state.endDate &&
        !isStart &&
        !isEnd &&
        (isAfterDay(cellDate, state.startDate) && isBeforeDay(cellDate, state.endDate));

      const clsBase =
        "logestay-cal-day aspect-square flex items-center justify-center text-sm rounded-lg transition-all hover:bg-gray-100";
      const clsDisabled = " text-gray-400 line-through cursor-not-allowed bg-gray-100";
      const clsNormal = " text-gray-900 hover:bg-gray-100 cursor-pointer";
      const clsInRange = " bg-emerald-100 text-emerald-900";
      const clsSelected = " bg-emerald-600 text-white shadow";


      const isBlocked = state.disabledDates && state.disabledDates.includes(ymd);
      const finalDisabled = isDisabled || isBlocked;


      let cls = clsBase;

      if (finalDisabled) {
        cls += clsDisabled;
      } else if (isStart || isEnd) {
        cls += clsSelected;
      } else if (inRange) {
        cls += clsInRange;
      } else {
        cls += clsNormal;
      }

      cells.push(
        `<button type="button" class="${cls}" data-ymd="${ymd}" ${finalDisabled ? "disabled" : ""}>${dayNum}</button>`
      );
    }

    const header = `
        ${dayLabels().map(l =>
          `<div class="text-center text-xs font-semibold text-gray-600 py-2">${l}</div>`
        ).join("")}
    `;

    const grid = `
        ${cells.join("")}
    `;

    return header + grid;
  }

  function renderCalendar($root) {
    $root.find(".logestay-cal-label-0").text(monthLabel(state.monthBase));
    $root.find(".logestay-cal-label-1").text(monthLabel(addMonths(state.monthBase, 1)));

    $root.find(".logestay-cal-month-0").html(renderMonthGrid(state.monthBase, 0));
    $root.find(".logestay-cal-month-1").html(renderMonthGrid(state.monthBase, 1));
  }

  // ----------------------------
  // Payment UI
  // ----------------------------
  function updatePaymentUI($root) {
    const hasDates = !!(state.startDate && state.endDate && calcNights() > 0);

    const $wrap = $root.find(".logestay-pay-wrap");
    if (hasDates) $wrap.removeClass("hidden");
    else $wrap.addClass("hidden");

    // confirm button enabled only when payment selected + dates
    const $confirm = $root.find(".logestay-av-confirm");
    const canConfirm = hasDates && !!state.payment;
    $confirm.prop("disabled", !canConfirm);

    // render payment list
    const $list = $root.find(".logestay-pay-list");
    $list.html(state.payments.map(paymentRow).join(""));

    // mark selected payment
    // mark selected payment (use exact classes)
    const NORMAL_CLASSES =
      'logestay-pay-item w-full bg-white rounded-xl p-4 border-2 transition-all duration-200 text-left border-gray-200 hover:border-blue-300 hover:shadow-md';

    const SELECTED_CLASSES =
      'logestay-pay-item w-full bg-white rounded-xl p-4 border-2 transition-all duration-200 text-left border-blue-600 shadow-lg scale-[1.02]';

      // Reset all items
      $list.find('.logestay-pay-item').each(function () {
        $(this)
          .attr('class', NORMAL_CLASSES)
          .find('.checked_selected')
          .addClass('hidden');
      });

      // Apply selected state
      if (state.payment) {
        $list
          .find(`.logestay-pay-item[data-key="${state.payment}"]`)
          .attr('class', SELECTED_CLASSES)
          .find('.checked_selected')
          .removeClass('hidden');
      }

    // First set ALL items to normal (because list is re-rendered)
    $list.find('.logestay-pay-item').each(function () {
      $(this).attr('class', NORMAL_CLASSES);
    });

    // Then apply selected class to the chosen one
    if (state.payment) {
      $list.find(`.logestay-pay-item[data-key="${state.payment}"]`).attr('class', SELECTED_CLASSES);
    }

    // instructions + summary
    const $ins = $root.find(".logestay-pay-instructions");
    const $sum = $root.find(".logestay-summary");

    if (state.payment && hasDates) {
      const info = paymentInstructions(state.payment);

      if (info) {
        $ins.removeClass("hidden").html(info);
      } else {
        $ins.addClass("hidden").empty();
      }

      $sum.removeClass("hidden").html(bookingSummary());
      $('.logestay-guest-details').addClass('hidden');
    } else {
      $ins.addClass("hidden").empty();
      $sum.addClass("hidden").empty();
    }

    safeCallLucide();
  }

  function updateGuestLimitsUI($root){
    // disable + buttons when reaching max
    ["adults","children","pets"].forEach(function(k){
      const max = parseInt(state.maxGuests?.[k] ?? 0, 10);

      const val = parseInt(state.guests[k] ?? 0, 10);

      // adults: at least 1
      if(k === "adults" && val < 1) state.guests.adults = 1;

      const $plus = $root.find(`.logestay-guest-plus[data-key="${k}"]`);
      if(max > 0){
        $plus.prop("disabled", val >= max);
      } else {
        // if max=0, treat as "not allowed"
        if(k !== "adults"){
          state.guests[k] = 0;
          $root.find(`.logestay-guest-val[data-key="${k}"]`).text("0");
          $plus.prop("disabled", true);
          $root.find(`.logestay-guest-minus[data-key="${k}"]`).prop("disabled", true);
        }
      }
    });
  }

  // ----------------------------
  // Open / Close
  // ----------------------------
  function openModal(listingId) {
    if ($(".logestay-av-modal").length) closeModal();

    state.open = true;
    state.listingId = listingId;
    state.startDate = null;
    state.endDate = null;
    state.payment = null;
    state.monthBase = new Date(new Date().getFullYear(), new Date().getMonth(), 1);
    state.checkinTime = "15:00";
    state.checkoutTime = "11:00";

    // OPTIONAL: Pull listing title / city from DOM if you have them available on the card
    // You can set data-title / data-city on the button if you want:
    const $btn = $(`.view_av[data-id="${listingId}"]`).first();
    const t = $btn.data("title");
    const c = $btn.data("city");
    if (t) state.listingTitle = String(t);
    if (c) state.cityTitle = String(c);

    const html = modalTemplate();
    $("body").append(html);

    const $root = $(".logestay-av-modal").last();
    $root.attr("aria-hidden", "false");

    // lock scroll
    $("body").addClass("overflow-hidden");

    state.maxGuests = { adults: 1, children: 0, pets: 0 };
    state.pricePerNight = 0;

    $.ajax({
      url: logestay.ajaxUrl,
      type: "POST",
      dataType: "json",
      data: {
        action: "logestay_get_listing_popup_data",
        nonce: logestay.nonce,
        listing_id: listingId
      },
      success(res){
        if(!res.success) return;

        state.listingTitle = res.data.title || "Apartment";
        state.cityTitle = res.data.city_title || "City";
        state.pricePerNight = parseFloat(res.data.price_per_night || 0) || 0;
        state.cleaningFee = parseFloat(res.data.cleaning_fee || 0) || 0;
        state.maxGuests = res.data.max || state.maxGuests;
        state.partner.airbnbUrl = res.data.airbnbUrl;
        state.partner.bookingUrl = res.data.bookingUrl;
        state.checkinTime  = res.data.checkin_time  || "15:00";
        state.checkoutTime = res.data.checkout_time || "11:00";

        const $root = $(".logestay-av-modal");
        $root.find(".logestay-av-title").text(state.listingTitle);
        $root.find(".logestay-av-city").text(state.cityTitle);

        // if you add a price placeholder element (recommended)
        $root.find(".logestay-av-price").text(state.pricePerNight ? `${state.pricePerNight} € / night` : "");

        // Support both camelCase and snake_case from PHP
        const airbnbUrl  = res.data.airbnbUrl  || res.data.airbnb_url  || "";
        const bookingUrl = res.data.bookingUrl || res.data.booking_url || "";

        state.partner.airbnbUrl  = airbnbUrl;
        state.partner.bookingUrl = bookingUrl;


        // Update partner buttons immediately (template was already printed)
        const $air = $root.find(".logestay-partner-airbnb");
        const $bok = $root.find(".logestay-partner-booking");

        if (airbnbUrl) {
          $air.removeClass("hidden").attr("href", airbnbUrl);
        } else {
          $air.addClass("hidden").attr("href", "");
        }

        if (bookingUrl) {
          $bok.removeClass("hidden").attr("href", bookingUrl);
        } else {
          $bok.addClass("hidden").attr("href", "");
        }

        // IMPORTANT: wrapper that you want to hide/show
        const $partnerWrap = $root.find(".logestay-partner-wrap"); // add this class in markup (best)
        // fallback if you didn't add class:
        const $partnerWrapFallback = $air.closest(".border-t.border-gray-200.pt-6");

        // Show/hide the whole section based on BOTH urls
        const hasPartners = !!airbnbUrl || !!bookingUrl;

        // Use the wrapper you have
        const $wrap = $partnerWrap.length ? $partnerWrap : $partnerWrapFallback;
        $wrap.toggleClass("hidden", !hasPartners);

        safeCallLucide(); // refresh partner button icons too

        updateGuestLimitsUI($root);
      }
    });

    renderCalendar($root);
    updatePaymentUI($root);
    updateSelectedDatesUI($root);
    safeCallLucide();
  }

  function closeModal() {
    state.open = false;
    $(".logestay-av-modal").remove();
    $("body").removeClass("overflow-hidden");
  }

  // ----------------------------
  // Events
  // ----------------------------
  function bindEvents() {
    // open
    $(document).on("click", ".view_av", function (e) {
      e.preventDefault();
      const listingId = parseInt($(this).data("id"), 10) || 0;
      if (!listingId) return;
      openModal(listingId);

      loadDisabledDates(listingId, function () {
        const $root = $(".logestay-av-modal");
        renderCalendar($root);
      });

    });

    // close
    $(document).on("click", ".logestay-av-close, .logestay-av-cancel", function (e) {
      e.preventDefault();
      closeModal();
    });
    $(document).on("click", ".logestay-av-backdrop", function (e) {
      e.preventDefault();
      closeModal();
    });

    // calendar nav
    $(document).on("click", ".logestay-cal-prev", function (e) {
      e.preventDefault();
      state.monthBase = addMonths(state.monthBase, -1);
      const $root = $(".logestay-av-modal");
      renderCalendar($root);
      safeCallLucide();
    });
    $(document).on("click", ".logestay-cal-next", function (e) {
      e.preventDefault();
      state.monthBase = addMonths(state.monthBase, 1);
      const $root = $(".logestay-av-modal");
      renderCalendar($root);
      safeCallLucide();
    });

    // date pick
    $(document).on("click", ".logestay-cal-day", function (e) {
      e.preventDefault();
      const ymd = $(this).data("ymd");
      if (!ymd) return;

      const [Y, M, D] = String(ymd).split("-").map((x) => parseInt(x, 10));
      const picked = new Date(Y, (M || 1) - 1, D || 1);

      // selection logic:
      // 1) if no start -> set start
      // 2) if start exists and no end:
      //    - if picked <= start -> replace start
      //    - else set end
      // 3) if both exist -> reset to new start
      if (!state.startDate) {
        state.startDate = picked;
        state.endDate = null;
        state.payment = null;
      } else if (state.startDate && !state.endDate) {
        if (!isAfterDay(picked, state.startDate)) {
          state.startDate = picked;
          state.endDate = null;
          state.payment = null;
        } else {
          state.endDate = picked;
        }
      } else {
        state.startDate = picked;
        state.endDate = null;
        state.payment = null;
      }

      const $root = $(".logestay-av-modal");
      renderCalendar($root);
      updatePaymentUI($root);
      updateSelectedDatesUI($root);

    });

    // guests +/-
    $(document).on("click", ".logestay-guest-plus, .logestay-guest-minus", function (e) {
      e.preventDefault();
      const key = String($(this).data("key") || "");
      if (!key || !state.guests.hasOwnProperty(key)) return;

      const isPlus = $(this).hasClass("logestay-guest-plus");
      const min = key === "adults" ? 1 : 0;

      const next = (state.guests[key] || 0) + (isPlus ? 1 : -1);
      state.guests[key] = Math.max(min, next);

      const $root = $(".logestay-av-modal");
      $root.find(`.logestay-guest-val[data-key="${key}"]`).text(state.guests[key]);

      updateGuestLimitsUI($root);


    });

    // payment select
    $(document).on("click", ".logestay-pay-item", function (e) {
      e.preventDefault();
      const key = String($(this).data("key") || "");
      if (!key) return;

      state.payment = key;

      const $root = $(".logestay-av-modal");
      updatePaymentUI($root);

    });

    $(document).on('click', '.logestay-back', function () {
      // Hide guest details
      $('.logestay-guest-details').addClass('hidden');

      // Show summary again
      $('.logestay-summary').removeClass('hidden');

      // Optional scroll back to summary
      $('.logestay-summary')[0]?.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
      });
    });

    const CONFIRM_ENABLED_CLASSES =
      'logestay-confirm flex-1 bg-green-600 text-white py-3 rounded-xl font-semibold hover:bg-green-700 transition-colors flex items-center justify-center gap-2';

    const CONFIRM_DISABLED_CLASSES =
      'logestay-confirm flex-1 bg-green-600 text-white py-3 rounded-xl font-semibold transition-colors opacity-50 cursor-not-allowed flex items-center justify-center gap-2';

    function validateBookingForm() {
      const name   = $('.logestay-input-name').val().trim();
      const email  = $('.logestay-input-email').val().trim();
      const terms  = $('#acceptTerms').is(':checked');

      // Better email validation (still lightweight)
      const emailValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

      const isValid = name.length > 2 && emailValid && terms;

      const $btn = $('.logestay-confirm');

      if (isValid) {
        $btn
          .prop('disabled', false)
          .attr('class', CONFIRM_ENABLED_CLASSES);
      } else {
        $btn
          .prop('disabled', true)
          .attr('class', CONFIRM_DISABLED_CLASSES);
      }
    }

    // Trigger validation on input + checkbox change
    $(document).on(
      'input change',
      '.logestay-input-name, .logestay-input-email, #acceptTerms',
      validateBookingForm
    );

    // confirm (frontend only for now)
    $(document).on("click", ".logestay-av-confirm", function (e) {
      e.preventDefault();


       // Hide summary
      $('.logestay-summary').addClass('hidden');

      // Show guest details
      $('.logestay-guest-details').removeClass("hidden").html(logestayRenderGuestDetails());

      // Optional: smooth scroll inside popup
      $('.logestay-guest-details')[0]?.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
      });

      safeCallLucide();

    });

    function validateBookingState() {
      if (!state.startDate || !state.endDate || calcNights() <= 0) {
        return logestay.i18n.error_invalid_dates;
      }

      if (!state.payment) {
        return logestay.i18n.error_select_payment;
      }

      if (!state.guest || !state.guest.name || !state.guest.email) {
        return logestay.i18n.error_guest_details;
      }

      if (!state.guest.email.includes("@")) {
        return logestay.i18n.error_invalid_email;
      }

      return null;
    }

    function handlePaymentFlow(res, $root, $btn) {
      const payment = state.payment;

      // ONLINE PAYMENTS (redirect later)
      if (payment === "card" || payment === "paypal") {
        window.location.href = res.redirect_url || "#";
        return;
      }

      // OFFLINE PAYMENTS → success screen
      showOfflineSuccess($root);
      resetConfirmButton($btn);
    }

    function showOfflineSuccess($root) {
      const $wrap = $root.find(".logestay-summary");

      $wrap.prepend(`
        <div class="mb-4 bg-emerald-50 border border-emerald-200 rounded-2xl p-4 flex items-start gap-3">
          <i data-lucide="check-circle" class="w-5 h-5 text-emerald-700 mt-0.5"></i>
          <div>
            <p class="font-bold text-emerald-900">${logestay.i18n.booking_request_sent}</p>
            <p class="text-sm text-emerald-800">
              ${logestay.i18n.booking_request_desc}
            </p>
          </div>
        </div>
      `);

      safeCallLucide();
    }

   function resetConfirmButton($btn) {
      $btn
        .prop("disabled", false)
        .html(
          `<i data-lucide="check" class="w-5 h-5"></i> ${logestay.i18n.confirm_booking}`
        );

      safeCallLucide();
    }


      $(document).on("click", ".logestay-confirm", function () {
        const $btn = $(this);

        if ($btn.prop("disabled")) return;

        if (!state.startDate || !state.endDate || !state.payment || !state.listingId) {
          //alert("Please complete all steps.");
          //return;
        }

        $btn.prop("disabled", true).html(
          `<i data-lucide="loader-circle" class="w-5 h-5 animate-spin"></i> ` + logestay.i18n.processing
        );
        safeCallLucide();

        // ✅ ALWAYS ensure booking hold first
        ensureBookingHold(function (err) {
          if (err) {
            alert(err.message);
            resetConfirmButton($btn);
            return;
          }

          // Branch by payment method
          if (state.payment === "card") {
            createStripeCheckout($btn);
            return;
          }

          if (state.payment === "paypal") {
            createPayPalCheckout($btn);
            return;
          }

          // bank / cash / link
          confirmOfflineBooking($btn);
        });
      });

      

     function createPayPalCheckout($btn) {
        $.ajax({
          url: logestay.ajaxUrl,
          type: "POST",
          dataType: "json",
          data: {
            action: "logestay_create_paypal_order",
            nonce: logestay.nonce,
            booking_id: state.bookingId,
          },
          success(res) {
            if (res.success && res.data && res.data.approve_url) {
              window.location.href = res.data.approve_url;
              return;
            }

            alert(
              (res.data && res.data.message)
                ? res.data.message
                : logestay.i18n.paypal_error
            );
            resetConfirmButton($btn);
          },
          error() {
            alert(logestay.i18n.server_error);
            resetConfirmButton($btn);
          },
        });
      }

      function createStripeCheckout($btn) {
        $.ajax({
          url: logestay.ajaxUrl,
          type: "POST",
          dataType: "json",
          data: {
            action: "logestay_create_checkout",
            nonce: logestay.nonce,
            booking_id: state.bookingId,
          },
          success(res) {
            if (res.success && res.data.checkout_url) {
              window.location.href = res.data.checkout_url;
            } else {
              alert(
                (res.data && res.data.message)
                  ? res.data.message
                  : logestay.i18n.payment_error
              );
              resetConfirmButton($btn);
            }
          },
          error() {
            alert(logestay.i18n.server_error);
            resetConfirmButton($btn);
          },
        });
      }

      function confirmOfflineBooking($btn) {
        $.ajax({
          url: logestay.ajaxUrl,
          type: "POST",
          dataType: "json",
          data: {
            action: "logestay_confirm_offline_booking",
            nonce: logestay.nonce,
            booking_id: state.bookingId,
            payment_method: state.payment,
          },
          success(res) {
            if (res.success) {
              // close booking popup
              $(".logestay-av-modal").fadeOut(200, function () {
                $(this).remove();
              });

              // show success screen
              showBookingSuccessModal();
            } else {
              alert(
                (res.data && res.data.message)
                  ? res.data.message
                  : logestay.i18n.booking_save_error
              );
            }
            resetConfirmButton($btn);
          },
          error() {
            alert(logestay.i18n.server_error);
            resetConfirmButton($btn);
          },
        });
      }


      function cleanLogestayUrl() {
        if (!window.history.replaceState) return;
        const url = new URL(window.location.href);
        url.searchParams.delete("ls_booking");
        url.searchParams.delete("ls_status");
        url.searchParams.delete("ls_token");
        window.history.replaceState({}, document.title, url.pathname);
      }

      $(document).on("click", ".logestay-success-close", function () {
        $(".logestay-success-modal").fadeOut(200, function () {
          $(this).remove();
          $('body').removeClass('overflow-hidden');
        });
        cleanLogestayUrl();

      });

      function showBookingSuccessModal() {
        const html = `
          <div class="logestay-success-modal fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-end md:items-center justify-center p-4 animate-fadeIn">
            <div class="bg-white rounded-t-3xl md:rounded-3xl w-full max-w-2xl shadow-2xl animate-slideUp p-8 text-center">
              <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
              </div>

              <h3 class="text-3xl font-bold text-gray-900 mb-4">
                ${logestay.i18n.booking_registered}
              </h3>

              <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                ${logestay.i18n.booking_registered_desc}
              </p>

              <button type="button"
                class="logestay-success-close bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-xl font-semibold transition-colors">
                ${logestay.i18n.close}
              </button>
            </div>
          </div>
        `;

        $("body").append(html);
      }


      function showBookingSuccessModalStripe() {
      const html = `
        <div class="logestay-success-modal fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-end md:items-center justify-center p-4 animate-fadeIn">
          <div class="bg-white rounded-t-3xl md:rounded-3xl w-full max-w-2xl shadow-2xl animate-slideUp p-8 text-center">
            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <svg class="w-10 h-10 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
            </div>

            <h3 class="text-3xl font-bold text-gray-900 mb-4">
              ${logestay.i18n.booking_paid_title}
            </h3>

            <p class="text-lg text-gray-600 mb-8 leading-relaxed">
              ${logestay.i18n.booking_paid_desc}
            </p>

            <button type="button"
              class="logestay-success-close bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-xl font-semibold transition-colors">
              ${logestay.i18n.close}
            </button>
          </div>
        </div>
      `;

      $("body").append(html);
    }

      function getLogestayUrlParams() {
        const p = new URLSearchParams(window.location.search);
        return {
          booking: p.get("ls_booking"),
          status: p.get("ls_status"),
          token:  p.get("ls_token"),
        };
      }

      function showBookingCancelModal() {
        $(".logestay-av-modal").remove();

        const html = `
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-end md:items-center justify-center p-4 animate-fadeIn logestay-cancel-modal">
          <div class="bg-white rounded-t-3xl md:rounded-3xl w-full max-w-2xl shadow-2xl animate-slideUp p-8 text-center">
            <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <svg class="w-10 h-10 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01M12 5a7 7 0 100 14 7 7 0 000-14z"/>
              </svg>
            </div>

            <h3 class="text-3xl font-bold text-gray-900 mb-4">
              ${logestay.i18n.payment_cancelled_title}
            </h3>

            <p class="text-lg text-gray-600 mb-8 leading-relaxed">
              ${logestay.i18n.payment_cancelled_desc}
            </p>

            <button class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-xl font-semibold transition-colors logestay-close-cancel">
              ${logestay.i18n.close}
            </button>
          </div>
        </div>
        `;

        $("body").append(html);

        $(document).on("click", ".logestay-close-cancel", function () {
          $(".logestay-cancel-modal").remove();
          cleanLogestayUrl();
        });
      }

      $(function () {
        const p = getLogestayUrlParams();
        if (!p.status || !p.booking) return;

        if (p.status === "success") {
          // requires token
          if (!p.booking || !p.token) {
            showBookingSuccessModalStripe(); // fallback UX
            return;
          }

          $.ajax({
            url: logestay.ajaxUrl,
            type: "POST",
            dataType: "json",
            data: {
              action: "logestay_mark_paid",
              nonce: logestay.nonce,
              booking_id: p.booking,
              token: p.token,
            },
            success(res) {
              // even if server refuses, still show UX
              showBookingSuccessModalStripe();
            },
            error() {
              showBookingSuccessModalStripe();
            },
            complete() {
              // clean URL so it won't repeat
              cleanLogestayUrl();
            }
          });
        }

        if (p.status === "cancel") {
          if (p.booking && p.token) {
            $.ajax({
              url: logestay.ajaxUrl,
              type: "POST",
              dataType: "json",
              data: {
                action: "logestay_cancel_booking",
                nonce: logestay.nonce,
                booking_id: p.booking,
                token: p.token,
              },
              complete() {
                showBookingCancelModal();
                cleanLogestayUrl();
              },
            });
          } else {
            showBookingCancelModal();
            cleanLogestayUrl();
          }
        }
      });

      function showBookingCancelModal() {
        const html = `
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-end md:items-center justify-center p-4 animate-fadeIn logestay-cancel-modal">
          <div class="bg-white rounded-t-3xl md:rounded-3xl w-full max-w-2xl shadow-2xl animate-slideUp p-8 text-center">
            <div class="w-20 h-20 bg-rose-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <i data-lucide="x" class="w-10 h-10 text-rose-600"></i>
            </div>

            <h3 class="text-3xl font-bold text-gray-900 mb-4">
              ${logestay.i18n.payment_cancelled_title}
            </h3>

            <p class="text-lg text-gray-600 mb-8 leading-relaxed">
              ${logestay.i18n.payment_cancelled_desc}
            </p>

            <button class="bg-rose-600 hover:bg-rose-700 text-white px-8 py-4 rounded-xl font-semibold transition-colors logestay-cancel-close">
              ${logestay.i18n.close}
            </button>
          </div>
        </div>`;

        $("body").append(html);
        safeCallLucide();

        $(document).on("click", ".logestay-cancel-close, .logestay-cancel-modal", function (e) {
          if ($(e.target).hasClass("logestay-cancel-modal") || $(e.target).hasClass("logestay-cancel-close")) {
            $(".logestay-cancel-modal").remove();
          }
        });
      }

      (function () {
        const params = new URLSearchParams(window.location.search);
        const modal = params.get("ls_modal");
        const bookingId = params.get("ls_book");

        if (!modal) return;

        // clean the URL (optional)
        if (window.history && window.history.replaceState) {
          params.delete("ls_modal");
          params.delete("ls_book");
          const newUrl = window.location.pathname + (params.toString() ? "?" + params.toString() : "");
          window.history.replaceState({}, document.title, newUrl);
        }

        if (modal === "booking_success") {
          showBookingSuccessModal(); // you already have this
        }

        if (modal === "booking_cancel") {
          showBookingCancelModal(); // you said you want this
        }
      })();

    // ESC to close
    $(document).on("keydown", function (e) {
      if (e.key === "Escape" && $(".logestay-av-modal").length) closeModal();
    });
  }

  // ----------------------------
  // Init
  // ----------------------------
  $(function () {
    bindEvents();
  });



})(jQuery);