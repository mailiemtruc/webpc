/* Canonical PC comparison owner. Persist IDs only; refresh public catalog data. */
(function () {
  "use strict";
  const tray = document.querySelector("[data-compare-tray]");
  const dialog = document.querySelector("[data-compare-dialog]");
  if (!tray || !dialog) return;
  const key = "pcmarket.compare.v1";
  const registry = new Map();
  const base = document.body.dataset.appBase.replace(/\/$/, "");
  const money = (value) => new Intl.NumberFormat("vi-VN").format(value) + " ₫";
  const fields = [
    ["cpu", "CPU"],
    ["vga", "Card đồ họa"],
    ["ram", "RAM"],
    ["storage", "Lưu trữ"],
    ["mainboard", "Bo mạch chủ"],
    ["psu", "Nguồn"],
    ["cooling", "Tản nhiệt"],
    ["case", "Vỏ case"],
    ["price", "Giá bán"],
    ["quantity", "Tồn kho"],
  ];
  let ids = [],
    request = null,
    busy = false,
    message = "",
    failed = false,
    opener = null;
  function el(tag, text, className) {
    const node = document.createElement(tag);
    if (text !== undefined) node.textContent = text;
    if (className) node.className = className;
    return node;
  }
  function cleanIds(value) {
    return Array.isArray(value)
      ? [
          ...new Set(value.filter((id) => Number.isSafeInteger(id) && id > 0)),
        ].slice(0, 4)
      : [];
  }
  function read() {
    try {
      return cleanIds(JSON.parse(localStorage.getItem(key) || "[]"));
    } catch (_) {
      return [];
    }
  }
  function save() {
    try {
      localStorage.setItem(key, JSON.stringify(ids));
    } catch (_) {
      message =
        "Trình duyệt không cho lưu lựa chọn; danh sách chỉ được giữ trong trang này.";
    }
  }
  function normalize(value) {
    if (
      !value ||
      !Number.isSafeInteger(value.id) ||
      value.id < 1 ||
      ![1, 2, 3].includes(Number(value.category)) ||
      !Number.isFinite(Number(value.price)) ||
      Number(value.price) < 0
    )
      return null;
    try {
      const url = new URL(value.url, location.href);
      if (
        url.origin !== location.origin ||
        !url.pathname.includes("/san-pham/")
      )
        return null;
      return {
        id: value.id,
        name: String(value.name || "PC").slice(0, 600),
        category: Number(value.category),
        price: Number(value.price),
        quantity: Number.isFinite(Number(value.quantity))
          ? Number(value.quantity)
          : null,
        url: url.href,
        specs:
          value.specs && typeof value.specs === "object" ? value.specs : {},
      };
    } catch (_) {
      return null;
    }
  }
  function get() {
    return {
      ids: [...ids],
      items: ids.map((id) => registry.get(id)).filter(Boolean),
      busy,
    };
  }
  function updateButtons() {
    document.querySelectorAll("[data-pc-compare]").forEach((button) => {
      const product = normalize(JSON.parse(button.dataset.pcCompare));
      if (!product) return;
      const selected = ids.includes(product.id);
      button.hidden = false;
      button.setAttribute("aria-pressed", String(selected));
      button.disabled = !selected && ids.length >= 4;
      button.querySelector("span").textContent = selected
        ? "Đã chọn so sánh"
        : "So sánh PC";
      button.setAttribute(
        "aria-label",
        (selected ? "Bỏ so sánh " : "Thêm so sánh ") + product.name,
      );
    });
  }
  function renderTable() {
    const products = get().items;
    const head = el("tr");
    const first = el("th", "Thông số");
    first.scope = "col";
    head.append(first);
    products.forEach((product) => {
      const th = el("th", undefined, "compare-product-heading");
      th.scope = "col";
      const link = el("a", product.name);
      link.href = product.url;
      const remove = el("button", "Bỏ máy này", "text-button");
      remove.type = "button";
      remove.setAttribute("aria-label", "Bỏ so sánh " + product.name);
      remove.addEventListener("click", () => removeId(product.id));
      th.append(link, remove);
      head.append(th);
    });
    dialog.querySelector("[data-compare-head]").replaceChildren(head);
    const rows = [];
    const onlyDifferences = dialog.querySelector(
      "[data-compare-differences]",
    ).checked;
    fields.forEach(([field, label]) => {
      const values = products.map((product) => {
        if (field === "price") return money(product.price);
        if (field === "quantity")
          return product.quantity === null
            ? "Chưa cập nhật"
            : product.quantity > 0
              ? "Còn hàng (" + product.quantity + ")"
              : "Tạm hết hàng";
        const value = product.specs[field];
        return typeof value === "string" || typeof value === "number"
          ? String(value)
          : "Chưa có thông tin";
      });
      const different = new Set(values).size > 1;
      if (onlyDifferences && !different) return;
      const row = el("tr", undefined, different ? "compare-different" : "");
      const labelCell = el("th", label);
      labelCell.scope = "row";
      if (different) labelCell.append(el("small", "Khác nhau"));
      row.append(labelCell);
      values.forEach((value) => row.append(el("td", value)));
      rows.push(row);
    });
    if (!rows.length) {
      const row = el("tr"),
        cell = el("td", "Không có khác biệt trong các thông số đã cung cấp.");
      cell.colSpan = products.length + 1;
      row.append(cell);
      rows.push(row);
    }
    dialog.querySelector("[data-compare-body]").replaceChildren(...rows);
    dialog.querySelector(".compare-table").style.minWidth =
      140 + products.length * 250 + "px";
    dialog
      .querySelector(".compare-table-scroll")
      .setAttribute("aria-busy", String(busy));
    const prices = products.map((product) => product.price);
    dialog.querySelector("[data-compare-summary]").textContent =
      products.length >= 2
        ? products.length +
          " PC · Chênh lệch thấp–cao: " +
          money(Math.max(...prices) - Math.min(...prices))
        : "";
  }
  function sync() {
    const active = document.activeElement;
    tray.hidden = ids.length === 0 && !message;
    document.body.classList.toggle("compare-active", !tray.hidden);
    tray.querySelector("[data-compare-count]").textContent = ids.length + "/4";
    const chips = ids.map((id) => {
      const product = registry.get(id);
      const chip = el("div", undefined, "compare-chip");
      chip.append(
        el(
          "span",
          product
            ? product.name
            : busy
              ? "Đang tải PC #" + id
              : "Chưa tải được PC #" + id,
        ),
      );
      const remove = el("button", "×");
      remove.type = "button";
      remove.setAttribute(
        "aria-label",
        "Bỏ so sánh " + (product ? product.name : "PC #" + id),
      );
      remove.addEventListener("click", () => removeId(id));
      chip.append(remove);
      return chip;
    });
    tray.querySelector("[data-compare-items]").replaceChildren(...chips);
    tray.querySelector("[data-compare-open]").disabled = get().items.length < 2;
    tray.querySelector("[data-compare-retry]").hidden = !failed || busy;
    dialog.querySelector("[data-compare-dialog-status]").textContent =
      message || (busy ? "Đang cập nhật thông số…" : "");
    tray.querySelector("[data-compare-status]").textContent =
      message ||
      (ids.length === 1
        ? "Chọn thêm 1 PC để bắt đầu so sánh."
        : busy
          ? "Đang cập nhật thông số…"
          : "Chọn tối đa 4 PC từ các trang sản phẩm.");
    updateButtons();
    if (dialog.open) {
      if (get().items.length < 2) dialog.close();
      else renderTable();
    }
    if (active && !active.isConnected) {
      const next = dialog.open
        ? dialog.querySelector("[data-compare-close]")
        : tray.hidden
          ? document.querySelector("[data-pc-compare]")
          : tray.querySelector("button");
      if (next) next.focus();
    }
    window.dispatchEvent(
      new CustomEvent("pccompare:change", { detail: get() }),
    );
  }
  function removeId(id) {
    ids = ids.filter((value) => value !== id);
    message = "";
    failed = false;
    save();
    sync();
    if (busy) hydrate();
  }
  function add(value) {
    const product = normalize(value);
    if (!product) return false;
    registry.set(product.id, product);
    if (ids.includes(product.id)) return true;
    if (ids.length >= 4) {
      message = "Đã chọn đủ 4 PC. Bỏ một máy trước khi thêm cấu hình khác.";
      sync();
      return false;
    }
    ids.push(product.id);
    message = "";
    failed = false;
    save();
    sync();
    if (busy) hydrate();
    return true;
  }
  function clear() {
    if (request) request.abort();
    request = null;
    busy = false;
    ids = [];
    message = "";
    failed = false;
    save();
    sync();
  }
  async function hydrate() {
    if (request) request.abort();
    request = null;
    if (!ids.length) {
      busy = false;
      sync();
      return;
    }
    const controller = new AbortController();
    request = controller;
    const requested = [...ids];
    busy = true;
    message = "";
    failed = false;
    sync();
    const timeout = setTimeout(() => controller.abort(), 8000);
    try {
      const response = await fetch(
        base + "/api/compare?ids=" + requested.join(","),
        {
          signal: controller.signal,
          headers: { Accept: "application/json" },
          cache: "no-store",
        },
      );
      if (!response.ok) throw new Error("comparison unavailable");
      const payload = await response.json();
      if (!Array.isArray(payload.products)) throw new Error("invalid response");
      if (request !== controller) return;
      const fresh = payload.products.map(normalize).filter(Boolean);
      fresh.forEach((product) => registry.set(product.id, product));
      const valid = new Set(fresh.map((product) => product.id));
      ids = ids.filter((id) => valid.has(id));
      if (ids.length !== requested.length)
        message = "Đã bỏ PC không còn được hiển thị trong cửa hàng.";
      save();
    } catch (_) {
      if (request !== controller) return;
      failed = true;
      message =
        "Không thể cập nhật thông số. Kiểm tra kết nối và nhấn Thử tải lại; thông tin đang có có thể chưa mới nhất.";
    } finally {
      clearTimeout(timeout);
      if (request === controller) {
        busy = false;
        request = null;
        sync();
      }
    }
  }
  function open(trigger) {
    if (get().items.length < 2) return;
    opener = trigger || document.activeElement;
    renderTable();
    dialog.showModal();
    dialog.querySelector("[data-compare-close]").focus();
    hydrate();
  }
  window.PCCompare = {
    get,
    add,
    remove: removeId,
    clear,
    open,
    register(values) {
      values.forEach((value) => {
        const product = normalize(value);
        if (product) registry.set(product.id, product);
      });
      sync();
    },
  };
  ids = read();
  document.querySelectorAll("[data-pc-compare]").forEach((button) => {
    let value;
    try {
      value = JSON.parse(button.dataset.pcCompare);
    } catch (_) {
      return;
    }
    const product = normalize(value);
    if (!product) return;
    registry.set(product.id, product);
    button.addEventListener("click", () =>
      ids.includes(product.id) ? removeId(product.id) : add(product),
    );
  });
  document
    .querySelectorAll("[data-compare-open]")
    .forEach((button) => button.addEventListener("click", () => open(button)));
  tray.querySelector("[data-compare-clear]").addEventListener("click", clear);
  tray.querySelector("[data-compare-retry]").addEventListener("click", hydrate);
  dialog
    .querySelector("[data-compare-close]")
    .addEventListener("click", () => dialog.close());
  dialog
    .querySelector("[data-compare-differences]")
    .addEventListener("change", renderTable);
  dialog.addEventListener("close", () => {
    const target =
      opener && opener.isConnected && !opener.disabled
        ? opener
        : document.querySelector("[data-pc-compare]");
    if (target) target.focus();
  });
  window.addEventListener("storage", (event) => {
    if (event.key !== key && event.key !== null) return;
    ids = read();
    sync();
    hydrate();
  });
  sync();
  if (ids.length) hydrate();
})();
