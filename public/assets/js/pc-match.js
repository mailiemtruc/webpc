/* Budget discovery delegates all selection/comparison state to PCCompare. */
(function () {
  "use strict";
  const root = document.getElementById("pc-match");
  const owner = window.PCCompare;
  if (!root || !owner) return;
  const data = JSON.parse(document.getElementById("pc-match-data").textContent);
  owner.register(data);
  const money = (value) => new Intl.NumberFormat("vi-VN").format(value) + " ₫";
  const budget = root.querySelector("#match-budget"),
    results = root.querySelector("[data-match-results]"),
    count = root.querySelector("[data-match-count]");
  function node(tag, text, className) {
    const el = document.createElement(tag);
    if (text !== undefined) el.textContent = text;
    if (className) el.className = className;
    return el;
  }
  function sync() {
    const state = owner.get();
    root.querySelector("[data-match-comparison]").hidden =
      state.ids.length === 0;
    root.querySelector("[data-match-selection-note]").textContent =
      "Đã chọn " +
      state.ids.length +
      "/4 PC. Lựa chọn được giữ khi chuyển trang.";
    root.querySelector("[data-compare-open]").disabled = state.items.length < 2;
    root.querySelectorAll("[data-match-checkbox]").forEach((input) => {
      input.checked = state.ids.includes(Number(input.value));
      input.disabled = state.ids.length >= 4 && !input.checked;
      input
        .closest(".match-result")
        .classList.toggle("is-selected", input.checked);
    });
  }
  function render() {
    const cap = Number(budget.value) * 1000000,
      need = Number(root.querySelector('[name="match-need"]:checked').value);
    const matches = data
      .filter((p) => p.price <= cap && (!need || p.category === need))
      .sort((a, b) => a.price - b.price);
    root.querySelector("#match-budget-value").textContent = money(cap);
    budget.setAttribute("aria-valuetext", money(cap));
    count.textContent =
      matches.length + " cấu hình phù hợp · chọn tối đa 4 để so sánh";
    root.querySelector("[data-match-empty]").hidden = matches.length > 0;
    results.replaceChildren();
    matches.forEach((product) => {
      const card = node("article", undefined, "match-result"),
        image = node("img");
      image.src = product.image;
      image.alt = "";
      image.width = 100;
      image.height = 100;
      image.addEventListener("error", () => {
        if (!image.dataset.fallback) {
          image.dataset.fallback = "true";
          image.src = new URL(
            "../images/product-placeholder.svg",
            document.querySelector('script[src*="pc-match.js"]').src,
          ).href;
        }
      });
      const body = node("div", undefined, "match-result-copy"),
        title = node("h4"),
        link = node("a", product.name);
      link.href = product.url;
      title.append(link);
      body.append(
        title,
        node("p", String(product.specs.cpu || "Xem thông số trong chi tiết")),
        node("strong", money(product.price)),
      );
      const label = node("label", undefined, "match-select"),
        input = node("input");
      input.type = "checkbox";
      input.value = product.id;
      input.dataset.matchCheckbox = "";
      input.setAttribute("aria-label", "So sánh " + product.name);
      input.addEventListener("change", () =>
        input.checked ? owner.add(product) : owner.remove(product.id),
      );
      label.append(input, node("span", "So sánh"));
      card.append(image, body, label);
      results.append(card);
    });
    sync();
  }
  budget.addEventListener("input", render);
  root
    .querySelectorAll('[name="match-need"]')
    .forEach((input) => input.addEventListener("change", render));
  root.querySelector("[data-match-reset]").addEventListener("click", () => {
    budget.value = 80;
    root.querySelector('[name="match-need"][value="0"]').checked = true;
    render();
    budget.focus();
  });
  root.querySelector("[data-match-clear]").addEventListener("click", () => {
    owner.clear();
    const first = root.querySelector("[data-match-checkbox]");
    (first || budget).focus();
  });
  window.addEventListener("pccompare:change", sync);
  root.querySelector("[data-pc-match]").hidden = false;
  render();
})();
