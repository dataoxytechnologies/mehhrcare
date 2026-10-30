/**
 * hospital-custom-bs5.js
 * Bootstrap 5 compatible version of hospital-custom.js.
 *
 * Changes from original:
 *  1. $.fn.btnLoading() / $.fn.btnReset() polyfills replace removed .button('loading'/'reset')
 *  2. modal_click_disabled() uses Bootstrap 5 Modal API instead of jQuery .modal()
 *  3. shModal(id) helper: returns BS5 Modal instance (replaces $('#id').modal('show/hide'))
 *  4. All .button('loading') → .btnLoading()
 *  5. All .button('reset')   → .btnReset()
 *  6. All .modal('show')     → shModal(id).show()
 *  7. All .modal('hide')     → shModal(id).hide()
 *  8. .affix() blocks removed (replaced by CSS position: sticky in sh-theme.css)
 *
 * DO NOT add .button(), .affix(), or bootstrapVersion:3 back — these were removed in Bootstrap 5.
 */

/* =============================================================
   POLYFILLS: replace Bootstrap 3's removed .button() plugin
   ============================================================= */
$.fn.btnLoading = function () {
  return this.each(function () {
    var $b = $(this);
    if ($b.data('btn-loading')) return; // already loading
    $b.data('btn-orig', $b.html())
      .data('btn-loading', true)
      .prop('disabled', true)
      .html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Loading…');
  });
};

$.fn.btnReset = function () {
  return this.each(function () {
    var $b = $(this);
    $b.prop('disabled', false)
      .html($b.data('btn-orig') || $b.html())
      .data('btn-loading', false);
  });
};

/* Bootstrap 5 Modal helper — replaces $('#id').modal('show'/'hide') */
function shModal(idOrEl) {
  var el = (typeof idOrEl === 'string') ? document.getElementById(idOrEl) : idOrEl;
  if (!el) return { show: function(){}, hide: function(){}, toggle: function(){} };
  if (el.parentNode !== document.body) { document.body.appendChild(el); }
  return bootstrap.Modal.getOrCreateInstance(el);
}

/* Strip a single query param from current URL without reloading the page.
   Used by Quick Add (+) auto-modal triggers so closing the modal + refreshing
   the page doesn't re-trigger the modal. */
function shCleanUrlParam(name) {
  try {
    if (history.replaceState && window.URL) {
      var u = new URL(window.location.href);
      u.searchParams.delete(name);
      history.replaceState({}, '', u.pathname + u.search + u.hash);
    }
  } catch (e) {}
}

/* =============================================================
   UTILITY FUNCTIONS
   ============================================================= */
function capitalizeFirstLetter(string) {
  if (!string) return '';
  return string.charAt(0).toUpperCase() + string.slice(1);
}

function timeConvert(time) {
  time = time.toString().match(/^([01]\d|2[0-3])(:)([0-5]\d)(:[0-5]\d)?$/) || [time];
  if (time.length > 1) {
    time = time.slice(1);
    time[5] = +time[0] < 12 ? 'AM' : 'PM';
    time[3] = ' ';
    time[0] = +time[0] % 12 || 12;
  }
  return time.join('');
}

/* Keep modal-open class + clean up extra backdrops when stacked modals close */
$(document).on('hidden.bs.modal', '.modal', function () {
  var $visible = $('.modal.show');
  if ($visible.length) {
    $(document.body).addClass('modal-open');
    var $backdrops = $('.modal-backdrop');
    while ($backdrops.length > $visible.length) {
      $backdrops.last().remove();
      $backdrops = $('.modal-backdrop');
    }
  }
});

/* Stacked modal z-index: each new modal sits above the previous one */
$(document).on('show.bs.modal', '.modal', function () {
  var visibleCount = $('.modal.show').length;
  if (visibleCount > 0) {
    var zIndex = 1055 + (20 * visibleCount);
    $(this).css('z-index', zIndex);
    setTimeout(function () {
      $('.modal-backdrop').last().css('z-index', zIndex - 10);
    }, 0);
  }
});

function reset_form(selector) {
  $(selector).find('input:text, input:password, input:file, select, textarea').val('');
  $(selector).find('input:radio, input:checkbox').prop('checked', false);
  $(selector).find('select option').prop('selected', false);
}

