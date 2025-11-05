// Include this before </body> or as a separate file.
// Overlay opens when .view-btn clicked. No "×" button — close via backdrop click or Escape.

document.addEventListener('DOMContentLoaded', function () {
  const overlay = document.getElementById('details-overlay');
  if (!overlay) return;

  const longDescEl = overlay.querySelector('#overlay-long-desc');

  function openOverlay() {
    overlay.classList.add('active');
    overlay.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }
  function closeOverlay() {
    overlay.classList.remove('active');
    overlay.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  // Close by clicking backdrop (outside .overlay-content)
  overlay.addEventListener('click', function (e) {
    if (e.target === overlay) closeOverlay();
  });

  // Close with Escape key
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && overlay.classList.contains('active')) closeOverlay();
  });

  // Populate and open overlay when any .view-btn is clicked
  document.querySelectorAll('.view-btn').forEach(function (btn) {
    btn.addEventListener('click', function (ev) {
      const tr = ev.currentTarget.closest('tr');
      if (!tr) return;

      // Recommended: set these data-attributes on the <tr> server-side:
      // data-order-id, data-customer, data-date, data-product, data-qty, data-price, data-total, data-status, data-ordered-by, data-description, data-img
      const getData = (key, fallbackIndex) => tr.dataset[key] ?? (tr.cells && tr.cells[fallbackIndex] ? tr.cells[fallbackIndex].textContent.trim() : '');

      const orderId = getData('orderId', 0);
      const customer = getData('customer', 1);
      const date = getData('date', 2);
      const product = getData('product', 3);
      const qty = getData('qty', 4);
      const price = getData('price', 5);
      const total = getData('total', 6);
      const status = getData('status', 7);
      const orderedBy = getData('orderedBy', null);
      const description = tr.dataset.description ?? btn.dataset.description ?? '';

      // fill overlay fields safely
      const setText = (id, value) => {
        const el = overlay.querySelector('#' + id);
        if (el) el.textContent = value ?? '';
      };

      setText('overlay-order-id', orderId || '#ORD-xxxx');
      setText('overlay-customer', customer || '—');
      setText('overlay-date', date || '—');
      setText('overlay-product-name', product || '—');
      setText('overlay-qty', qty || '0');
      setText('overlay-price', price || '');
      setText('overlay-total', total || '');
      setText('overlay-status', status || '—');
      setText('overlay-ordered-by', orderedBy || '');

      // heading and image
      const heading = overlay.querySelector('#overlay-product');
      if (heading) heading.textContent = product || 'Product';

      const img = overlay.querySelector('#overlay-image');
      const imgSrc = tr.dataset.img ?? btn.dataset.img;
      if (img && imgSrc) img.src = imgSrc;

      // description: insert as plain text (textContent), preserve newlines with CSS pre-wrap
      if (longDescEl) {
        longDescEl.textContent = description.trim() || '—';
      }

      openOverlay();
    });
  });
});



// Include this before </body> or as a separate JS file.
// Handles: view-btn, confirm-btn, cancel-btn using event delegation.
// Replaces action buttons with a single "View Details" after action.

