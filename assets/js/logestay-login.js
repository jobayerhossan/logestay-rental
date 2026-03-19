jQuery(document).ready(function ($) {

  function openForgotModal() {
    $('#logestay-forgot-modal').removeAttr('hidden');
    $('body').css('overflow', 'hidden');
  }

  function closeForgotModal() {
    $('#logestay-forgot-modal').attr('hidden', true);
    $('body').css('overflow', '');
  }


  $('.logestay-password-toggle').on('click', function () {
    var $btn = $(this);
    var $input = $('#password');
    var $showIcon = $btn.find('.icon-show');
    var $hideIcon = $btn.find('.icon-hide');

    if ($input.attr('type') === 'password') {
      $input.attr('type', 'text');
      $btn.attr('aria-label', $btn.data('hide-label'));
      $showIcon.hide();
      $hideIcon.show();
    } else {
      $input.attr('type', 'password');
      $btn.attr('aria-label', $btn.data('show-label'));
      $showIcon.show();
      $hideIcon.hide();
    }
  });

  $('.logestay-forgot-trigger').on('click', function (e) {
    e.preventDefault();
    openForgotModal();
  });

  $('.logestay-forgot-close, .logestay-forgot-cancel, .logestay-forgot-overlay').on('click', function (e) {
    e.preventDefault();
    closeForgotModal();
  });

  $(document).on('keydown', function (e) {
    if (e.key === 'Escape') {
      closeForgotModal();
      closeLanguageDropdown();
    }
  });


  $('#logestay-login-form').on('submit', function (e) {
    e.preventDefault();

    var $form = $(this);
    var $submit = $form.find('.logestay-login-submit');
    var $identifier = $form.find('.logestay-identifier');
    var $password = $form.find('.logestay-password');

    // basic validation
    if (!$identifier.val() || !$password.val()) {
      $identifier.focus();
      return;
    }

    $submit.prop('disabled', true).addClass('loading');
    $('.logestay-login-error').addClass('hidden');

    $.ajax({
      url: logestay_ajax.ajax_url,
      type: 'POST',
      data: {
        action: 'logestay_ajax_login',
        log: $identifier.val(),
        pwd: $password.val(),
        remember: $form.find('.logestay-remember').is(':checked'),
        security: $form.find('#logestay_login_nonce').val()
      },
      success: function (response) {

        if (response.success) {
          window.location.href = logestay_ajax.redirect;
        } else {
          showLoginError(response.data);
          $('.logestay-login-error').removeClass('hidden');
          $submit.prop('disabled', false).removeClass('loading');
        }
      },
      error: function () {
        showLoginError('Something went wrong. Please try again.');
        $submit.prop('disabled', false).removeClass('loading');
      }
    });

  });

  function showLoginError(message) {
    var $error = $('.logestay-login-error');
    $error.find('.logestay-login-error-text').text(message).show();
  }


  $('.logestay-forgot-form').on('submit', function (e) {
    e.preventDefault();

    var $form = $(this);
    var $email = $form.find('.logestay-forgot-email');
    var $submit = $form.find('.logestay-forgot-submit');
    var $spinner = $form.find('.logestay-forgot-submit-spinner');
    var $errorBox = $('.logestay-forgot-error');
    var $errorText = $('.logestay-forgot-error-text');

    // reset UI
    $errorBox.addClass('hidden');

    if (!$email.val()) {
      $email.trigger('focus');
      return;
    }

    $submit.prop('disabled', true);
    $spinner.show();

    $.ajax({
      url: logestay_ajax.ajax_url,
      type: 'POST',
      data: {
        action: 'logestay_ajax_forgot_password',
        user_login: $email.val(),
        security: $form.find('#logestay_forgot_nonce').val()
      },
      success: function (response) {

        $submit.prop('disabled', false);
        $spinner.hide();

        if (response.success) {

          // hide form
          $form.hide();

          // show success block (you already have it)
          $('.logestay-forgot-success').removeClass('hidden');

        } else {
          $errorText.text(response.data);
          $errorBox.removeClass('hidden');
        }
      },
      error: function () {
        $submit.prop('disabled', false);
        $spinner.hide();

        $errorText.text('Something went wrong. Please try again.');
        $errorBox.removeClass('hidden');
      }
    });
  });


  $('.logestay-reset-form').on('submit', function(e){
      e.preventDefault();

      var $form = $(this);
      var error = $form.find('.logestay-reset-error');
      var success = $form.find('.logestay-reset-success');

      error.addClass('hidden');
      success.addClass('hidden');

      $.ajax({
          url: logestay_ajax.ajax_url,
          type: 'POST',
          data: {
              action: 'logestay_ajax_reset_password',
              security: $('#logestay_reset_nonce').val(),
              reset_key: $form.find('[name="reset_key"]').val(),
              login: $form.find('[name="login"]').val(),
              password: $form.find('[name="password"]').val(),
              confirm_password: $form.find('[name="confirm_password"]').val()
          },
          success: function(res){

              if(res.success){

                  success.removeClass('hidden');

                  setTimeout(function(){
                      $('.logestay-reset-modal').fadeOut();
                      window.location.href = '/login/';
                  }, 2500);

              } else {
                  error.text(res.data).removeClass('hidden');
              }

          }
      });

  });

  if (window.logestayLoginConfig && window.logestayLoginConfig.openForgotModal) {
    openForgotModal();
  }
});