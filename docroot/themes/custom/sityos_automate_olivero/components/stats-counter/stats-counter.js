/**
 * @file
 * Stats Counter — IntersectionObserver-based count-up animation.
 *
 * Animates numeric values from 0 to target when entering the viewport.
 * Respects prefers-reduced-motion.
 */

(function (Drupal, once) {
  'use strict';

  /**
   * Animate a single counter from 0 to target.
   *
   * @param {HTMLElement} el - The element displaying the number.
   * @param {number} target - Target value.
   * @param {number} duration - Animation duration in ms.
   */
  function animateCounter(el, target, duration) {
    var startTime = null;

    function step(timestamp) {
      if (!startTime) {
        startTime = timestamp;
      }

      var progress = Math.min((timestamp - startTime) / duration, 1);
      // Ease-out cubic for deceleration.
      var eased = 1 - Math.pow(1 - progress, 3);
      var current = Math.round(eased * target);

      el.textContent = current.toLocaleString();

      if (progress < 1) {
        requestAnimationFrame(step);
      }
    }

    requestAnimationFrame(step);
  }

  Drupal.behaviors.saoStatsCounter = {
    attach: function (context) {
      var counters = once(
        'sao-stats-counter',
        '[data-sao-counter]',
        context
      );

      if (!counters.length) {
        return;
      }

      // Check reduced motion preference.
      var prefersReducedMotion =
        window.matchMedia &&
        window.matchMedia('(prefers-reduced-motion: reduce)').matches;

      if (prefersReducedMotion) {
        // Show final values immediately.
        counters.forEach(function (counter) {
          var target = parseInt(counter.getAttribute('data-sao-counter-target'), 10);
          var valueEl = counter.querySelector('[data-sao-counter-value]');
          if (valueEl && !isNaN(target)) {
            valueEl.textContent = target.toLocaleString();
          }
        });
        return;
      }

      // Use IntersectionObserver for viewport detection.
      var observer = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              var counter = entry.target;
              var target = parseInt(counter.getAttribute('data-sao-counter-target'), 10);
              var valueEl = counter.querySelector('[data-sao-counter-value]');

              if (valueEl && !isNaN(target)) {
                animateCounter(valueEl, target, 1500);
              }

              observer.unobserve(counter);
            }
          });
        },
        { threshold: 0.3 }
      );

      counters.forEach(function (counter) {
        observer.observe(counter);
      });
    },
  };
})(Drupal, once);