document.addEventListener('DOMContentLoaded', function () {
  const table = document.querySelector('.orders-table'); // container for delegation
  const overlay = document.getElementById('details-overlay');
  const longDescEl = overlay?.querySelector('#overlay-long-desc');
  const overlayImage = overlay?.querySelector('#overlay-image');
  const setOverlayText = (id, value) => {
    const el = overlay?.querySelector('#' + id);
    if (el) el.textContent = value ?? '';
  };
  function openOverlay() {
    if (!overlay) return;
    overlay.classList.add('active');
    overlay.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }
  function closeOverlay() {
    if (!overlay) return;
    overlay.classList.remove('active');
    overlay.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }
  // Close overlay via backdrop or Escape
  if (overlay) {
    overlay.addEventListener('click', function (e) { if (e.target === overlay) closeOverlay(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && overlay.classList.contains('active')) closeOverlay(); });
  }

  // Helper: normalize status tag (set text and classes)
  function updateStatusTag(tr, newStatus) {
    const statusSpan = tr.querySelector('.status-tag');
    if (!statusSpan) return;
    // remove known status classes
    statusSpan.classList.remove('pending','completed','cancelled');
    // set class + text
    if (newStatus === 'completed') {
      statusSpan.classList.add('completed');
      statusSpan.textContent = 'Completed';
    } else if (newStatus === 'cancelled') {
      statusSpan.classList.add('cancelled');
      statusSpan.textContent = 'Cancelled';
    } else {
      statusSpan.classList.add('pending');
      statusSpan.textContent = 'Pending';
    }
    // update dataset on <tr>
    tr.dataset.status = newStatus;
  }

  // Helper: replace actions cell with single View Details button
  function replaceActionsWithView(tr) {
    // Assuming the last td is actions; find the last td
    const tds = tr.querySelectorAll('td');
    if (!tds || tds.length === 0) return;
    const actionCell = tds[tds.length - 1];
    actionCell.innerHTML = '<button class="action-btn view-btn">View Details</button>';
    // no need to manually bind event; delegation handles .view-btn clicks
  }

  // Handler: confirm click
  function handleConfirm(btn) {
    const tr = btn.closest('tr');
    if (!tr) return;
    // Immediately mark as completed and update UI
    updateStatusTag(tr, 'completed');
    replaceActionsWithView(tr);
    // Optional: show a small flash or toast (not implemented)
  }

  // Handler: cancel click (asks for confirmation)
  function handleCancel(btn) {
    const tr = btn.closest('tr');
    if (!tr) return;
    const orderId = tr.dataset.orderId || tr.cells?.[0]?.textContent?.trim() || '';
    const confirmMsg = orderId ? `Cancel ${orderId}? Are you sure you want to cancel this order?` : 'Are you sure you want to cancel this order?';
    if (!window.confirm(confirmMsg)) return; // user aborted
    updateStatusTag(tr, 'cancelled');
    replaceActionsWithView(tr);
    // Optional: if you need to notify server, do an AJAX call here to persist change
  }

  // Handler: view click (open overlay and populate fields)
  function handleView(btn) {
    const tr = btn.closest('tr');
    if (!tr || !overlay) return;
    // read attributes preferring data-* attributes (recommended)
    const getData = (key, fallbackIndex) => tr.dataset[key] ?? (tr.cells && tr.cells[fallbackIndex] ? tr.cells[fallbackIndex].textContent.trim() : '');
    const orderId = getData('orderId', 0);
    const customer = getData('customer', 1);
    const date = getData('date', 2);
    const product = getData('product', 3);
    const qty = getData('qty', 4);
    const price = getData('price', 5);
    const total = getData('total', 6);
    const status = getData('status', 7);
    const orderedBy = getData('orderedBy', null);
    const description = tr.dataset.description ?? '';

    // fill overlay fields safely (textContent)
    setOverlayText('overlay-order-id', orderId || '#ORD-xxxx');
    setOverlayText('overlay-customer', customer || '—');
    setOverlayText('overlay-date', date || '—');
    setOverlayText('overlay-product-name', product || '—');
    setOverlayText('overlay-qty', qty || '0');
    setOverlayText('overlay-price', price || '');
    setOverlayText('overlay-total', total || '');
    setOverlayText('overlay-status', status ? capitalize(status) : '—');
    setOverlayText('overlay-ordered-by', orderedBy || '');
    // heading
    if (overlay.querySelector('#overlay-product')) overlay.querySelector('#overlay-product').textContent = product || 'Product';
    // image (optional)
    const imgSrc = tr.dataset.img ?? '';
    if (overlayImage && imgSrc) overlayImage.src = imgSrc;
    // description: keep as plain text and preserve newlines using CSS white-space: pre-wrap
    if (longDescEl) longDescEl.textContent = description.trim() || '—';
    openOverlay();
  }

  // Utility: capitalize first letter
  function capitalize(s) { return (s && typeof s === 'string') ? s.charAt(0).toUpperCase() + s.slice(1) : s; }

  // Delegated click listener for table area (handles view, confirm, cancel)
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('button');
    if (!btn) return;
    if (btn.matches('.confirm-btn')) {
      e.preventDefault();
      handleConfirm(btn);
      return;
    }
    if (btn.matches('.cancel-btn')) {
      e.preventDefault();
      handleCancel(btn);
      return;
    }
    if (btn.matches('.view-btn')) {
      e.preventDefault();
      handleView(btn);
      return;
    }
  });

  // Optional: If you want to persist changes to the server, replace updateStatusTag/replaceActionsWithView calls
  // with fetch()/XHR to your API endpoints, then only update the UI after server responds successfully.
});