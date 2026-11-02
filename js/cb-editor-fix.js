(function () {
  if (typeof acf === "undefined") {
    return;
  }

  function stripCloneEditorIds() {
    const cloneEditors = document.querySelectorAll(
      ".acf-row.-clone .acf-editor-wrap textarea",
    );

    cloneEditors.forEach(function (textarea) {
      textarea.removeAttribute("id");
    });
  }

  acf.addAction("prepare", stripCloneEditorIds);
  acf.addAction("append", stripCloneEditorIds);

  /**
   * Stops ACF's TinyMCE (re)initialisation stealing focus from whatever the
   * editor was typing in.
   *
   * In the block editor, inserting/moving a block makes ACF unmount and then
   * remount (destroy + re-create) the wysiwyg fields of the blocks after it.
   * Each re-created TinyMCE iframe ends up with document focus when it
   * finishes initialising, so the caret jumps from the paragraph being typed
   * into (e.g. after a CB Case Study Hero) into the first rich-text field of
   * a later CB Content Grid, and the typed text lands there. Only happens when
   * at least one TinyMCE editor is on the page.
   *
   * Fix: remember what had focus when each ACF editor is added; if that editor
   * then takes focus within a short window without any user input inside it,
   * hand focus (and caret) back. A real click/keypress inside the editor
   * always wins.
   */
  var STEAL_WINDOW_MS = 2500;

  function isInsideAcfEditor(el) {
    return !!(
      el &&
      el.closest &&
      (el.closest(".acf-editor-wrap") || el.closest(".mce-tinymce"))
    );
  }

  // The element that really has focus, looking through iframes (the block
  // editor canvas may be one) but not into ACF's own TinyMCE iframes.
  function focusSnapshot() {
    var el = document.activeElement;
    var win = window;

    try {
      while (
        el &&
        el.tagName === "IFRAME" &&
        !isInsideAcfEditor(el) &&
        el.contentDocument
      ) {
        win = el.contentWindow;
        el = el.contentDocument.activeElement;
      }
    } catch (e) {
      return null;
    }

    if (
      !el ||
      el === document.body ||
      el.tagName === "BODY" ||
      el.tagName === "IFRAME" ||
      isInsideAcfEditor(el)
    ) {
      return null;
    }

    var range = null;
    try {
      var sel = win.getSelection();
      if (sel && sel.rangeCount) {
        range = sel.getRangeAt(0).cloneRange();
      }
    } catch (e) {}

    return { win: win, el: el, range: range };
  }

  function restoreFocus(snap) {
    if (!snap || !snap.el.isConnected) {
      return;
    }

    try {
      snap.el.focus({ preventScroll: true });
      if (snap.range) {
        var sel = snap.win.getSelection();
        sel.removeAllRanges();
        sel.addRange(snap.range);
      }
    } catch (e) {}
  }

  function guardEditor(ed) {
    if (!/^acf-editor-/.test(ed.id) || ed.__cbFocusGuard) {
      return;
    }
    ed.__cbFocusGuard = true;

    var snap = focusSnapshot();
    var addedAt = Date.now();
    var touched = false;

    if (!snap) {
      return;
    }

    ed.on("mousedown touchstart keydown", function () {
      touched = true;
    });

    ed.on("focus", function () {
      if (touched || Date.now() - addedAt > STEAL_WINDOW_MS) {
        return;
      }
      // Focus is only "stolen" if something else in the page had it.
      restoreFocus(snap);
    });
  }

  function installFocusGuard() {
    if (
      window.__cbMceFocusGuard ||
      typeof tinymce === "undefined" ||
      typeof tinymce.on !== "function"
    ) {
      return;
    }
    window.__cbMceFocusGuard = true;

    tinymce.on("AddEditor", function (ev) {
      guardEditor(ev.editor);
    });
  }

  acf.addAction("prepare", installFocusGuard);
  acf.addAction("ready", installFocusGuard);
  installFocusGuard();
})();