/* modal_click_disabled: initialize modals as static (no close on backdrop click)
   BS5 replaces: $('#id').modal({backdrop:'static', keyboard:false, show:false}) */
modal_click_disabled = function () {
  for (var i = 0; i < arguments.length; i++) {
    var el = document.getElementById(arguments[i]);
    if (el) {
      bootstrap.Modal.getOrCreateInstance(el, { backdrop: 'static', keyboard: false });
    }
  }
};

/* Disable autocomplete on all inputs */
$(document).on('focus', ':input', function () {
  $(this).attr('autocomplete', 'off');
});

/* =============================================================
   LEGACY DELETE HELPERS (unchanged — just AJAX calls)
   ============================================================= */
function delete_recordById(url) {
  if (confirm('Are you sure want to delete ?')) {
    $.ajax({
      url: baseurl + url, dataType: 'json',
      success: function (res) {
        if (res.status == 1) { successMsg(res.msg); table.ajax.reload(); }
        else { errorMsg(res.msg); }
      },
      error: function () { alert('Something Went Wrong'); }
    });
  }
}

function delete_recordByIdReload(url) {
  if (confirm('Are you sure want to delete ?')) {
    $.ajax({
      url: baseurl + url, dataType: 'json',
      success: function (res) {
        if (res.status == 1) {
          successMsg(res.msg);
          setTimeout(function () { window.location.reload(); }, 500);
        } else { errorMsg(res.msg); }
      },
      error: function () { alert('Something Went Wrong'); }
    });
  }
}

/* =============================================================
   DOCUMENT READY — MAIN BLOCK
   ============================================================= */
