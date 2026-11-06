/*
 * Progressive motion enhancement. No data access, business logic or dependency.
 * Shared by client/admin layouts; respects the OS preference at runtime.
 */
(function () {
  "use strict";
  const preference = window.matchMedia("(prefers-reduced-motion: reduce)");
  let observer = null;
  const targets = new Set();
  function reveal(element) {
    element.classList.add("is-revealed");
    if (observer) observer.unobserve(element);
  }
  function enable() {
    if (preference.matches || !("IntersectionObserver" in window)) return;
    document.documentElement.classList.add("motion-enabled");
    observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) reveal(entry.target);
        });
      },
      { threshold: 0.06, rootMargin: "0px 0px -24px 0px" },
    );
    const groups = [
      [".shopping-assurance > div", 65],
      [".needs-grid > .need-card", 65],
      [".product-grid > .shop-product, .product-grid > .build-pc-tile", 55],
      [".component-links > a", 35],
      [".metrics-grid > .metric", 60],
      [".dashboard-columns > .admin-panel", 60],
    ];
    const add = (element, delay) => {
      if (targets.has(element)) return;
      targets.add(element);
      if (element.getBoundingClientRect().top < window.innerHeight - 24) {
        // Above-the-fold content stays immediately visible; hero owns its entrance.
        return;
      }
      element.style.setProperty("--reveal-delay", delay + "ms");
      element.classList.add("motion-reveal");
      observer.observe(element);
    };
    groups.forEach(([selector, gap]) => {
      document
        .querySelectorAll(selector)
        .forEach((element, index) => add(element, (index % 4) * gap));
    });
    document
      .querySelectorAll(
        ".section-heading, .match-heading, .builder-banner, .detail-specs, .orders-panel",
      )
      .forEach((element) => add(element, 0));
  }
  function disable() {
    document.documentElement.classList.remove("motion-enabled");
    if (observer) observer.disconnect();
    targets.forEach((element) => {
      element.classList.remove("motion-reveal", "is-revealed");
      element.style.removeProperty("--reveal-delay");
    });
    targets.clear();
    observer = null;
  }
  document.addEventListener("focusin", (event) => {
    const hidden = event.target.closest(".motion-reveal:not(.is-revealed)");
    if (hidden) reveal(hidden);
  });
  preference.addEventListener("change", () => {
    disable();
    enable();
  });
  window.addEventListener("pageshow", (event) => {
    if (event.persisted) targets.forEach(reveal);
  });
  enable();
})();
