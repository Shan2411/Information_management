
        // Function to set active link based on current URL
        function setActiveLink() {
            const links = document.querySelectorAll('.sidebar-links li a');
            const currentPath = window.location.pathname.split('/').pop();  // Get the current file name (e.g., 'dashboard.html')
            
            links.forEach(link => {
                const linkHref = link.getAttribute('href');
                if (linkHref === currentPath) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
        }
        // Run on page load
        document.addEventListener('DOMContentLoaded', setActiveLink);
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('sidebarToggle');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('open');
            });
        }

        // Product Card Clicks
        document.querySelectorAll('.product-card').forEach(card => {
            card.addEventListener('click', (e) => {
                if (!e.target.closest('.product-actions')) {
                    const id = card.dataset.productId;
                    openProductModal(id);
                }
            });
        });



        // Overlay functions
        function showOverlay() {
            document.getElementById('product-overlay').classList.add('active');
        }

        function closeOverlay() {
            document.getElementById('product-overlay').classList.remove('active');
        }

        // Close overlay when clicking outside
        document.getElementById('product-overlay').addEventListener('click', function(e) {
            if (e.target === this) {
                closeOverlay();
            }
        });




          // Example: Show overlay on floating button click
        document.querySelector('.floating-add-btn').addEventListener('click', () => {
            document.querySelector('.adding-products-overlay').classList.add('active');
        });
        // Example: Hide overlay on outside click (optional)
        document.querySelector('.adding-products-overlay').addEventListener('click', (e) => {
            if (e.target === e.currentTarget) {
                e.currentTarget.classList.remove('active');
            }
        });



        // --- Add product overlay behavior helpers ---
        const addOverlayEl = document.querySelector('.adding-products-overlay');
        const openOverlayBtn = document.getElementById('openOverlay') || document.querySelector('.floating-add-btn');
        const addForm = document.getElementById('addProductForm');
        const firstInput = document.getElementById('product-name');
        const clearBtn = document.getElementById('clearForm');
        const cancelBtn = document.getElementById('cancelAdd');

        // Open overlay + focus first field
        if (openOverlayBtn) {
            openOverlayBtn.addEventListener('click', () => {
                addOverlayEl.classList.add('active');
                // small timeout to wait for CSS animation/display changes, then focus
                setTimeout(() => {
                    if (firstInput) firstInput.focus();
                }, 80);
            });
        }

        // Also handle any other logic that toggles the overlay (defensive)
        const observer = new MutationObserver((mutations) => {
            mutations.forEach(m => {
                if (m.attributeName === 'class') {
                    if (addOverlayEl.classList.contains('active')) {
                        if (firstInput) firstInput.focus();
                    }
                }
            });
        });
        if (addOverlayEl) observer.observe(addOverlayEl, { attributes: true });

        // Clear form and focus first field
        if (clearBtn && addForm) {
            clearBtn.addEventListener('click', () => {
                addForm.reset();
                // if you have custom UI updates (e.g., previews) clear them here
                if (firstInput) firstInput.focus();
            });
        }

        // Cancel button: close overlay
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                addOverlayEl.classList.remove('active');
            });
        }

        // --- Edit product overlay behavior ---
        const editOverlay = document.getElementById('editProductOverlay');
        const editForm = document.getElementById('editProductForm');
        const editClear = document.getElementById('clearEditForm');
        const editCancel = document.getElementById('cancelEdit');

        // Open when "Edit" button clicked
        document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', e => {
            e.stopPropagation(); // prevent card click from triggering product overlay
            editOverlay.classList.add('active');
        });
        });

        // Close when clicking outside
        editOverlay.addEventListener('click', e => {
        if (e.target === editOverlay) editOverlay.classList.remove('active');
        });

        // Clear form
        editClear.addEventListener('click', () => {
        editForm.reset();
        document.getElementById('edit-product-name').focus();
        });

        // Cancel button
        editCancel.addEventListener('click', () => {
        editOverlay.classList.remove('active');
        });












        // CONFIRMATION FOR ADDING,EDITING,ARCHIVING
        addForm.addEventListener('submit', e => {
        e.preventDefault();
        if (confirm("Are you sure you want to add this product?")) {
            // do actual submission (AJAX or PHP)
            alert("Product added successfully!");
            addOverlayEl.classList.remove('active');
            addForm.reset();
        }
        });

        editForm.addEventListener('submit', e => {
        e.preventDefault();
        if (confirm("Save changes to this product?")) {
            alert("Product updated!");
            editOverlay.classList.remove('active');
        }
        });

        document.querySelectorAll('.archive-btn').forEach(btn => {
        btn.addEventListener('click', e => {
            e.stopPropagation();
            if (confirm("Are you sure you want to archive this product?")) {
                alert("Product archived successfully!");
            }
        });
        });

