/* Travel Oceans - Main JS */
(function ($) {
  'use strict';

  // Init AOS
  if (typeof AOS !== 'undefined') {
    AOS.init({
      duration: 800,
      once: true,
      offset: 60,
    });
  }

  // Hero Slider (Owl)
  $(function () {
    if ($('.hero-slider').length) {
      $('.hero-slider').owlCarousel({
        items: 1,
        loop: true,
        autoplay: true,
        autoplayTimeout: 6000,
        autoplayHoverPause: true,
        animateOut: 'fadeOut',
        dots: true,
        nav: false,
        smartSpeed: 800,
      });
    }

    // Testimonials
    if ($('.testimonial-slider').length) {
      $('.testimonial-slider').owlCarousel({
        loop: true,
        margin: 24,
        autoplay: true,
        autoplayTimeout: 5000,
        dots: true,
        nav: false,
        responsive: {
          0:    { items: 1 },
          768:  { items: 2 },
          1200: { items: 3 },
        },
      });
    }
  });

  // Back to top
  $(window).on('scroll', function () {
    if ($(this).scrollTop() > 200) {
      $('#backToTop').addClass('show');
    } else {
      $('#backToTop').removeClass('show');
    }
  });
  $('#backToTop').on('click', function () {
    $('html, body').animate({ scrollTop: 0 }, 600);
  });

  // Smooth scroll for hash links
  $(document).on('click', 'a[href^="#"]:not([href="#"])', function (e) {
    var target = $(this).attr('href');
    if ($(target).length) {
      e.preventDefault();
      $('html, body').animate({ scrollTop: $(target).offset().top - 80 }, 600);
    }
  });

  // AJAX contact form
  $(document).on('submit', '.ajax-form', function (e) {
    e.preventDefault();
    var $form = $(this);
    var $btn = $form.find('button[type="submit"]');
    var oldText = $btn.html();
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Sending…');

    $.ajax({
      url: $form.attr('action') || 'api/contact.php',
      type: 'POST',
      data: $form.serialize(),
      dataType: 'json',
      success: function (r) {
        if (r.success) {
          $form.find('.form-message').html('<div class="alert alert-success">' + r.message + '</div>');
          $form[0].reset();
        } else {
          $form.find('.form-message').html('<div class="alert alert-danger">' + (r.message || 'Something went wrong.') + '</div>');
        }
      },
      error: function () {
        $form.find('.form-message').html('<div class="alert alert-danger">Network error. Please try again.</div>');
      },
      complete: function () {
        $btn.prop('disabled', false).html(oldText);
        setTimeout(function () {
          $form.find('.form-message').fadeOut(400, function () { $(this).html('').show(); });
        }, 5000);
      },
    });
  });

  // Newsletter form
  $(document).on('submit', '.newsletter-form', function (e) {
    e.preventDefault();
    var $f = $(this);
    $.ajax({
      url: 'api/newsletter.php',
      type: 'POST',
      data: $f.serialize(),
      dataType: 'json',
      success: function (r) {
        $f.find('.newsletter-message').html('<small class="' + (r.success ? 'text-success' : 'text-danger') + '">' + r.message + '</small>');
        if (r.success) $f[0].reset();
      }
    });
  });

  // Counter animation
  function animateCounters() {
    $('.counter').each(function () {
      var $el = $(this);
      if ($el.data('animated')) return;
      var rect = $el.get(0).getBoundingClientRect();
      if (rect.top < window.innerHeight - 60) {
        $el.data('animated', true);
        var target = parseInt($el.data('target'), 10);
        var suffix = $el.data('suffix') || '';
        var current = 0;
        var step = Math.max(1, Math.floor(target / 60));
        var timer = setInterval(function () {
          current += step;
          if (current >= target) { current = target; clearInterval(timer); }
          $el.text(current.toLocaleString() + suffix);
        }, 30);
      }
    });
  }
  $(window).on('scroll resize', animateCounters);
  $(animateCounters);

  // Admin sidebar toggle
  $(document).on('click', '#adminSidebarToggle', function () {
    $('.admin-sidebar').toggleClass('show');
  });

})(jQuery);