$(document).ready(function () {

  /* --- License modal --- */
  if ($('.purchasemodal').length <= 0 && chk_validate == '') {
    shModal('activelicmodal').show();
  }
  $(document).on('click', '.purchasemodal', function () {
    shModal('activelicmodal').show();
  });
  $('#activelicmodal').on('shown.bs.modal', function () {
    $(this).find('input').next('div').html('');
  });

  /* --- Session modal (static — no backdrop close) --- */
  modal_click_disabled('sessionModal');
  $('#sessionModal').on('show.bs.modal', function (event) {
    var $modalDiv = $(event.delegateTarget);
    $('.sessionmodal_body').html('');
    $.ajax({
      type: 'POST', url: baseurl + 'admin/admin/getSession', dataType: 'text',
      beforeSend:  function () { $modalDiv.addClass('modal_loading'); },
      success:     function (data) { $('.sessionmodal_body').html(data); },
      error:       function () { $modalDiv.removeClass('modal_loading'); },
      complete:    function () { $modalDiv.removeClass('modal_loading'); }
    });
  });
  $(document).on('click', '.submit_session', function () {
    var $this = $(this);
    $.ajax({
      type: 'POST', url: baseurl + 'admin/admin/updateSession', dataType: 'json',
      data: $('form#form_modal_session').serialize(),
      beforeSend: function () { $this.btnLoading(); },
      success: function (data) {
        if (data.status == 1) {
          shModal('sessionModal').hide();
          window.location.href = baseurl + 'admin/admin/dashboard';
          successMsg('Session change successful');
        }
      },
      error:    function () { $this.btnReset(); },
      complete: function () { $this.btnReset(); }
    });
  });

  /* --- Toastr config --- */
  toastr.options = {
    closeButton: true, debug: false, newestOnTop: false, progressBar: false,
    positionClass: 'toast-top-right', preventDuplicates: false,
    onclick: null,
    showDuration: '5000', hideDuration: '5000',
    timeOut: '5000', extendedTimeOut: '10000',
    showEasing: 'swing', hideEasing: 'linear',
    showMethod: 'fadeIn', hideMethod: 'fadeOut'
  };

  /* NOTE: .affix() removed — #alert sticky behavior is now CSS-only via sh-theme.css */

  /* --- User quick session modal --- */
  modal_click_disabled('user_sessionModal');
  $('#user_sessionModal').on('show.bs.modal', function (event) {
    var $modalDiv = $(event.delegateTarget);
    $('.user_sessionmodal_body').html('');
    $.ajax({
      type: 'POST', url: baseurl + 'common/getSudentSessions', dataType: 'text',
      beforeSend:  function () { $modalDiv.addClass('modal_loading'); },
      success:     function (data) { $('.user_sessionmodal_body').html(data); },
      error:       function () { $modalDiv.removeClass('modal_loading'); },
      complete:    function () { $modalDiv.removeClass('modal_loading'); }
    });
  });
  $(document).on('click', '.submit_usersession', function () {
    var $this = $(this);
    $.ajax({
      type: 'POST', url: baseurl + 'common/updateSession', dataType: 'json',
      data: $('form#form_modal_usersession').serialize(),
      beforeSend: function () { $this.btnLoading(); },
      success: function (data) {
        if (data.status == 1) {
          shModal('sessionModal').hide();
          window.location.href = baseurl + 'user/user/dashboard';
          successMsg('Session change successful');
        }
      },
      error:    function () { $this.btnReset(); },
      complete: function () { $this.btnReset(); }
    });
  });

  /* --- Common session modal --- */
  modal_click_disabled('commanSessionModal');
  $('#commanSessionModal').on('show.bs.modal', function (event) {
    var $modalDiv = $(event.delegateTarget);
    $('.commonsessionmodal_body').html('');
    $.ajax({
      type: 'POST', url: baseurl + 'common/getAllSession', dataType: 'text',
      beforeSend:  function () { $modalDiv.addClass('modal_loading'); },
      success:     function (data) { $('.commonsessionmodal_body').html(data); },
      error:       function () { $modalDiv.removeClass('modal_loading'); },
      complete:    function () { $modalDiv.removeClass('modal_loading'); }
    });
  });
  $(document).on('click', '.submit_common_session', function () {
    var $this = $(this);
    $.ajax({
      type: 'POST', url: baseurl + 'common/updateSession', dataType: 'json',
      data: $('form#form_modal_commonsession').serialize(),
      beforeSend: function () { $this.btnLoading(); },
      success: function (data) {
        if (data.status == 1) {
          shModal('sessionModal').hide();
          window.location.href = data.redirect_url;
          successMsg('Session change successful');
        }
      },
      error:    function () { $this.btnReset(); },
      complete: function () { $this.btnReset(); }
    });
  });

  /* --- License activation form --- */
  $('#purchase_code').submit(function (e) {
    e.preventDefault();
    var form = $(this);
    var $btn = form.find('button[type=submit]');
    $.ajax({
      type: 'POST', url: form.attr('action'), data: form.serialize(), dataType: 'JSON',
      beforeSend: function () {
        $('.lic_modal-body .error_message').html('');
        $btn.btnLoading();
      },
      success: function (response, textStatus, xhr) {
        if (xhr.status != 200) {
          $('<div/>').addClass('alert alert-danger').html(response.response)
            .appendTo('.lic_modal-body .error_message');
        } else if (xhr.status == 200) {
          if (response.status == 2) {
            $.each(response.error, function (key, value) {
              $('#input-' + key).parents('.form-group').find('#error').html(value);
            });
          } else if (response.status == 1) {
            successMsg(response.message);
            window.location.href = baseurl + 'admin/admin/dashboard';
            shModal('activelicmodal').hide();
          }
        }
      },
      error: function (xhr) {
        $btn.btnReset();
        var r = jQuery.parseJSON(xhr.responseText);
        $('<div/>').addClass('alert alert-danger').html(r.response)
          .appendTo('.lic_modal-body .error_message');
      },
      complete: function () { $btn.btnReset(); }
    });
  });

}); /* end first document.ready */

/* =============================================================
   SECOND DOCUMENT READY — Android App & Addon Modals
   ============================================================= */
