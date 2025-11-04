document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("searchArchived");
    const cards = document.querySelectorAll(".archived-card");
    const emptyMsg = document.getElementById("emptyArchived");
    const archivedCount = document.getElementById("archivedCount");

    // Search filter
    searchInput.addEventListener("input", () => {
        const query = searchInput.value.toLowerCase();
        let visible = 0;

        cards.forEach(card => {
            const name = card.querySelector("h3").textContent.toLowerCase();
            card.style.display = name.includes(query) ? "flex" : "none";
            if (name.includes(query)) visible++;
        });

        emptyMsg.style.display = visible === 0 ? "block" : "none";
    });

 

    // Count total
    archivedCount.textContent = cards.length;
});


   // === UNIVERSAL RESTORE & DELETE CONFIRMATION ===

// Helper function for confirmation dialogs
function handleAction(selector, message, successMsg) {
    document.querySelectorAll(selector).forEach(btn => {
        btn.addEventListener("click", e => {
            e.preventDefault(); // prevent accidental form submission
            if (confirm(message)) {
                alert(successMsg);
                // TODO: Add backend call or overlay close logic here
            }
        });
    });
}

// Archived overlay
handleAction(".archived-restore-btn", "Restore this product?", "Product restored successfully!");
handleAction(".archived-delete-btn", "Permanently delete this product? This action cannot be undone.", "Product deleted permanently!");

// Product Details overlay
handleAction(".details-restore-btn", "Restore this product?", "Product restored successfully!");
handleAction(".details-delete-btn", "Permanently delete this product? This action cannot be undone.", "Product deleted permanently!");






 // Function to show the product details overlay
function showProductOverlay() {
    const overlay = document.getElementById('product-overlay');
    if (overlay) {
        overlay.classList.add('active');
        console.log('Overlay shown'); // Debug log
    } else {
        console.error('Overlay element not found!'); // Debug log
    }
}

// Function to close the product details overlay
function closeProductOverlay() {
    const overlay = document.getElementById('product-overlay');
    if (overlay) {
        overlay.classList.remove('active');
        console.log('Overlay closed'); // Debug log
    }
}

// Close overlay when clicking outside the content
document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.getElementById('product-overlay');
    
    if (overlay) {
        overlay.addEventListener('click', function(e) {
            // Only close if clicking the backdrop, not the content
            if (e.target === this) {
                closeProductOverlay();
            }
        });
    }
    
    // Add click handlers to all product cards
    const productCards = document.querySelectorAll('.archived-card, .product-card');
    productCards.forEach(card => {
        card.addEventListener('click', function(e) {
            // Don't trigger if clicking on action buttons
            if (!e.target.closest('.archived-actions') && 
                !e.target.closest('.product-actions')) {
                showProductOverlay();
            }
        });
    });
});











document.addEventListener('DOMContentLoaded', () => {
  // === Restore Button Confirmation ===
  document.querySelectorAll('.restore-btn').forEach(btn => {
    btn.addEventListener('click', e => {
      e.preventDefault();
      if (confirm('Restore this product?')) {
        alert('Product restored successfully!');
        // Example: Close overlay if needed
        document.getElementById('product-overlay').classList.remove('active');
      }
    });
  });

  // === Delete Button Confirmation ===
  document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', e => {
      e.preventDefault();
      if (confirm('Are you sure you want to permanently delete this product?')) {
        alert('Product deleted successfully!');
        // Example: Close overlay if needed
        document.getElementById('product-overlay').classList.remove('active');
      }
    });
  });
});

