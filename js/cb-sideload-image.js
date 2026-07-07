/**
 * Block editor integration: adds a "Download to Media Library" button
 * to core/image blocks that reference an external URL.
 *
 * @package cb-idtravel2026
 */
(function () {
  "use strict";

  function isExternalUrl(url) {
    if (!url) return false;
    try {
      var siteOrigin = window.location.origin;
      var urlOrigin = new URL(url).origin;
      return urlOrigin !== siteOrigin;
    } catch (_) {
      return false;
    }
  }

  function addDownloadButton(block) {
    var inspectorControls =
      block && block.innerBlock && block.innerBlock.length > 0
        ? block.innerBlock[0].props
        : null;

    // Fallback: scan the block's DOM for the inspector region.
    var container =
      document.querySelector(".block-editor-block-inspector") ||
      document.querySelector('[data-type="core/image"] .block-editor');

    if (!container) return;

    // Prevent duplicate buttons.
    if (container.querySelector(".cb-sideload-btn")) return;

    var img = block.attributes.url;

    if (!isExternalUrl(img)) return;
    if (block.attributes.id && block.attributes.id > 0) return;

    var btn = document.createElement("button");
    btn.type = "button";
    btn.className =
      "components-button components-is-secondary cb-sideload-btn components-with-icon";
    btn.style.cssText = "margin-top:12px;width:100%;justify-content:center;";
    btn.textContent = "Download to Media Library";

    btn.addEventListener("click", function () {
      var postId = document.querySelector("#post_ID")
        ? parseInt(document.querySelector("#post_ID").value, 10)
        : 0;

      btn.disabled = true;
      btn.textContent = "Downloading…";

      wp.apiFetch({
        path: "/cb-utility/v1/sideload-image",
        method: "POST",
        data: {
          url: img,
          post_id: postId,
        },
      })
        .then(function (result) {
          if (result && result.id) {
            var newBlock = Object.assign({}, block, {
              attributes: Object.assign({}, block.attributes, {
                id: result.id,
                url: result.url,
                alt: result.alt || block.attributes.alt || "",
              }),
            });

            wp.data
              .dispatch("core/block-editor")
              .replaceBlock(block.clientId, newBlock);
          }
        })
        .catch(function (err) {
          btn.disabled = false;
          btn.textContent = "Download failed — retry";
          console.error("[cb-sideload-image]", err);
        });
    });

    container.appendChild(btn);
  }

  function observeSelectedBlock() {
    if (typeof wp.data.subscribe !== "function") return;

    var lastClientId = null;

    wp.data.subscribe(function () {
      var selectedBlock;

      try {
        selectedBlock = wp.data.select("core/block-editor").getSelectedBlock();
      } catch (_) {
        return;
      }

      if (!selectedBlock) return;

      if (selectedBlock.clientId === lastClientId) return;
      lastClientId = selectedBlock.clientId;

      if (selectedBlock.name !== "core/image") return;

      var btn = document.querySelector(".cb-sideload-btn");
      if (btn) btn.remove();

      addDownloadButton(selectedBlock);
    });
  }

  function init() {
    // Ensure the block editor has fully loaded.
    if (document.querySelector(".block-editor")) {
      observeSelectedBlock();
    } else {
      document.addEventListener("DOMContentLoaded", observeSelectedBlock);
    }
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