$(document).ready(function () {

  /* --- Android app modal --- */
  modal_click_disabled('andappModal');

  $('#andapp_code').on('submit', function (e) {
    e.preventDefault();
    var _this = $(this);
    var $this = _this.find('button[type=submit]:focus');
    $.ajax({
      type: 'POST', url: _this.attr('action'), data: _this.serialize(), dataType: 'JSON',
      beforeSend: function () {
        $('.andapp_modal-body .error_message').html('');
        $('[class^="input-error"]').html('');
        $this.btnLoading();
      },
      success: function (response, textStatus, xhr) {
        if (xhr.status != 200) {
          $('<div/>').addClass('alert alert-danger').html(response.response)
            .appendTo('.lic_modal-body .error_message');
        } else if (xhr.status == 200) {
          if (response.status == 2) {
            $.each(response.error, function (key, value) {
              $('#input-' + key).parents('.form-group').find('#error').html(value);
            });
          } else if (response.status == 1) {
            successMsg(response.message);
            window.location.href = baseurl + 'schsettings';
            shModal('andappModal').hide();
          }
        }
      },
      error: function (xhr) {
        $this.btnReset();
        var r = jQuery.parseJSON(xhr.responseText);
        $('<div/>').addClass('alert alert-danger').html(r.response)
          .appendTo('.andapp_modal-body .error_message');
      },
      complete: function () { $this.btnReset(); }
    });
  });

  /* --- Addon modal --- */
  modal_click_disabled('addonModal');
  $('#addonModal').on('shown.bs.modal', function (e) {
    $('.addon_type', this).val($(e.relatedTarget).data('addon'));
    $('.addon_version', this).val($(e.relatedTarget).data('addonVersion'));
  });
  $('#addonModal').on('hidden.bs.modal', function () {
    $('#addonModal .addon_modal-body .alert-danger').remove();
    $('#addonModal .input-error').html('');
    $('.addon_type', this).val('');
    $('.addon_version', this).val('');
  });

  $('#addon_verify').on('submit', function (e) {
    e.preventDefault();
    var _this = $(this);
    var $this = _this.find('button[type=submit]:focus');
    $.ajax({
      type: 'POST', url: _this.attr('action'), data: _this.serialize(), dataType: 'JSON',
      beforeSend: function () {
        $('.addon_modal-body .error_message').html('');
        $('[class^="input-error"]').html('');
        $this.btnLoading();
      },
      success: function (response, textStatus, xhr) {
        if (xhr.status != 200) {
          $('<div/>').addClass('alert alert-danger').html(response.response)
            .appendTo('.addon_modal-body .error_message');
        } else if (xhr.status == 200) {
          if (response.status == 2) {
            $.each(response.error, function (key, value) {
              $('#input-' + key).parents('.form-group').find('#error').html(value);
            });
          } else if (response.status == 1) {
            successMsg(response.message);
            window.location.href = response.back;
            shModal('addonModal').hide();
          }
        }
      },
      error: function (xhr) {
        $this.btnReset();
        var r = jQuery.parseJSON(xhr.responseText);
        $('<div/>').addClass('alert alert-danger').html(r.response)
          .appendTo('.addon_modal-body .error_message');
      },
      complete: function () { $this.btnReset(); }
    });
  });

}); /* end second document.ready */

/* =============================================================
   GLOBAL MESSAGE HELPERS (used by all AJAX handlers in views)
   ============================================================= */
function successMsg(msg) { toastr.success(msg); }
function errorMsg(msg)   { toastr.error(msg);   }
function infoMsg(msg)    { toastr.info(msg);    }
function warningMsg(msg) { toastr.warning(msg); }

/* =============================================================
   LANGUAGE SWITCHER — header select (onchange="set_languages(val)")
   ============================================================= */
function set_languages(lang_id) {
    if (!lang_id) return;
    window.location.href = baseurl + 'admin/language/user_language/' + lang_id;
}

/* =============================================================
   BED STATUS — header toolbar button (onclick="getbedstatus()")
   ============================================================= */
function getbedstatus() {
    var modalEl = document.getElementById('bedStatusModal');
    if (!modalEl) return;
    var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    var $body = $('#bed-status-body');
    $body.html('<div class="text-center py-5"><span class="spinner-border text-primary" role="status"></span></div>');
    modal.show();
    $.ajax({
        url:  baseurl + 'admin/patient/getBedStatus',
        type: 'GET',
        success: function (data) { $body.html(data); },
        error:   function ()     { $body.html('<div class="alert alert-danger m-3">Failed to load bed status.</div>'); }
    });
}
