jQuery(function ($) {

  // ============ Amenities Builder ============
  var $wrap = $('#logestay-amenities-wrap');
  if (!$wrap.length) return;

  var $field = $('#logestay_listing_amenities'); // hidden JSON
  var presets = window.LOGESTAY_AMENITY_PRESETS || [];
  function toKebabCase(name) {
  // "AlarmClockMinus" => "alarm-clock-minus"
  return String(name || "")
    .replace(/([a-z0-9])([A-Z])/g, "$1-$2")
    .replace(/_/g, "-")
    .toLowerCase()
    .trim();
}

function getLucideKeys() {
  const dict = (window.lucide && window.lucide.icons && typeof window.lucide.icons === "object")
    ? window.lucide.icons
    : {};

  // These are the REAL names available in your current lucide.min.js build
  const keys = Object.keys(dict);

  // Make sure check exists + keep it first
  const set = new Set(keys);
  const out = [];

  if (set.has("check")) out.push("check");
  if (set.has("Check")) out.push("Check"); // just in case

  keys.forEach((k) => {
    if (k === "check" || k === "Check") return;
    out.push(k);
  });

  return out;
}

let icons = getLucideKeys();


  function readItems() {
    var raw = $field.val();
    if (!raw) return [];
    try {
      var arr = JSON.parse(raw);
      return Array.isArray(arr) ? arr : [];
    } catch (e) {
      return [];
    }
  }

  function writeItems(items) {
    $field.val(JSON.stringify(items));
  }

  function escapeHtml(str) {
    return String(str || '')
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');
  }

  function renderButtonIcons(){
    $('.logestay-icon-btn .icon').each(function(){
      var name = $(this).attr('data-icon') || 'check';
      $(this).html('<i data-lucide="'+name+'"></i>');
    });
    if(window.lucide && window.lucide.createIcons){
      window.lucide.createIcons();
      hideUnavailableLucideIcons();
    }
  }

  function render() {
    var items = readItems();
    $wrap.empty();

    if (!items.length) {
      $wrap.append('<p class="logestay-help">No amenities added yet.</p>');
      return;
    }

    items.forEach(function (item, idx) {
      var label = item.label || '';
      var icon  = item.icon || '';

      var html =
        '<div class="logestay-amenity-item" data-index="' + idx + '">' +
          '<div class="logestay-amenity-row">' +
            '<div>' +
              '<label style="display:block;font-weight:600;margin-bottom:6px;">Label</label>' +
              '<input type="text" class="logestay-amenity-label" value="' + escapeHtml(label) + '" placeholder="e.g. Wi-Fi" />' +
            '</div>' +
            '<div>' +
              '<input type="hidden" class="logestay-amenity-icon" value="' + escapeHtml(icon) + '" />' +
              '<label style="display:block;font-weight:600;margin-bottom:6px;" >Click to pick icon</label>' +
              '<button type="button" class="logestay-icon-btn logestay-open-icon-picker">' +
                '<span class="icon" data-icon="' + escapeHtml(icon || 'check') + '"></span>' +
                '<span class="name">' + escapeHtml(icon || 'check') + '</span>' +
              '</button>' +
              
            '</div>' +
            '<div style="display:flex;flex-direction:column;gap:8px;align-items:flex-end;">' +
              '<span class="logestay-amenity-handle">Drag</span>' +
              '<button type="button" class="logestay-amenity-remove">Remove</button>' +
            '</div>' +
          '</div>' +
        '</div>';

      $wrap.append(html);
    });

    renderButtonIcons();
  }

  function hideUnavailableLucideIcons() {
    $('.logestay-icon-item').each(function () {
      var $item = $(this);

      // If lucide failed, <i> still exists
      if ($item.find('i[data-lucide]').length) {
        $item.addClass('hidden');
      } else {
        $item.removeClass('hidden');
      }
    });
  }

  // ---------- Icon Picker (modal + searchable grid) ----------
      function ensureIconModal(){
        if($('#logestay-icon-modal').length) return;

        var modal = ''+
        '<div id="logestay-icon-modal" class="logestay-icon-modal" role="dialog" aria-modal="true">'+
          '<div class="logestay-icon-modal__panel">'+
            '<div class="logestay-icon-modal__head">'+
              '<input type="search" id="logestay-icon-search" placeholder="Search icon..." />'+
              '<button type="button" class="logestay-icon-modal__close">Close</button>'+
            '</div>'+
            '<div class="logestay-icon-grid" id="logestay-icon-grid"></div>'+
          '</div>'+
        '</div>';

        $('body').append(modal);

        // build grid once
        let html = "";
        icons.forEach((name) => {
          html += `
            <button type="button" class="logestay-icon-item" data-value="${escapeHtml(name)}">
              <i data-lucide="${escapeHtml(name)}"></i>
              <span class="label">${escapeHtml(name)}</span>
            </button>
          `;
        });

        $("#logestay-icon-grid").html(html);

        if (window.lucide && window.lucide.createIcons) {
          window.lucide.createIcons();
          hideUnavailableLucideIcons();
        }

        // close handlers
        $(document).on('click', '.logestay-icon-modal__close, #logestay-icon-modal', function(e){
          if(e.target.id === 'logestay-icon-modal' || $(e.target).hasClass('logestay-icon-modal__close')){
            $('#logestay-icon-modal').removeClass('is-open').data('target', '');
          }
        });

        // search filter
        $('#logestay-icon-search').on('input', function(){
          var q = ($(this).val() || '').toLowerCase();
          $('#logestay-icon-grid .logestay-icon-item').each(function(){
            var v = ($(this).data('value') || '').toLowerCase();
            $(this).toggle(v.indexOf(q) !== -1);
          });
        });

        // choose icon
        $(document).on('click', '#logestay-icon-grid .logestay-icon-item', function(){
          var chosen = $(this).data('value');
          var target = $('#logestay-icon-modal').data('target'); // selector
          if(!target) return;

          // target can be: hidden input selector OR an amenity row button context
          var $t = $(target);

          if($t.length){
            $t.val(chosen).trigger('change');
            // update paired button UI if exists
            var $btn = $t.data('btn') ? $($t.data('btn')) : null;
            if($btn && $btn.length){
              $btn.find('.name').text(chosen);
              $btn.find('.icon').attr('data-icon', chosen).html('<i data-lucide="'+chosen+'"></i>');
              if(window.lucide && window.lucide.createIcons){ window.lucide.createIcons(); hideUnavailableLucideIcons(); }
            }
          }

          $('#logestay-icon-modal').removeClass('is-open').data('target', '');
        });

      }

      // Open picker for amenities + custom field
      $(document).on('click', '.logestay-open-icon-picker', function(e){
        e.preventDefault();
        ensureIconModal();

        var $btn = $(this);

        // Case A: custom field button uses data-target="#id"
        var targetSel = $btn.data('target');

        // Case B: amenity button -> find sibling hidden input
        if(!targetSel){
          var $hidden = $btn.closest('.logestay-amenity-row').find('.logestay-amenity-icon');
          if(!$hidden.length) return;

          // create a unique hook by storing selectorless reference using data
          // easiest: store the element in data via an id (assign if missing)
          if(!$hidden.attr('id')){
            $hidden.attr('id', 'logestay-icon-hidden-' + Math.random().toString(16).slice(2));
          }
          targetSel = '#' + $hidden.attr('id');

          // link hidden input to its button (for UI update after choose)
          $hidden.data('btn', '#' + $btn.attr('id'));
          if(!$btn.attr('id')){
            $btn.attr('id', 'logestay-icon-btn-' + Math.random().toString(16).slice(2));
            $hidden.data('btn', '#' + $btn.attr('id'));
          }
        } else {
          // link custom hidden to this button for UI update
          var $hidden2 = $(targetSel);
          if($hidden2.length){
            if(!$btn.attr('id')){
              $btn.attr('id', 'logestay-icon-btn-' + Math.random().toString(16).slice(2));
            }
            $hidden2.data('btn', '#' + $btn.attr('id'));
          }
        }

        // open modal
        $('#logestay-icon-search').val('');
        $('#logestay-icon-modal').data('target', targetSel).addClass('is-open');

        // focus search
        setTimeout(function(){ $('#logestay-icon-search').trigger('focus'); }, 50);
      });

  function addItem(item) {
    var items = readItems();
    items.push({
      label: item.label || '',
      icon: item.icon || 'check'
    });
    writeItems(items);
    render();
  }

  // Sortable
  $wrap.sortable({
    items: '.logestay-amenity-item',
    update: function () {
      var items = [];
      $wrap.find('.logestay-amenity-item').each(function () {
        var $it = $(this);
        items.push({
          label: $it.find('.logestay-amenity-label').val() || '',
          icon:  $it.find('.logestay-amenity-icon').val() || 'check'
        });
      });
      writeItems(items);
      render();
    }
  });

  // Sync changes
  $wrap.on('input change', '.logestay-amenity-label, .logestay-amenity-icon', function () {
    var items = [];
    $wrap.find('.logestay-amenity-item').each(function () {
      var $it = $(this);
      items.push({
        label: $it.find('.logestay-amenity-label').val() || '',
        icon:  $it.find('.logestay-amenity-icon').val() || 'check'
      });
    });
    writeItems(items);
  });

  // Remove item
  $wrap.on('click', '.logestay-amenity-remove', function (e) {
    e.preventDefault();
    $(this).closest('.logestay-amenity-item').remove();
    render();
  });

  // Add from preset
  $('#logestay-amenity-add-preset').on('click', function (e) {
    e.preventDefault();
    var key = $('#logestay-amenity-preset').val();
    if (!key) return;

    var found = presets.find(function (p) {
      return p.key === key;
    });
    if (!found) return;

    addItem(found);
  });

  // Add custom
  $('#logestay-amenity-add-custom').on('click', function (e) {
    e.preventDefault();
    var label = ($('#logestay-amenity-custom-label').val() || '').trim();
    var icon  = $('#logestay-amenity-custom-icon').val() || 'check';
    if (!label) return;

    addItem({ label: label, icon: icon });
    $('#logestay-amenity-custom-label').val('');
  });

  // Initial render
  render();

});



