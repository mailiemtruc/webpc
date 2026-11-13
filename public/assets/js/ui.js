/* Shared progressive enhancement for the storefront and management workspace. */
(function () {
  "use strict";
  // Shared category disclosure: hover preview, click to pin, native fallback.
  var catalog = document.querySelector("[data-catalog-dropdown]");
  if (catalog) {
    var catalogTrigger = catalog.querySelector("summary");
    var catalogPanel = catalog.querySelector(".catalog-dropdown-panel");
    var catalogPinned = false;
    var catalogCloseTimer;
    var catalogHover = window.matchMedia("(hover: hover) and (pointer: fine)");
    function closeCatalog(restoreFocus) {
      clearTimeout(catalogCloseTimer);
      catalogPinned = false;
      catalog.open = false;
      if (restoreFocus) catalogTrigger.focus();
    }
    catalog.addEventListener("pointerenter", function () {
      if (!catalogHover.matches) return;
      clearTimeout(catalogCloseTimer);
      catalog.open = true;
    });
    catalog.addEventListener("pointerleave", function () {
      if (!catalogHover.matches || catalogPinned) return;
      catalogCloseTimer = setTimeout(function () {
        if (!catalogPanel.contains(document.activeElement)) closeCatalog(false);
      }, 140);
    });
    catalogTrigger.addEventListener("click", function (event) {
      event.preventDefault();
      clearTimeout(catalogCloseTimer);
      catalogPinned = !catalogPinned;
      catalog.open = catalogPinned;
    });
    catalogTrigger.addEventListener("keydown", function (event) {
      if (event.key !== "ArrowDown") return;
      event.preventDefault();
      clearTimeout(catalogCloseTimer);
      catalog.open = true;
      catalogPinned = true;
      catalogPanel.querySelector("a").focus();
    });
    catalog.addEventListener("focusout", function (event) {
      if (!catalog.contains(event.relatedTarget)) closeCatalog(false);
    });
    document.addEventListener("pointerdown", function (event) {
      if (!catalog.contains(event.target)) closeCatalog(false);
    });
    document.addEventListener("keydown", function (event) {
      if (event.key === "Escape" && catalog.open) {
        event.preventDefault();
        closeCatalog(true);
      }
    });
    window
      .matchMedia("(max-width: 760px)")
      .addEventListener("change", function (event) {
        if (event.matches) closeCatalog(false);
      });
  }
  var search = document.getElementById("store-search");
  var menu = document.getElementById("mobile-menu");
  var searchTrigger = document.querySelector("[data-search-toggle]");
  var menuTrigger = document.querySelector("[data-menu-toggle]");
  function setPanel(panel, trigger, open) {
    if (!panel || !trigger) return;
    panel.hidden = !open;
    trigger.setAttribute("aria-expanded", String(open));
  }
  if (searchTrigger)
    searchTrigger.addEventListener("click", function () {
      var open = search.hidden;
      setPanel(menu, menuTrigger, false);
      setPanel(search, searchTrigger, open);
      if (open) search.querySelector("input").focus();
    });
  if (menuTrigger)
    menuTrigger.addEventListener("click", function () {
      var open = menu.hidden;
      setPanel(search, searchTrigger, false);
      setPanel(menu, menuTrigger, open);
    });
  document.addEventListener("keydown", function (event) {
    if (event.key !== "Escape") return;
    if (search && searchTrigger && !search.hidden) {
      setPanel(search, searchTrigger, false);
      searchTrigger.focus();
    }
    if (menu && !menu.hidden) {
      setPanel(menu, menuTrigger, false);
      menuTrigger.focus();
    }
  });
  function escapeHtml(str) {
    var div = document.createElement("div");
    div.textContent = str || "";
    return div.innerHTML;
  }

  document.querySelectorAll("[data-search-form]").forEach(function (form) {
    var input = form.querySelector('input[name="q"]');
    var clear = form.querySelector("[data-search-clear]");
    var wrap = form.querySelector(".search-input-wrap") || form;
    var baseApp = document.body.getAttribute("data-app-base") || "";

    // Gợi ý tìm kiếm nhanh (Ajax Live Search)
    var dropdown = document.createElement("div");
    dropdown.className = "header-search-suggestions";
    dropdown.hidden = true;
    wrap.appendChild(dropdown);

    var debounceTimer = null;
    var abortController = null;

    function hideDropdown() {
      dropdown.hidden = true;
      dropdown.innerHTML = "";
    }

    function update() {
      if (clear) clear.hidden = !input.value.length;
    }

    if (clear) {
      clear.addEventListener("click", function () {
        input.value = "";
        update();
        hideDropdown();
        input.focus();
      });
    }

    input.addEventListener("input", function () {
      update();
      var query = input.value.trim();
      clearTimeout(debounceTimer);
      if (abortController) {
        abortController.abort();
      }

      if (query.length < 2) {
        hideDropdown();
        return;
      }

      debounceTimer = setTimeout(function () {
        abortController = new AbortController();
        var searchUrl = baseApp.replace(/\/+$/, "") + "/api/search?q=" + encodeURIComponent(query);
        fetch(searchUrl, { signal: abortController.signal })
          .then(function (res) { return res.json(); })
          .then(function (items) {
            if (!Array.isArray(items) || items.length === 0) {
              dropdown.innerHTML = '<div class="search-suggest-empty">Không tìm thấy sản phẩm khớp với "<strong>' + escapeHtml(query) + '</strong>"</div>';
              dropdown.hidden = false;
              return;
            }

            var html = '<div class="search-suggest-header">Gợi ý sản phẩm</div><div class="search-suggest-list">';
            items.forEach(function (item) {
              html += '<a href="' + escapeHtml(item.url) + '" class="search-suggest-item">';
              html += '<img src="' + escapeHtml(item.thumbnail) + '" alt="" width="44" height="44" onerror="this.onerror=null;this.src=\'' + baseApp.replace(/\/+$/, '') + '/assets/images/product-placeholder.svg\';">';
              html += '<div class="search-suggest-info">';
              html += '<span class="search-suggest-title">' + escapeHtml(item.name) + '</span>';
              html += '<div class="search-suggest-price-box">';
              html += '<span class="search-suggest-price">' + escapeHtml(item.price_formatted) + '</span>';
              if (item.old_price_formatted) {
                html += '<span class="search-suggest-old-price">' + escapeHtml(item.old_price_formatted) + '</span>';
              }
              html += '</div></div></a>';
            });
            html += '</div>';
            html += '<a class="search-suggest-view-all" href="' + baseApp.replace(/\/+$/, "") + '/san-pham?q=' + encodeURIComponent(query) + '">Xem tất cả kết quả cho "<strong>' + escapeHtml(query) + '</strong>" →</a>';
            dropdown.innerHTML = html;
            dropdown.hidden = false;
          })
          .catch(function (err) {
            if (err.name !== "AbortError") {
              hideDropdown();
            }
          });
      }, 250);
    });

    input.addEventListener("keydown", function (event) {
      if (event.key === "Enter" && event.isComposing) event.preventDefault();
      if (event.key === "Escape") hideDropdown();
    });

    document.addEventListener("click", function (e) {
      if (!form.contains(e.target)) {
        hideDropdown();
      }
    });

    update();
  });
  document
    .querySelectorAll("[data-password-toggle]")
    .forEach(function (button) {
      var input = document.getElementById(button.getAttribute("aria-controls"));
      if (!input) return;
      button.addEventListener("click", function () {
        var visible = input.type === "text";
        input.type = visible ? "password" : "text";
        button.setAttribute("aria-pressed", String(!visible));
        button.setAttribute(
          "aria-label",
          visible ? "Hiện mật khẩu" : "Ẩn mật khẩu",
        );
        button.querySelector("i").className = visible
          ? "fa-regular fa-eye"
          : "fa-regular fa-eye-slash";
        input.focus();
      });
    });
  document.querySelectorAll("[data-validate-form]").forEach(function (form) {
    var submitted = false;
    var fields = Array.from(
      form.querySelectorAll('input:not([type="hidden"])'),
    );
    var draftKey = "pcmarket-form:" + window.location.pathname;
    var nonSecret = fields.filter(function (input) {
      return ["email", "full_name", "phone"].includes(input.name);
    });
    try {
      var draft = JSON.parse(sessionStorage.getItem(draftKey) || "{}");
      if (document.querySelector(".flash-error"))
        nonSecret.forEach(function (input) {
          input.value = draft[input.name] || "";
        });
      sessionStorage.removeItem(draftKey);
    } catch (error) {
      /* Forms remain usable when browser storage is unavailable. */
    }
    function validate(input) {
      var message = "";
      if (input.required && !input.value.trim())
        message = "Vui lòng điền thông tin này.";
      else if (input.type === "email" && input.validity.typeMismatch)
        message = "Vui lòng nhập địa chỉ email hợp lệ.";
      else if (
        input.name === "password" &&
        input.minLength > 0 &&
        input.value.length < input.minLength
      )
        message = "Mật khẩu cần ít nhất " + input.minLength + " ký tự.";
      else if (
        input.name === "password_confirmation" &&
        input.value !== form.querySelector('[name="password"]').value
      )
        message = "Mật khẩu xác nhận chưa trùng khớp.";
      var error = document.getElementById(input.id + "-error");
      input.setAttribute("aria-invalid", String(Boolean(message)));
      if (error) {
        error.textContent = message;
        error.hidden = !message;
        if (message) input.setAttribute("aria-describedby", error.id);
        else input.removeAttribute("aria-describedby");
      }
      return !message;
    }
    form.addEventListener("submit", function (event) {
      submitted = true;
      var validity = fields.map(validate);
      var firstInvalid = validity.indexOf(false);
      if (firstInvalid !== -1) {
        event.preventDefault();
        fields[firstInvalid].focus();
        return;
      }
      try {
        var draft = {};
        nonSecret.forEach(function (input) {
          draft[input.name] = input.value;
        });
        sessionStorage.setItem(draftKey, JSON.stringify(draft));
      } catch (error) {
        /* Server submission does not depend on storage. */
      }
      var submit = form.querySelector('[type="submit"]');
      submit.style.minWidth = submit.offsetWidth + "px";
      submit.style.minHeight = submit.offsetHeight + "px";
      submit.disabled = true;
      submit.setAttribute("aria-busy", "true");
      submit.textContent = "Đang xử lý…";
    });
    fields.forEach(function (input) {
      input.addEventListener("input", function () {
        if (!submitted) return;
        validate(input);
        if (input.name === "password") {
          var confirmation = form.querySelector(
            '[name="password_confirmation"]',
          );
          if (confirmation && confirmation.value) validate(confirmation);
        }
      });
    });
  });
  window.addEventListener("pageshow", function (event) {
    if (event.persisted) window.location.reload();
  });
  document.querySelectorAll("[data-dismiss]").forEach(function (button) {
    button.addEventListener("click", function () {
      button.closest(".flash").remove();
    });
  });
  var dialog = document.getElementById("feature-dialog");
  if (dialog) {
    var dialogTrigger = null;
    dialog.addEventListener("close", function () {
      if (dialogTrigger) dialogTrigger.focus();
    });
    document.querySelectorAll("[data-feature]").forEach(function (button) {
      button.addEventListener("click", function () {
        dialogTrigger = button;
        var builder = button.dataset.feature === "builder";
        document.getElementById("feature-title").textContent = builder
          ? "Công cụ xây dựng cấu hình PC sắp ra mắt."
          : "Chức năng đang được nâng cấp.";
        document.getElementById("feature-message").textContent = builder
          ? "Tính năng chọn linh kiện kiểm tra tương thích đang được cập nhật. Trong lúc chờ, hãy khám phá các bộ PC và linh kiện tại cửa hàng."
          : "Bạn có thể thêm sản phẩm trực tiếp vào giỏ hàng tại trang chi tiết hoặc danh mục sản phẩm.";
        dialog.showModal();
      });
    });
    dialog
      .querySelector("[data-dialog-close]")
      .addEventListener("click", function () {
        dialog.close();
      });
    dialog.addEventListener("click", function (event) {
      if (event.target === dialog) {
        var box = dialog.getBoundingClientRect();
        if (
          event.clientX < box.left ||
          event.clientX > box.right ||
          event.clientY < box.top ||
          event.clientY > box.bottom
        )
          dialog.close();
      }
    });
  }
  document.querySelectorAll("[data-product-image]").forEach(function (image) {
    function fallback() {
      if (image.dataset.fallback) return;
      image.dataset.fallback = "true";
      image.src = new URL(
        "../images/product-placeholder.svg",
        document.querySelector('script[src*="ui.js"]').src,
      ).href;
    }
    image.addEventListener("error", fallback);
    if (image.complete && image.naturalWidth === 0) fallback();
  });
  var sidebar = document.getElementById("admin-sidebar");
  var adminTrigger = document.querySelector("[data-admin-menu]");
  var backdrop = document.querySelector("[data-admin-backdrop]");
  function closeSidebar() {
    if (!sidebar || !adminTrigger) return;
    sidebar.classList.remove("is-open");
    adminTrigger.setAttribute("aria-expanded", "false");
    if (backdrop) backdrop.hidden = true;
    if (window.innerWidth < 992) sidebar.inert = true;
  }
  if (sidebar && adminTrigger) {
    function adaptSidebar() {
      sidebar.inert =
        window.innerWidth < 992 && !sidebar.classList.contains("is-open");
      if (window.innerWidth >= 992 && backdrop) backdrop.hidden = true;
    }
    adaptSidebar();
    window.addEventListener("resize", adaptSidebar);
    adminTrigger.addEventListener("click", function () {
      var open = !sidebar.classList.contains("is-open");
      sidebar.classList.toggle("is-open", open);
      sidebar.inert = !open;
      adminTrigger.setAttribute("aria-expanded", String(open));
      backdrop.hidden = !open;
      if (open) sidebar.querySelector("a").focus();
    });
    if (backdrop)
      backdrop.addEventListener("click", function () {
        closeSidebar();
        adminTrigger.focus();
      });
    document.addEventListener("keydown", function (event) {
      if (event.key === "Escape" && sidebar.classList.contains("is-open")) {
        closeSidebar();
        adminTrigger.focus();
      }
      if (
        event.key === "Tab" &&
        window.innerWidth < 992 &&
        sidebar.classList.contains("is-open")
      ) {
        var focusable = sidebar.querySelectorAll("a, button:not(:disabled)");
        var first = focusable[0],
          last = focusable[focusable.length - 1];
        if (event.shiftKey && document.activeElement === first) {
          event.preventDefault();
          last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
          event.preventDefault();
          first.focus();
        }
      }
    });
  }

  /* Chuyển đổi giữa mã Authenticator (TOTP) và mã dự phòng (kiểu Discord) ở trang 2FA. */
  var tfaTabs = document.querySelectorAll("[data-tfa-tab]");
  if (tfaTabs.length) {
    var tfaPanels = document.querySelectorAll("[data-tfa-panel]");
    tfaTabs.forEach(function (tab) {
      tab.addEventListener("click", function () {
        var mode = tab.getAttribute("data-tfa-tab");
        tfaTabs.forEach(function (t) {
          var active = t === tab;
          t.classList.toggle("is-active", active);
          t.setAttribute("aria-selected", String(active));
        });
        tfaPanels.forEach(function (panel) {
          var match = panel.getAttribute("data-tfa-panel") === mode;
          panel.hidden = !match;
          if (match) {
            var input = panel.querySelector('input:not([type="hidden"])');
            if (input) input.focus();
          }
        });
      });
    });
  }

  /* Nút sao chép khóa bí mật / bộ 10 mã dự phòng & tải file .txt. */
  document.querySelectorAll("[data-copy-text]").forEach(function (btn) {
    btn.addEventListener("click", function () {
      var text = btn.getAttribute("data-copy-text") || "";
      if (!text || !navigator.clipboard) return;
      var orig = btn.innerHTML;
      navigator.clipboard.writeText(text).then(function () {
        btn.textContent = "Đã sao chép ✓";
        setTimeout(function () {
          btn.innerHTML = orig;
        }, 1800);
      });
    });
  });

  document.querySelectorAll("[data-download-text]").forEach(function (btn) {
    btn.addEventListener("click", function () {
      var text = btn.getAttribute("data-download-text") || "";
      var filename = btn.getAttribute("data-download-name") || "pcmarket-backup-codes.txt";
      var blob = new Blob([text], { type: "text/plain;charset=utf-8" });
      var url = URL.createObjectURL(blob);
      var link = document.createElement("a");
      link.href = url;
      link.download = filename;
      document.body.appendChild(link);
      link.click();
      link.remove();
      setTimeout(function () {
        URL.revokeObjectURL(url);
      }, 1000);
    });
  });

  /* Đóng popover menu tài khoản khi nhấn ra ngoài hoặc bấm phím Escape */
  document.addEventListener("pointerdown", function (event) {
    var accountMenu = document.querySelector(".account-menu");
    if (accountMenu && accountMenu.open && !accountMenu.contains(event.target)) {
      accountMenu.open = false;
    }
  });
  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") {
      var accountMenu = document.querySelector(".account-menu");
      if (accountMenu && accountMenu.open) {
        accountMenu.open = false;
      }
    }
  });
})();
