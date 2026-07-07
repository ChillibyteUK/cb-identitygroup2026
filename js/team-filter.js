(() => {
  const $filter = document.getElementById('team-filter');
  if (!$filter) return;

  const deptSel = document.getElementById('team-dept');
  const qInput = document.getElementById('team-q');
  const resetBtn = document.getElementById('team-filter-reset');
  const status = document.getElementById('team-filter-status');

  const debounce = (fn, ms) => {
    let t;
    return (...args) => {
      clearTimeout(t);
      t = setTimeout(() => fn(...args), ms);
    };
  };

  const readCards = () => {
    const nodes = Array.from(document.querySelectorAll('.hub-team__card'));
    return nodes.map((el) => ({
      el,
      depts: (el.getAttribute('data-dept') || '').split(/\s+/).filter(Boolean),
      name: (el.getAttribute('data-name') || '').toLowerCase(),
      parent: el.closest('.hub-team')
    }));
  };

  const cards = readCards();

  const applyFilter = () => {
    const dept = (deptSel && deptSel.value) || '';
    const q = (qInput && qInput.value.toLowerCase()) || '';

    let shown = 0;
    const blocks = new Map();

    cards.forEach(({ el, depts, name, parent }) => {
      const matchDept = !dept || depts.includes(dept);
      const matchName = !q || name.includes(q);
      const visible = matchDept && matchName;
      el.style.display = visible ? '' : 'none';
      if (visible) {
        shown++;
      }
      if (parent) {
        const count = blocks.get(parent) || { total: 0, visible: 0 };
        count.total++;
        if (visible) count.visible++;
        blocks.set(parent, count);
      }
    });

    // Toggle empty state per block
    blocks.forEach((count, parent) => {
      const heading = parent.querySelector('.hub-team__heading');
      if (count.visible === 0) {
        parent.classList.add('is-empty');
        if (heading) heading.style.display = 'none';
      } else {
        parent.classList.remove('is-empty');
        if (heading) heading.style.display = '';
      }
    });

    if (status) {
      status.textContent = `${shown} team member${shown === 1 ? '' : 's'} shown`;
    }

    // Sync URL
    const params = new URLSearchParams(window.location.search);
    if (dept) params.set('dept', dept); else params.delete('dept');
    if (q) params.set('q', q); else params.delete('q');
    const url = `${window.location.pathname}${params.toString() ? '?' + params.toString() : ''}`;
    history.replaceState(null, '', url);
  };

  const initFromURL = () => {
    const params = new URLSearchParams(window.location.search);
    const dept = params.get('dept') || '';
    const q = params.get('q') || '';
    if (deptSel) deptSel.value = dept;
    if (qInput) qInput.value = q;
    applyFilter();
  };

  if (deptSel) deptSel.addEventListener('change', applyFilter);
  if (qInput) qInput.addEventListener('input', debounce(applyFilter, 200));
  if (resetBtn) {
    resetBtn.addEventListener('click', () => {
      if (deptSel) deptSel.value = '';
      if (qInput) qInput.value = '';
      applyFilter();
    });
  }

  initFromURL();
})();
