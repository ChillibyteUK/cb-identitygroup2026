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
})();
