/* Canonical loading states for server-rendered navigation and image downloads. */
(function () {
  "use strict";
  const main = document.getElementById("main-content");
  const overlay = document.getElementById("page-skeleton");
  const status = document.getElementById("navigation-loading-status");
  const appBase = document.body.dataset.appBase;
  let startTimer, recoveryTimer;
  function stop() {
    clearTimeout(startTimer);
    clearTimeout(recoveryTimer);
    if (overlay) overlay.hidden = true;
    if (main) main.removeAttribute("aria-busy");
    if (status) status.textContent = "";
  }
  function queue(url) {
    if (!overlay || !appBase || !main) return;
    const base = new URL(appBase);
    if (
      url.origin !== base.origin ||
      !url.pathname.startsWith(base.pathname.replace(/\/$/, "") + "/")
    ) {
      if (url.href.replace(/\/$/, "") !== base.href.replace(/\/$/, "")) return;
    }
    const route =
      url.pathname
        .slice(base.pathname.replace(/\/$/, "").length)
        .replace(/\/$/, "") || "/";
    const layout =
      route === "/san-pham"
        ? "catalog"
        : /^\/san-pham\/[^/]+$/.test(route)
          ? "detail"
          : null;
    if (!layout) return;
    stop();
    startTimer = setTimeout(() => {
      overlay.dataset.layout = layout;
      overlay.hidden = false;
      main.setAttribute("aria-busy", "true");
      status.textContent =
        "Đang tải " +
        (layout === "catalog" ? "danh sách sản phẩm…" : "chi tiết sản phẩm…");
    }, 120);
    // Do not trap the current document if navigation is cancelled or stalls.
    recoveryTimer = setTimeout(stop, 10000);
  }
  document.addEventListener("click", (event) => {
    if (
      event.defaultPrevented ||
      event.button !== 0 ||
      event.metaKey ||
      event.ctrlKey ||
      event.shiftKey ||
      event.altKey
    )
      return;
    const link = event.target.closest("a[href]");
    if (
      !link ||
      link.hasAttribute("download") ||
      (link.target && link.target !== "_self")
    )
      return;
    const url = new URL(link.href, location.href);
    if (
      url.origin === location.origin &&
      url.pathname === location.pathname &&
      url.search === location.search &&
      url.hash
    )
      return;
    queue(url);
  });
  document.addEventListener("submit", (event) => {
    if (event.defaultPrevented || event.target.method.toLowerCase() !== "get")
      return;
    queue(new URL(event.target.action, location.href));
  });
  window.addEventListener("pageshow", stop);
  window.addEventListener("pagehide", stop);
  window.addEventListener("keydown", (event) => {
    if (event.key === "Escape") stop();
  });
  document
    .querySelectorAll(
      ".product-image > img, .detail-photo > img, .campaign-visual > img",
    )
    .forEach((image) => {
      if (image.complete) return;
      const frame = image.parentElement;
      frame.classList.add("media-loading");
      const done = () => {
        frame.classList.remove("media-loading");
        frame.removeAttribute("aria-busy");
      };
      frame.setAttribute("aria-busy", "true");
      image.addEventListener("load", done, { once: true });
      image.addEventListener("error", done, { once: true });
    });
})();
