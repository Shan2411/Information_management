document.addEventListener('DOMContentLoaded', function () {
  const links = document.querySelectorAll('.sidebar-links li a');
  const currentPath = window.location.pathname;

  links.forEach(link => {
    // Skip links marked to not receive highlight (e.g., logout)
    if (link.dataset.noHighlight !== undefined) {
      // still attach click-handler for anchor behavior if needed, but skip highlight logic
      link.addEventListener('click', function () {
        // do nothing related to active class
      });
      return;
    }

    const linkHref = link.getAttribute('href');

    if (
      linkHref !== '#' &&
      (currentPath.endsWith(linkHref) ||
       currentPath.includes(linkHref))
    ) {
      link.classList.add('active');
    }

    link.addEventListener('click', function () {
      // remove active from all (only those not marked noHighlight)
      links.forEach(l => {
        if (l.dataset.noHighlight === undefined) l.classList.remove('active');
      });

      this.classList.add('active');
    });
  });
});


document.getElementById('logoutLink').addEventListener('click', (e) => {
  e.preventDefault();
  if (confirm("Are you sure you want to log out?")) {
    // proceed to logout
    window.location.href = e.currentTarget.getAttribute('href') || 'logout.php';
  } else {
    // user cancelled — nothing to restore since link is ignored by highlighter
  }
});