jQuery(function ($) {

  var frame;

  function renderGallery(ids){
    var $wrap = $('#logestay-gallery-wrap');
    $wrap.empty();

    if(!ids.length){
      $wrap.append('<p class="logestay-help">No gallery images selected.</p>');
      return;
    }

    ids.forEach(function(id){
      id = parseInt(id, 10);
      if(!id) return;

      var thumb = $('#logestay-gallery-wrap').data('placeholder');

      // We'll request attachment data via wp.media if available
      if (wp && wp.media && wp.media.attachment) {
        var att = wp.media.attachment(id);
        att.fetch().done(function(){
          var url = att.get('sizes') && att.get('sizes').thumbnail ? att.get('sizes').thumbnail.url : att.get('url');
          addItem(id, url);
        }).fail(function(){
          addItem(id, thumb);
        });
      } else {
        addItem(id, thumb);
      }
    });

    function addItem(id, url){
      var html = ''+
        '<div class="logestay-gallery-item" data-id="'+id+'">'+
          '<button type="button" class="remove" aria-label="Remove">×</button>'+
          '<img src="'+url+'" alt="" />'+
        '</div>';
      $wrap.append(html);
    }
  }

  function getIds(){
    var val = $('#logestay_listing_gallery').val().trim();
    if(!val) return [];
    return val.split(',').map(function(v){ return parseInt(v.trim(),10); }).filter(Boolean);
  }

  function setIds(ids){
    $('#logestay_listing_gallery').val(ids.join(','));
  }

  // Init sortable
    $('#logestay-gallery-wrap').sortable({
      items: '.logestay-gallery-item',
      update: function(){
        var ids = [];
        $('#logestay-gallery-wrap .logestay-gallery-item').each(function(){
          ids.push(parseInt($(this).data('id'),10));
        });
        setIds(ids);
      }
    });

  $(document).on('click', '.logestay-gallery-item .remove', function(){
      var $item = $(this).closest('.logestay-gallery-item');
      $item.remove();
      var ids = [];
      $('#logestay-gallery-wrap .logestay-gallery-item').each(function(){
        ids.push(parseInt($(this).data('id'),10));
      });
      setIds(ids);
      if(!ids.length){
        $('#logestay-gallery-wrap').html('<p class="logestay-help">No gallery images selected.</p>');
      }
  });

  $('#logestay-gallery-add').on('click', function(e){
      e.preventDefault();

      if(frame){
        frame.open();
        return;
      }

      frame = wp.media({
        title: 'Select gallery images',
        button: { text: 'Use selected images' },
        multiple: true
      });

      frame.on('select', function(){
        var selection = frame.state().get('selection');
        var ids = getIds();

        selection.each(function(att){
          var id = att.get('id');
          if(ids.indexOf(id) === -1){
            ids.push(id);
          }
        });

        setIds(ids);
        renderGallery(ids);
      });

      frame.open();
    });

  // Clear button
    $('#logestay-gallery-clear').on('click', function(e){
      e.preventDefault();
      setIds([]);
      $('#logestay-gallery-wrap').html('<p class="logestay-help">No gallery images selected.</p>');
    });

    // Initial render
    renderGallery(getIds());

});
