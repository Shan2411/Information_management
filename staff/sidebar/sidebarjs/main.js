// ===== UNIVERSAL SIDEBAR ACTIVE LINK HIGHLIGHTER =====
document.addEventListener('DOMContentLoaded', function () {
    const links = document.querySelectorAll('.sidebar-links li a');
    const currentPath = window.location.pathname;

    links.forEach(link => {
        const linkHref = link.getAttribute('href');

        // Handle 3 cases:
        // 1. Absolute or relative paths that match the current URL
        // 2. Subdirectories (using .includes)
        // 3. '#' links (highlighted when clicked)

        if (
            linkHref !== '#' &&
            (currentPath.endsWith(linkHref) ||
             currentPath.includes(linkHref))
        ) {
            link.classList.add('active');
        }

        // Add click listener for '#' or other local links
        link.addEventListener('click', function () {
            // remove active from all
            links.forEach(l => l.classList.remove('active'));
            // add to clicked one
            this.classList.add('active');
        });
    });
});


// Logout confirmation
document.getElementById('logoutLink').addEventListener('click', (e) => {
  e.preventDefault();
  if (confirm("Are you sure you want to log out?")) {
    // Redirect to login or logout PHP
    window.location.href = "logout.php"; // change path if needed
  }
});

